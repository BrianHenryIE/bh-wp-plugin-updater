<?php

namespace BrianHenryIE\WP_Plugin_Updater;

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
	}

	add_action( 'plugins_loaded', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );
}
