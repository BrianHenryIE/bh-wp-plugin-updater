<?php
/**
 * This is necessary to get the View Details modal to work.
 *
 * http://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=woocommerce
 *
 * @see install_plugin_information()
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Admin;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;

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
	 * @see plugins_api()
	 *
	 * @see https://github.com/WordPress/wordpress.org/blob/trunk/wordpress.org/public_html/wp-content/plugins/plugin-directory/api/routes/class-plugin.php
	 *
	 * The `plugins_api` filter will always pass `false` as its value and any non-false return value short-circuits
	 * the normal API request process. The `plugins_api_result` is used for filtering the API response.
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

		$update_information = $this->api->get_plugin_information( false );

		if ( is_null( $update_information ) ) {
			return $res;
		}

		// Check sections have content before adding any.
		$sections = $update_information->get_sections();

		foreach ( $sections as $name => $section ) {
			$res->sections[ $name ] = $section;
		}

		if ( ! isset( $sections['changelog'] ) ) {
			$res->sections['changelog'] = 'No changelog available.';
		}

		// $res->banners // $update_information->get_banners()
		// $res->ratings // $update_information->get_ratings()

		return $res;
	}
}
