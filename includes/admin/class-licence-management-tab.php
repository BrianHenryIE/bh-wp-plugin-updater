<?php
/**
 * Add licence UI to the plugins.php "View Details" modal, aka `plugin-information-tabs`.
 *
 * @see install_plugin_information()
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Admin;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;

class Licence_Management_Tab {

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api
	 * @param Settings_Interface $settings
	 */
	public function __construct(
		protected API_Interface $api, // TODO: remove, not in use.
		protected Settings_Interface $settings,
	) {
	}

	/**
	 * Add the `licence` section to the plugin's data.
	 *
	 * TODO: The HTML is just a loading icon. The React JS will render the actual content.
	 *
	 * Add the minimal data required for the modal, if necessary: slug, name.
	 *
	 * @hooked plugins_api ?
	 *
	 * $api->sections as $section_name => $content
	 *  // $update_plugins = get_site_transient( 'update_plugins' );
	 *
	 * @param false|object|array $res The result object or array. Default false.
	 * @param string             $action The type of information being requested from the Plugin Installation API.
	 * @param object             $args Plugin API arguments.
	 */
	public function add_licence_tab( $res, $action, $args ) {

		if ( $this->settings->get_plugin_slug() !== $args->slug ) {
			return $res;
		}

		if ( false === $res ) {
			$res = new \stdClass();
		}

		$res->sections['licence'] = $this->get_licence_tab_html();

		return $res;
	}

	/**
	 * TODO: Print a loading icon in the licence tab while the React JS renders.
	 */
	protected function get_licence_tab_html(): string {
		return '';
	}
}
