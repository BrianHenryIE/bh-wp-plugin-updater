<?php
/**
 * Boot the plugin updater.
 *
 * Does not run on frontend requests.
 *
 * TODO: I don't think this will work when in the vendor directory. `plugin_basename()` will return the wrong value.
 * TODO: The `function_exists()` check will not be namespaced with Strauss.
 *
 * @package brianhenryie/wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use Psr\Log\NullLogger;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Avoid loading the plugin update functionality when it is not necessary.
 */
if ( ! ( is_admin() || wp_doing_cron() || bh_wp_is_rest_request() || defined( 'WP_CLI' ) ) ) {
	return;
}

if ( ! function_exists( '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' ) ) {
	/**
	 *
	 *
	 * @hooked init
	 * `remove_action( 'init', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );`
	 */
	function init_plugin_updater(): void {

		try {
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

						/**
						 * TODO: The following might work but there are known issues around symlinks that need to be tested and handled correctly.
						 *
						 * @see  https://core.trac.wordpress.org/ticket/42670
						 */
						$wp_plugin_basename = plugin_basename( __DIR__ );

						require_once constant( 'ABSPATH' ) . '/wp-admin/includes/plugin.php';

						foreach ( get_plugins() as $plugin_filename => $plugin_data ) {
							if ( str_starts_with( $plugin_filename, $wp_plugin_basename ) ) {
								return $plugin_filename;
							}
						}

						throw new \Exception( 'Could not determine plugin basename.' );
					}
				},
				new NullLogger()
			);
		} catch ( \Exception $e ) {
			// Oh well.
		}
	}

	add_action( 'init', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );
}
