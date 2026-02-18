<?php
/**
 * When WordPress checks for updates, so does this.
 *
 * @see wp_update_plugins()
 *
 * TODO: Check WP CLI.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use Composer\Semver\Comparator;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * @phpstan-type Plugin_Data_Array array{}
 * @phpstan-import-type Plugin_Update_Array from Plugin_Update
 *
 * @uses \BrianHenryIE\WP_Plugin_Updater\API_Interface::get_check_update()
 * @uses \BrianHenryIE\WP_Plugin_Updater\API_Interface::schedule_immediate_background_update()
 * @uses \BrianHenryIE\WP_Plugin_Updater\Settings_Interface::get_plugin_basename()
 */
class WordPress_Updater {
	use LoggerAwareTrait;

	/**
	 * Generally we will not refresh plugin update information synchronously, but when the update_plugins transient is
	 * deleted, we infer that to mean the site admin wants to force a check for updates.
	 */
	protected bool $force_refresh;

	/**
	 * We determine is the plugin also hosted on WordPress.org. This is used to decide how the update information is
	 * added to the transient.
	 */
	protected bool $is_dot_org_plugin = false;

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api The main updater functions.
	 * @param Settings_Interface $settings The settings provided by the plugin to configure the updater.
	 * @param LoggerInterface    $logger A PSR-3 logger.
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );

		add_filter( 'site_transient_update_plugins', array( $this, 'remove_wp_org_entry_from_transient' ), 10, 2 );
	}

	/**
	 * Remove WordPress.org update information for this plugin from the `update_plugins` transient.
	 *
	 * When a plugin exists on WordPress.org and we want to use this updater (e.g. for beta testing), we
	 * remove the entry from the transient so it is not used.
	 *
	 * @hooked site_transient_update_plugins
	 */
	public function remove_wp_org_entry_from_transient( stdClass|false $value, string $transient_name ): stdClass|false {
		if ( ! $value instanceof stdClass ) {
			return $value;
		}

		if (
			isset( $value->response[ $this->settings->get_plugin_basename() ] )
			&& str_starts_with( $value->response[ $this->settings->get_plugin_basename() ]->url, 'https://wordpress.org/plugins/' )
		) {
			unset( $value->response[ $this->settings->get_plugin_basename() ] );
			$this->is_dot_org_plugin = true;
		} elseif (
			isset( $value->no_update[ $this->settings->get_plugin_basename() ] )
			&& str_starts_with( $value->no_update[ $this->settings->get_plugin_basename() ]->url, 'https://wordpress.org/plugins/' )
		) {
			unset( $value->no_update[ $this->settings->get_plugin_basename() ] );
			$this->is_dot_org_plugin = true;
		}

		return $value;
	}

	/**
	 * Determine should the cached plugin information be used, or should a synchronous request be made.
	 *
	 * When a site admin deleted the `update_plugins` transient, i.e. `wp transient delete update_plugins --network`,
	 * that means they want to force a check for updates. In those cases, i.e. when this plugin does not already have
	 * an entry in the stored value, a synchronous HTTP request will be performed.
	 *
	 * Where the plugin is already in the transient, the value will be updated with the saved information, which
	 * itself is updated with the cron job.
	 *
	 * `wp transient delete update_plugins --network`
	 *
	 * @hooked pre_set_site_transient_update_plugins
	 * @see wp_update_plugins()
	 *
	 * @param false|stdClass $value
	 * @param string         $transient_name Always 'update_plugins'.
	 *
	 * @return false|stdClass Always the unchanged input value.
	 */
	public function on_set_transient_update_plugins( stdClass|false $value, string $transient_name ) {
		// Probably only happens on a fresh installation of WordPress.
		if ( ! $value instanceof stdClass ) {
			return $value;
		}

		if ( ! isset( $this->force_refresh ) ) {
			return $this->detect_force_update( $value );
		} else {
			return $this->add_update_information_to_transient_on_save( $value );
		}
	}

	/**
	 * Infer was the `update_plugins` transient recently deleted.
	 *
	 * Later use the {@see self::$force_refresh} boolean to decide if we use saved information, or make a remote API
	 * call for the update information.
	 *
	 * @param stdClass $value The plugin update information being saved to the `update_plugins` transient.
	 */
	protected function detect_force_update( stdClass $value ): stdClass {

		// Do a synchronous refresh if the plugin is not already in the `update_plugins` transient.
		$force_refresh = ! isset( $value->response[ $this->settings->get_plugin_basename() ] )
							&& ! isset( $value->no_update[ $this->settings->get_plugin_basename() ] );

		/**
		 * If we're in the admin area and haven't got plugin update information, schedule an immediate background job,
		 * to avoid possible timeouts (e.g. a 10 second pause loading plugins.php because the update server is offline).
		 */
		if ( $force_refresh && is_admin() ) {
			$force_refresh = false;
			$this->api->schedule_immediate_background_update();
		}
		$this->force_refresh = $force_refresh;

		return $value;
	}

	/**
	 * @param stdClass $plugin_update_object
	 */
	protected function add_update_information_to_transient_on_save( stdClass $plugin_update_object ): stdClass {

		try {
			/** @var ?Plugin_Update $plugin_information */
			$plugin_information = $this->api->get_check_update( $this->force_refresh );
		} catch ( \BrianHenryIE\WP_Plugin_Updater\Exception\Licence_Does_Not_Exist_Exception ) {
			$this->logger->debug( 'Licence does not exist on server.' );
			return $plugin_update_object;
		}

		if ( is_null( $plugin_information ) ) {
			return $plugin_update_object;
		}

		if ( Comparator::greaterThan(
			$plugin_information->new_version ?? '0.0.0',
			$plugin_update_object->checked[ $this->settings->get_plugin_basename() ] ?? '0.0.0',
		) ) {
			$plugin_update_object->response[ $this->settings->get_plugin_basename() ] = $plugin_information;
		} else {
			$plugin_update_object->no_update[ $this->settings->get_plugin_basename() ] = $plugin_information;
		}

		return $plugin_update_object;
	}

	/**
	 * Add the plugin's update information to the `update_plugins` transient. To be used later on plugins.php.
	 *
	 * This will work when the library is installed in the plugin directory, but will not work if it is installed as
	 * a second plugin, e.g. for beta installs, which would need to modify the cache of `get_plugins()` to set the
	 * update uri of the targeted plugin, ~`wp_cache_set( 'plugins', $cache_plugins, 'plugins' )`.
	 *
	 * @hooked update_plugins_{$hostname}
	 * @see wp-includes/update.php:513
	 * @see wp_update_plugins()
	 *
	 * @param false|Plugin_Update_Array $plugin_update_array Should always be false, but there could be another filter added to `update_plugins_{$hostname}`.
	 * @param Plugin_Data_Array         $plugin_data
	 * @param string                    $plugin_file The plugin basename.
	 * @param string[]                  $locales List of languages in en_US format, {@see get_available_languages()}.
	 *
	 * @return false|Plugin_Update_Array
	 */
	public function add_update_information( false|array $plugin_update_array, array $plugin_data, string $plugin_file, array $locales ): array|false {

		if ( $this->settings->get_plugin_basename() !== $plugin_file ) {
			return $plugin_update_array;
		}

		try {
			/** @var ?Plugin_Update $plugin_information */
			$plugin_information = $this->api->get_check_update( $this->force_refresh );
		} catch ( \BrianHenryIE\WP_Plugin_Updater\Exception\Licence_Does_Not_Exist_Exception ) {
			$this->logger->debug( 'Licence does not exist on server.' );
			return $plugin_update_array;
		}

		return is_null( $plugin_information )
			? $plugin_update_array
			: (array) $plugin_information;
	}
}
