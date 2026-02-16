<?php
/**
 * Plugin Name:   Example Plugin
 * Description:   A plugin that does nothing.
 * Version:       1.1.1
 * Author:        BrianHenryIE
 * Author URI:    https://bhwp.ie
 * Update URI:    updatestest.bhwp.ie/wp-json/slswc/v1
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

// Load all tha magic!

// When loading in the local project.
use BrianHenryIE\WP_Plugin_Updater\Plugin_Updater;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Trait;

if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {
	require __DIR__ . '/../vendor/autoload.php';

	// When loading via odd wp-env mappings.
} elseif ( file_exists( __DIR__ . '/../project/vendor/autoload.php' ) ) {
	require __DIR__ . '/../project/vendor/autoload.php';
}

remove_action( 'init', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );

Plugin_Updater::get_instance(
	new class() implements Settings_Interface {
		use Settings_Trait;

		public function get_plugin_basename(): string {
			return 'development-plugin/development-plugin.php';
		}
	}
);
