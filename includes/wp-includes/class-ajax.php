<?php
/**
 * Handle the AJAX requests.
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;

/**
 * Most functions return the Licence object.
 * TODO: add a message.
 */
class AJAX {

	/**
	 * Constructor.
	 *
	 * @param API_Interface $api The main API class where the functionality is implemented.
	 */
	public function __construct(
		protected API_Interface $api,
	) {
	}

	/**
	 * @hooked wp_ajax_{plugin_slug}_get_license_details
	 */
	public function get_licence_details(): void {

		if ( ! check_ajax_referer( self::class, false, false ) ) {
			wp_send_json_error( array( 'message' => 'Bad/no nonce.' ), 400 );
		}

		$refresh = isset( $_GET['refresh'] ) ? (bool) $_GET['refresh'] : null;

		$result = $this->api->get_licence_details( $refresh );

		wp_send_json( $result );
	}

	/**
	 * @hooked wp_ajax_{plugin_slug}_set_licence_key
	 */
	public function set_licence_key(): void {

		if ( ! check_ajax_referer( self::class, false, false ) ) {
			wp_send_json_error( array( 'message' => 'Bad/no nonce.' ), 400 );
		}

		if ( ! isset( $_POST['licence_key'] ) ) {
			wp_send_json_error( 'No licence key provided.', 400 );
		}

		$result = $this->api->activate_licence( sanitize_key( wp_unslash( $_POST['licence_key'] ) ) );

		wp_send_json( $result );
	}

	/**
	 * @hooked wp_ajax_{plugin_slug}_deactivate
	 */
	public function deactivate(): void {

		if ( ! check_ajax_referer( self::class, false, false ) ) {
			wp_send_json_error( array( 'message' => 'Bad/no nonce.' ), 400 );
		}

		// current_user_can() ?

		$result = $this->api->deactivate_licence();

		wp_send_json( $result );
	}

	/**
	 * @hooked wp_ajax_{plugin_slug}_activate
	 */
	public function activate(): void {

		if ( ! check_ajax_referer( self::class, false, false ) ) {
			wp_send_json_error( array( 'message' => 'Bad/no nonce.' ), 400 );
		}

		// current_user_can() ?

		$licence = $this->api->get_licence_details( false );

		$result = $this->api->activate_licence( $licence->get_licence_key() );

		wp_send_json( $result );
	}
}
