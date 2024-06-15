<?php
/**
 * @see wp_update_plugins()
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Model\Plugin_Update;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use stdClass;

class WordPress_Updater {

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api
	 * @param Settings_Interface $settings
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
	) {
	}

	/**
	 * Add the plugin's update information to the `update_plugins` transient. To be used later on plugins.php.
	 *
	 * When a site admin deleted the `update_plugins` transient, i.e. `wp transient delete update_plugins --network`,
	 * that means they want to force a check for updates. In those cases, i.e. when this plugin does not already have
	 * an entry in the stored value, a synchronous HTTP request will be performed.
	 *
	 * Where the plugin is already in the transient, the value will be updated with the saved information, which
	 * itself is updated with the cron job.
	 *
	 * // TODO: maybe split the synchronous/asynchronous requests into a separate method. see "update_plugins_{$hostname}"
	 *
	 * @param false|stdClass{last_checked:int, no_update: array<stdClass>, response: array<stdClass>, translations: array} $value
	 * @param string                                                                                                       $transient Always 'update_plugins'.
	 *
	 * @see wp_plugin_update_row()
	 *
	 * @hooked pre_set_site_transient_update_plugins
	 */
	public function add_product_data_to_wordpress_plugin_information( $value, string $transient ) {

		// Probably only on a fresh install of WordPress.
		if ( false === $value ) {
			return $value;
		}

		// Do a synchronous refresh if the plugin is not already in the `update_plugins` transient.
		$should_refresh = ! isset( $value->response[ $this->settings->get_plugin_basename() ] )
			&& ! isset( $value->no_update[ $this->settings->get_plugin_basename() ] );

		/** @var ?Plugin_Update $plugin_information */
		$plugin_information = $this->api->get_check_update( $should_refresh );

		if ( is_null( $plugin_information ) ) {
			return $value;
		}

		$plugin =
			$value->response[ $this->settings->get_plugin_basename() ] ??
			$value->no_update[ $this->settings->get_plugin_basename() ] ??
			new stdClass();

		$plugin->id     = $this->settings->get_plugin_basename();
		$plugin->slug   = $this->settings->get_plugin_slug();
		$plugin->plugin = $this->settings->get_plugin_basename();

		/**
		 * If `package` is empty, WordPress will display:
		 * "Automatic update is unavailable for this plugin."
		 */
		$plugin->package     = $plugin_information->get_package();
		$plugin->new_version = $plugin_information->get_version();

		$plugin->url = $plugin_information->get_url();

		// 'id' => 'w.org/plugins/woocommerce',
		// 'slug' => 'woocommerce',
		// 'plugin' => 'woocommerce/woocommerce.php',
		// 'new_version' => '8.3.1',
		// 'url' => 'https://wordpress.org/plugins/woocommerce/',
		// 'package' => 'https://downloads.wordpress.org/plugin/woocommerce.8.3.1.zip',
		// 'icons' =>
		// array (
		// '2x' => 'https://ps.w.org/woocommerce/assets/icon-256x256.gif?rev=2869506',
		// '1x' => 'https://ps.w.org/woocommerce/assets/icon-128x128.gif?rev=2869506',
		// ),
		// 'banners' =>
		// array (
		// '2x' => 'https://ps.w.org/woocommerce/assets/banner-1544x500.png?rev=3000842',
		// '1x' => 'https://ps.w.org/woocommerce/assets/banner-772x250.png?rev=3000842',
		// ),
		// 'banners_rtl' =>
		// array (
		// ),
		// 'requires' => '6.3',

		// foreach( get_plugins()[$this->settings->get_plugin_basename()] as $key => $v ) {
		// if ( ! isset( $plugin->$key ) ) {
		// $plugin->$key = $v;
		// }
		// }

		// TODO: merge with $value.
		if ( $this->api->is_update_available( false ) ) {
			$value->response[ $this->settings->get_plugin_basename() ] = $plugin;
			// unset( $value->no_update[ $this->settings->get_plugin_basename() ] );
		} else {
			$value->no_update[ $this->settings->get_plugin_basename() ] = $plugin;
		}

		return $value;
	}
}
