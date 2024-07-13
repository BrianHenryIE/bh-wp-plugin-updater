<?php
/**
 * @see wp_update_plugins()
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * @phpstan-type Plugin_Update_Array array{}
 * @phpstan-type Plugin_Data_Array array{}
 */
class WordPress_Updater {
	use LoggerAwareTrait;

	/**
	 * Generally we will not refresh plugin update information synchronously, but when the update_plugins transient is
	 * deleted, we infer that to mean the site admin wants to force a check for updates.
	 */
	protected bool $force_refresh = false;

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api
	 * @param Settings_Interface $settings
	 * @param LoggerInterface    $logger A PSR-3 logger.
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );
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
	 * @param string         $transient Always 'update_plugins'.
	 *
	 * @return false|stdClass Always the unchanged input value.
	 */
	public function detect_force_update( $value, string $transient_name ) {

		// Probably only happens on a fresh installation of WordPress.
		if ( false === $value ) {
			return $value;
		}

		// This evaluates to true if the cron job has never run.

		// Do a synchronous refresh if the plugin is not already in the `update_plugins` transient.
		$force_refresh = ! isset( $value->response[ $this->settings->get_plugin_basename() ] )
							&& ! isset( $value->no_update[ $this->settings->get_plugin_basename() ] );

		global $pagenow;
		if ( $force_refresh && is_admin() && 'plugins.php' !== $pagenow ) {
			$force_refresh = false;
			// TODO Schedule imediate update... on shutdown?
		}
		$this->force_refresh = $force_refresh;

		/**
		 * The `pre_set_site_transient_update_plugins` filter gets called twice in {@see wp_update_plugins()}. We don't
		 * need it on the later run.
		 */
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'detect_force_update' ) );

		return $value;
	}

	/**
	 * Add the plugin's update information to the `update_plugins` transient. To be used later on plugins.php.
	 *
	 * @hooked update_plugins_{$hostname}
	 * @see wp-includes/update.php:513
	 * @see wp_update_plugins()
	 *
	 * @param false|Plugin_Update_Array $plugin_update_array Should always be false, but there could be another filter added to `update_plugins_{$hostname}`.
	 * @param Plugin_Data_Array         $plugin_data
	 * @param string                    $plugin_file The plugin basename.
	 * @param array                     $locales
	 *
	 * @return false|Plugin_Update_Array
	 */
	public function add_update_information( $plugin_update_array, $plugin_data, $plugin_file, $locales ) {

		if ( $this->settings->get_plugin_basename() !== $plugin_file ) {
			return $plugin_update_array;
		}

		try {
			/** @var ?Plugin_Update_Interface $plugin_information */
			$plugin_information = $this->api->get_check_update( $this->force_refresh );
		} catch ( \BrianHenryIE\WP_Plugin_Updater\Exception\Licence_Does_Not_Exist_Exception $exception ) {
			$this->logger->debug( 'Licence does not exist no server.' );
			return $plugin_update_array;
		}

		return is_null( $plugin_information )
			? $plugin_update_array
			: $this->convert_to_array( $plugin_information );
	}

	/**
	 * Convert the Plugin_Update_Interface object to an array for use in the `update_plugins` transient.
	 *
	 * Not the most elegant solution, but it's the simplest.
	 *
	 * TODO use serialize / get object vars
	 *
	 * @param Plugin_Update_Interface $plugin_update
	 *
	 * @return Plugin_Update_Array
	 */
	protected function convert_to_array( Plugin_Update_Interface $plugin_update ): array {
		return array(
			'id'           => $plugin_update->get_id(),
			'slug'         => $plugin_update->get_slug(),
			'version'      => $plugin_update->get_version(),
			'url'          => $plugin_update->get_url(),
			'package'      => $plugin_update->get_package(),
			'tested'       => $plugin_update->get_tested(),
			'requires_php' => $plugin_update->get_requires_php(),
			'autoupdate'   => $plugin_update->get_autoupdate(),
			'icons'        => $plugin_update->get_icons(),
			'banners'      => $plugin_update->get_banners(),
			'banners_rtl'  => $plugin_update->get_banners_rtl(),
			'translations' => $plugin_update->get_translations(),
		);
	}
}
