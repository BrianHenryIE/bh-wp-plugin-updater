<?php
/**
 * CLI commands to view/set the licence key and status, to activate/deactivate the licence, and to refresh
 * product information from the update server.
 *
 * No need for a plugin update command, since updates should work as normal through `wp plugin update`.
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use WP_CLI;

/**
 * `wp zelle licence get-status`
 */
class CLI {

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
	 * Get the licence status
	 *
	 * ## EXAMPLES
	 *
	 *   # Get the licence status for the Zelle plugin.
	 *   $ wp zelle licence get-status
	 *   Success: active
	 *
	 * @see API_Interface::get_licence_details()
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 */
	public function get_licence_status( array $args, array $assoc_args ): void {

		$result = $this->api->get_licence_details();

		WP_CLI::success( $result->get_status() );
	}

	/**
	 * Get the licence key
	 *
	 * ## EXAMPLES
	 *
	 *   # Get the licence key the Zelle plugin has been configured with.
	 *   $ wp zelle licence get-key
	 *   Success: 876235557140adb9b8c47b28488cda8481d98495
	 *
	 * @see API_Interface::get_licence_details()
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 */
	public function get_licence_key( array $args, array $assoc_args ): void {

		$result = $this->api->get_licence_details( false );

		WP_CLI::success( $result->get_licence_key() ?? 'empty' );
	}

	/**
	 * Set the licence key.
	 *
	 * Immediately activates the licence.
	 *
	 * ## OPTIONS
	 *
	 *  <licence_key>
	 *  : Alphanumeric licence key.
	 *
	 * ## EXAMPLES
	 *
	 *   # Set the licence key the Zelle plugin should use.
	 *   $ wp zelle licence set-key 876235557140adb9b8c47b28488cda8481d98495
	 *   Success: active
	 *
	 *   # Set an invalid licence key
	 *   $ wp zelle licence set-key a1s2invalidp0o9
	 *   TODO
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::activate_licence()
	 */
	public function set_licence_key( array $args, array $assoc_args ): void {

		$result = $this->api->activate_licence( $args[0] );

		WP_CLI::success( $result->get_status() );
	}


	/**
	 * Deactivate the licence.
	 *
	 * Deactivates the configured licence key.
	 *
	 * ## EXAMPLES
	 *
	 *   # Deactivate the licence key the Zelle plugin is using.
	 *   $ wp zelle licence deactivate
	 *   TODO
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::deactivate_licence()
	 */
	public function deactivate( array $args, array $assoc_args ): void {

		$result = $this->api->deactivate_licence();

		WP_CLI::success( $result->get_status() );
	}

	/**
	 * Activate the already configured licence.
	 *
	 * ## EXAMPLES
	 *
	 *   # Activate this domain to use the configured licence key for Zelle plugin updates.
	 *   $ wp zelle licence activate
	 *   TODO
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::activate_licence()
	 */
	public function activate( array $args, array $assoc_args ): void {
		$licence = $this->api->get_licence_details( false );

		$result = $this->api->activate_licence( $licence->get_licence_key() );

		WP_CLI::success( $result->get_status() );
	}

	/**
	 * Refresh the product information from the plugin update server.
	 *
	 * ## EXAMPLES
	 *
	 *   # Refresh the product information from the plugin update server.
	 *   $ wp zelle product-information update
	 *   Success: {json} TODO: display as table
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::get_product_information()
	 */
	public function get_product_details( array $args, array $assoc_args ): void {

		$result = $this->api->get_product_information( true );

		WP_CLI::success( wp_json_encode( $result ) ?: '' );
	}
}
