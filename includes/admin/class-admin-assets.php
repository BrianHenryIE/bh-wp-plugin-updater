<?php
/**
 * Enqueue the JS for the licence tab and add the nonce for the AJAX requests.
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Admin;

class Admin_Assets {

	public function register_script(): void {
	}

	public function enqueue_script(): void {
	}

	/**
	 * @hooked admin_enqueue_scripts
	 */
	public function enqueue_styles(): void {

		wp_enqueue_style( 'wp-components' );
	}
}
