<?php
/**
 * A plain object abstracting settings.
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_Logger\Logger_Settings_Interface;

/**
 * Typed settings.
 */
class Settings implements Settings_Interface, Logger_Settings_Interface {

	public function get_plugin_name(): string {
		return 'BH WP SLSWC Client';
	}

	/**
	 * The current plugin version, as defined in the root plugin file, or a string hopefully in sync with the root file.
	 *
	 * @used-by Admin_Assets::enqueue_scripts()
	 * @used-by Admin_Assets::enqueue_styles()
	 * @used-by Frontend_Assets::enqueue_scripts()
	 * @used-by Frontend_Assets::enqueue_styles()
	 *
	 * @return string
	 */
	public function get_plugin_version(): string {
		return defined( 'BH_WP_SLSWC_CLIENT_VERSION' )
			? BH_WP_SLSWC_CLIENT_VERSION
			: '1.0.0';
	}

	/**
	 * The plugin basename, as defined in the root plugin file, or a string hopefully in sync with the true basename.
	 *
	 * @used-by Admin_Assets::enqueue_scripts()
	 * @used-by Admin_Assets::enqueue_styles()
	 * @used-by Frontend_Assets::enqueue_scripts()
	 * @used-by Frontend_Assets::enqueue_styles()
	 *
	 * @return string
	 */
	public function get_plugin_basename(): string {
		return defined( 'BH_WP_SLSWC_CLIENT_BASENAME' )
			? BH_WP_SLSWC_CLIENT_BASENAME
			: 'bh-wp-slswc-client/bh-wp-slswc-client.php';
	}

	public function get_plugin_slug(): string {
		return 'bh-wp-slswc-client';
	}

	public function get_log_level(): string {
		return get_option( 'bh_wp_slswc_client_log_level', 'notice' );
	}
}
