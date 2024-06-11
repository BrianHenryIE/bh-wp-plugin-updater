<?php
/**
 * This is necessary to get the View Details modal to work.
 *
 * @see install_plugin_information()
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Admin;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Server\SLSWC\Sections;
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

		$update_information = $this->api->get_check_update( false );

		if ( is_null( $update_information ) ) {
			return $res;
		}

		$sections                      = $update_information->get_sections();
		$res->sections['description']  = $sections->get_description();
		$res->sections['installation'] = $sections->get_installation()();
		$res->sections['changelog']    = $sections->get_changelog();

		// $res->banners // $update_information->get_banners()
		// $res->ratings // $update_information->get_ratings()

		return $res;
	}
}
