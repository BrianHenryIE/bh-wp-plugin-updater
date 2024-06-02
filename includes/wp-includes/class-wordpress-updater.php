<?php
/**
 * @see wp_update_plugins()
 */

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Server\Product;
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
	 * Add the plugin's update information to the transient. To be used later on plugins.php.
	 *
	 * This does not preform any HTTP requests.
	 *
	 * @param false|stdClass{last_checked:int, no_update: array<stdClass>, response: array<stdClass>, translations: array} $value
	 * @param string                                                                                                       $transient Always 'update_plugins'.
	 *
	 * @see wp_plugin_update_row()
	 *
	 * @hooked pre_set_site_transient_update_plugins
	 */
	public function add_product_data_to_wordpress_plugin_information( $value, string $transient ) {

		if ( false === $value ) {
			return $value;
		}

		if ( ! $this->api->is_update_available( false ) ) {
			return $value;
		}

		/** @var ?Product $plugin_information */
		$plugin_information = $this->api->get_product_information( false );

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
		$plugin->package     = $plugin_information->get_update_file_url();
		$plugin->new_version = $plugin_information->get_version();

		$plugin->url = $plugin_information->get_documentation_link();

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
		$value->no_update[ $this->settings->get_plugin_basename() ] = $plugin;

		return $value;
	}
}
