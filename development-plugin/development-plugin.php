<?php
/**
 * Plugin Name:   BH Plugin Updater Development/Test/Demo plugin
 * Description:   A plugin to showcase the library and add test helpers.
 * Version:       1.1.1
 * Author:        BrianHenryIE
 * Author URI:    https://bhwp.ie
 * Update URI:    https://github.com/brianhenryie/bh-wp-aws-ses-bounce-handler
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

// Load all tha magic!

namespace BrianHenryIE\WP_Plugin_Updater\Development_Plugin;

use Alley_Interactive\Autoloader\Autoloader;
use BrianHenryIE\WP_Plugin_Updater\Development_Plugin\Rest\Transients_Controller;
use BrianHenryIE\WP_Plugin_Updater\Development_Plugin\UI\WP_Admin_Bar;
use BrianHenryIE\WP_Plugin_Updater\Plugin_Updater;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Trait;


if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {
	require __DIR__ . '/../vendor/autoload.php';

	// When loading via odd wp-env mappings.
} elseif ( file_exists( __DIR__ . '/../project/vendor/autoload.php' ) ) {
	require __DIR__ . '/../project/vendor/autoload.php';
}

Autoloader::generate(
	__NAMESPACE__,
	__DIR__,
)->register();

// `wp-env` fixes.
( new WP_Env() )->register_hooks();

remove_action( 'init', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );

add_action(
	'rest_api_init',
	array( new Transients_Controller(), 'register_routes' )
);

( new WP_Admin_Bar() )->register_hooks();

Plugin_Updater::get_instance(
	new class() implements Settings_Interface {
		use Settings_Trait;

		/**
		 * The plugin basename of the plugin to be updated.
		 */
		public function get_plugin_basename(): string {
			return 'bh-wp-aws-ses-bounce-handler/bh-wp-aws-ses-bounce-handler.php';
		}

		/**
		 * NB: Heading must be `Update URI` not `UpdateURI`.
		 * This heading is required because WordPress uses it in the filter to fetch update information.
		 *
		 * @see WordPress_Updater::add_update_information()
		 */
		public function get_licence_server_host(): string {
			return 'https://github.com/brianhenryie/bh-wp-aws-ses-bounce-handler';
		}
	}
);

/**
 * Fix: Deprecated: strip_tags(): Passing null to parameter #1 ($string) of type string is deprecated in /.../wp-admin/admin-header.php on line 41
 *
 * @see wordpress/wp-admin/admin-header.php
 * @hooked plugins_loaded
 */
add_action(
	'plugins_loaded',
	function () {
		if (
			/**
			 * Read-only; not production code.
			 *
			 * phpcs:disable WordPress.Security.NonceVerification.Recommended
			 */
			! isset( $_REQUEST['page'] )
			|| ! is_string( $_REQUEST['page'] )
			|| 'development-plugin-logs' !== sanitize_key( wp_unslash( $_REQUEST['page'] ) )
		) {
			return;
		}

		/**
		 * There doesn't seem to be another way to set it, except to have this function be global.
		 *
		 * phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
		 */
		global $title;
		$title = 'Logs page';
	}
);
