<?php
/**
 * Enqueue the JS for the licence tab and add the nonce for the AJAX requests.
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Admin;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Licence;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use BrianHenryIE\WP_SLSWC_Client\SLSWC_Client;

use function BrianHenryIE\WP_SLSWC_Client\str_dash_to_next_capitalised_first_lower;

class Admin_Assets {

	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
	) {
	}

	public function register_script(): void {
	}

	public function enqueue_script(): void {

		$script_handle = "{$this->settings->get_plugin_slug()}-licence";

		$asset_file = include dirname( __DIR__, 2 ) . '/build/index.asset.php';

		wp_enqueue_script(
			$script_handle,
			plugins_url( '../../build/index.js', __FILE__ ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		try {
			$licence_details = $this->api->get_licence_details();
		} catch ( \Exception $e ) {
			$licence_details = new Licence();
		}

		$data = wp_json_encode(
			array(
				'restUrl'         => rest_url( "{$this->settings->get_rest_base()}/v1" ),
				'nonce'           => wp_create_nonce( \BrianHenryIE\WP_SLSWC_Client\WP_Includes\Rest::class ),
				'licence_details' => $licence_details,
				// 'licence_details' => $api->get_licence_details(),
			)
		);

		$script_var_name = str_dash_to_next_capitalised_first_lower( $script_handle );

		wp_add_inline_script(
			$script_handle,
			"const {$script_var_name} = {$data};",
			'before'
		);
	}

	/**
	 * @hooked admin_enqueue_scripts
	 */
	public function enqueue_styles(): void {

		wp_enqueue_style( 'wp-components' );
	}
}
