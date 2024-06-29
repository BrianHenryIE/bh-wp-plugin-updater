<?php
/**
 * Plugin Name:   Test Plugin    Software License Server for WooCommerce
 * Description:   My test plugin description
 * Version:       1.1.1
 * Author:        BrianHenryIE
 * Author URI:    https://bhwp.ie
 * Update URI:    updatestest.bhwp.ie
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\WP_Logger\Logger;

// TODO: Does this break the openapi generation?
if ( ! ( is_admin() || wp_doing_cron() || wp_is_serving_rest_request() || defined( 'WP_CLI' ) ) ) {
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

remove_action( 'plugins_loaded', '\\BrianHenryIE\\WP_Plugin_Updater\\init_plugin_updater' );


$settings = new class() implements \BrianHenryIE\WP_Plugin_Updater\Settings_Interface {
	use \BrianHenryIE\WP_Plugin_Updater\Settings_Trait;

	public function get_plugin_basename(): string {
		return plugin_basename( __FILE__ );
	}
};

class Init_Slswc_Client {

	public function __construct(
		protected Settings_Interface $settings
	) {
	}

	/**
	 * @hooked plugins_loaded
	 */
	public function init_slswc(): void {

		$logger_settings = new class() implements \BrianHenryIE\WP_Logger\Logger_Settings_Interface {
			use \BrianHenryIE\WP_Logger\Logger_Settings_Trait;

			public function get_plugin_basename(): string {
				return plugin_basename( __FILE__ );
			}

			public function get_log_level(): string {
				return 'debug';
			}
		};

		$logger = Logger::instance( $logger_settings );

		SLSWC_Client::get_instance(
			$this->settings,
			$logger
		);
	}
}

$init_slswc = new Init_Slswc_Client( $settings );

add_action( 'plugins_loaded', array( $init_slswc, 'init_slswc' ), 0 );
