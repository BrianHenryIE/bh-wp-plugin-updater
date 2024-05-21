<?php
/**
 *
 *
 * @see install_plugin_information()
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Admin;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;

class Plugins_Page_View_Details {

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
	 * The "View details" link on the plugins.php page is not present for plugins that are not in the WordPress.org repo.
	 *
	 * Add a slug to the plugin details array to add the link.
	 */

	/**
	 * @hooked plugins_api
	 *
	 * $api->sections as $section_name => $content
	 *
	 * @param false|object|array $res The result object or array. Default false.
	 * @param string             $action The type of information being requested from the Plugin Installation API.
	 * @param object             $args Plugin API arguments.
	 */
	public function add_plugin_modal_data( $res, $action, $args ) {

		if ( $this->settings->get_plugin_slug() !== $args->slug ) {
			return $res;
		}

		if ( false === $res ) {
			$res = new \stdClass();
		}

		$minimum = array(
			'slug'     => $this->settings->get_plugin_slug(),
			'name'     => $this->settings->get_plugin_name(),
			'sections' => array(),
		);

		foreach ( $minimum as $key => $value ) {
			if ( ! isset( $res->$key ) ) {
				$res->$key = $value;
			}
		}

		$remote_plugin_information = $this->api->get_product_information();

		// Required to cast as array due to how object is returned from api.
		foreach ( $remote_plugin_information->sections as $name => $section ) {
			$res->sections[ $name ] = $section;
		}
		if ( isset( $remote_plugin_information->banners ) ) {
			$res->banners = (array) $remote_plugin_information->banners;
		}
		if ( isset( $remote_plugin_information->ratings ) ) {
			$res->ratings = (array) $remote_plugin_information->ratings;
		}

		return $res;
	}
}
