<?php
/**
 * Plugin Name:   Test Plugin    Software License Server for WooCommerce
 * Description:   My test plugin description
 * Version:       1.1.1
 * Author:        BrianHenryIE
 * Author URI:    https://bhwp.ie
 * License Server: https://localhost:8889
 *
 * @package brianhenryie/bh-wp-slswc-client
 */


namespace BrianHenryIE\WP_SLSWC_Client_Test_Plugin;

use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use BrianHenryIE\WP_SLSWC_Client\SLSWC_Client;

require_once __DIR__ . '/vendor/autoload.php';

$settings = new \BrianHenryIE\WP_SLSWC_Client\Settings(
	plugin_basename( __FILE__ ),
	'updatestest.bhwp.ie'
);

class Init_Slswc_Client {

	public function __construct(
		protected Settings_Interface $settings
	) {
	}

	public function init_slswc() {
		SLSWC_Client::get_instance(
			$this->settings,
			new \Psr\Log\NullLogger()
		);
	}

	public function example_admin_enqueue_scripts() {
		$plugin_slug = explode( '/', plugin_basename( __FILE__ ) )[0];

		$script_handle = "{$plugin_slug}-licence";

		// Only load the JS on the plugin information modal for this plugin.
		global $pagenow;
		if ( 'plugin-install.php' !== $pagenow
			|| ! isset( $_GET['plugin'] )
			|| sanitize_key( wp_unslash( $_GET['plugin'] ) !== $plugin_slug )
		) {
			return;
		}

		$asset_file = include plugin_dir_path( __FILE__ ) . 'vendor/brianhenryie/bh-wp-slswc-client/build/index.asset.php';

		wp_enqueue_script(
			$script_handle,
			plugins_url( './build/index.js', __FILE__ ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$api = SLSWC_Client::get_instance();

		$data = wp_json_encode(
			array(
				'restUrl'         => rest_url( "{$this->settings->get_rest_base()}/v1" ),
				'nonce'           => wp_create_nonce( \BrianHenryIE\WP_SLSWC_Client\WP_Includes\Rest::class ),
				'licence_details' => $api->get_licence_details(),
			)
		);

		// `bh-wc-zelle-gateway-licence` -> `bhWcZelleGatewayLicence`;
		$script_var_name = lcfirst( str_replace( ' ', '', ucwords( str_replace( '-', ' ', $script_handle ) ) ) );

		wp_add_inline_script(
			$script_handle,
			"const {$script_var_name} = {$data};",
			'before'
		);
	}
}

$init_slswc = new Init_Slswc_Client( $settings );

add_action( 'plugins_loaded', array( $init_slswc, 'init_slswc' ) );

/**
 * @hooked admin_enqueue_scripts
 */
add_action( 'admin_enqueue_scripts', array( $init_slswc, 'example_admin_enqueue_scripts' ) );
