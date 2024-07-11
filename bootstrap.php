<?php

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\WP_Logger\Logger;
use Psr\Log\NullLogger;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Avoid loading the plugin update functionality when it is not necessary.
 */
if ( ! ( is_admin() || wp_doing_cron() || wp_is_serving_rest_request() || defined( 'WP_CLI' ) ) ) {
	return;
}

if ( ! function_exists( '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' ) ) {
	/**
	 *
	 *
	 * @hooked plugins_loaded
	 * `remove_action( 'plugins_loaded', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );`
	 */
	function init_plugin_updater(): void {

		Plugin_Updater::get_instance(
			new class() implements Settings_Interface {
				use Settings_Trait;

				/**
				 * The plugin basename. Used to add the Logs link on `plugins.php`.
				 *
				 * @see https://core.trac.wordpress.org/ticket/42670
				 *
				 * @throws \Exception When it cannot be determined. I.e. a symlink inside a symlink.
				 */
				public function get_plugin_basename(): string {
					return 'example-plugin/example-plugin.php';

					// TODO: The following might work but there are known issues around symlinks that need to be tested and handled correctly.
					// @see  https://core.trac.wordpress.org/ticket/42670

					$wp_plugin_basename = plugin_basename( __DIR__ );

					$plugin_filename = get_plugins( explode( '/', $wp_plugin_basename )[0] );

					return array_key_first( $plugin_filename );
				}
			},
			new NullLogger()
		);
	}

	add_action( 'plugins_loaded', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );
}
