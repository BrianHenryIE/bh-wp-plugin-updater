<?php
/**
 * Should only run in background
 * Should trigger when the wp transient is set and manually edit it afterwards/ set its own transient for same time
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\WP_Includes\CLI;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\Cron;
use DateTimeImmutable;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class API implements API_Interface {
	use LoggerAwareTrait;

	const REST_API_PATH = 'wp-json/slswc/v1/';

	protected Licence $licence;

	public function __construct(
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );
		$this->licence = $this->get_licence_details( false );
	}

	public function get_licence_details( ?bool $refresh = null ): Licence {
		switch ( $refresh ) {
			case true:
				return $this->refresh_licence_details();
			case false:
				return $this->get_saved_licence_information() ?? new Licence( $this->settings );
			default:
				return $this->get_saved_licence_information() ?? $this->refresh_licence_details();
		}
	}

	protected function get_saved_licence_information(): ?Licence {
		$value = get_option(
			$this->settings->get_licence_data_option_name(),
			null
		);
		return $value instanceof Licence ? $value : null;
	}

	/**
	 * Get the licence and product details from the licence server.
	 *
	 * @used-by Cron::handle_update_check_cron_job()
	 * @used-by CLI
	 */
	protected function refresh_licence_details(): Licence {

		$response = $this->server_request( 'check_update' );

		// stdClass Object
		// (
		// [status] => active
		// [slug] => bh-wc-zelle-gateway
		// [message] => License Valid
		// [software_details] => stdClass Object
		// (
		// [name] => bh-wc-zelle-gateway
		// [id] => 2
		// [slug] => bh-wc-zelle-gateway
		// [plugin] => bh-wc-zelle-gateway
		// [version] => 1.1.0
		// [last_updated] => 2023-11-15 00:00:00
		// [homepage] => http://localhost:8080/bh-wp-autologin-urls/product/bh-wc-zelle-gateway/
		// [requires] => 6.0
		// [tested] => 6.4
		// [new_version] => 1.1.0
		// [author] => BH
		// [sections] => stdClass Object
		// (
		// [description] => bh-wc-zelle-gateway
		// [installation] =>
		// [changelog] => 1.1.0 zelle
		// )
		// [banners] => stdClass Object
		// (
		// [low] =>
		// [high] =>
		// )
		//
		// [rating] => 0
		// [ratings] => Array
		// (
		// )
		//
		// [num_ratings] => 0
		// [active_installs] => 1
		// [external] => 1
		// [package] =>
		// [download_link] =>
		// )
		// )

		$this->licence->set_status( $response->status );
		$this->licence->set_last_updated( new DateTimeImmutable() );

		// TODO: string -> DateTime
		// $this->licence->set_expires( $response_body->expires );

		return $this->licence;
	}

	/**
	 * Send a HTTP request to deactivate the licence from this site.
	 *
	 * Is this a good idea? Should it only be possible from the licence server?
	 */
	public function deactivate_licence(): Licence {
		$response = $this->server_request( 'deactivate' );

		$this->licence->set_status( $response->status );
		$this->licence->set_last_updated( new DateTimeImmutable() );

		// TODO: string -> DateTime
		// $this->licence->set_expires( $response_body->expires );

		return $this->licence;
	}

	/**
	 * Activate the licence on this site.
	 */
	public function activate_licence( string $licence_key ): Licence {
		$this->licence->set_licence_key( $licence_key );

		$response = $this->server_request( 'activate' );

		$this->licence->set_status( $response->status );
		$this->licence->set_last_updated( new DateTimeImmutable() );

		// TODO: string -> DateTime
		// $this->licence->set_expires( $response_body->expires );

		return $this->licence;
	}


	/**
	 * Product information should be available regardless of licence status.
	 *
	 * Get the remote product information for the {@see get_plugins()} information array.
	 *
	 * @return  array
	 */
	public function get_product_information( ?bool $refresh = null ): ?object {

		switch ( $refresh ) {
			case true:
				return $this->get_remote_product_information();
			case false:
				return $this->get_cached_product_information();
			default:
				return $this->get_cached_product_information() ?? $this->get_remote_product_information();
		}
	}

	protected function get_cached_product_information(): ?object {
		return get_option(
			$this->settings->get_plugin_information_option_name(),
			null
		);
	}

	/**
	 * Returns null when it could not fetch the product information.
	 */
	protected function get_remote_product_information(): ?object {

		$response = $this->server_request( 'product' );

		if ( is_object( $response ) && 'ok' === $response->status ) {

			update_option( $this->settings->get_plugin_information_option_name(), $response->product );

			return $response->product;
		}

		return null;
	}

	/**
	 * https://my-domain.com/wp-json/slswc/v1/product
	 */
	protected function get_base_url( string $action ): string {
		return trailingslashit( $this->settings->get_licence_server_host() ) . self::REST_API_PATH . $action;
	}

	/**
	 * Send a request to the server.
	 *
	 * @param   string $action activate|deactivate|check_update.
	 */
	protected function server_request( string $action = 'check_update' ): ?object {

		$request_info = array(
			'slug'        => $this->settings->get_plugin_slug(),
			'license_key' => $this->licence->get_licence_key(),
		);

		/**
		 * Build the server url api end point fix url build to support the WordPress API.
		 *
		 * https://my-domain.com/wp-json/slswc/v1/product/?slug=plugin-slug&licence_key=licence-key
		 */
		$server_request_url = add_query_arg(
			$request_info,
			$this->get_base_url( $action )
		);

		// Options to parse the wp_safe_remote_get() call.
		$request_options = array( 'timeout' => 30 );

		// Query the license server.
		$endpoint_get_actions = apply_filters( 'slswc_client_get_actions', array( 'product', 'products' ) );
		if ( in_array( $action, $endpoint_get_actions, true ) ) {
			$response = wp_safe_remote_get( $server_request_url, $request_options );
		} else {
			$response = wp_safe_remote_post( $server_request_url, $request_options );
		}

		// Validate that the response is valid not what the response is.
		// Check if there is an error and display it if there is one, otherwise process the response.
		// @throws
		$this->validate_response( $response );

		return json_decode( wp_remote_retrieve_body( $response ) );

		// $this->logger->error( 'There was an error executing this request, please check the errors below.', array( 'response' => $response ) );
		//
		// return null;
	}

	/**
	 * Validate the license server response to ensure its valid response not what the response is.
	 */
	public function validate_response( $response ): void {

		if ( ! empty( $response ) ) {

			// Can't talk to the server at all, output the error.
			if ( is_wp_error( $response ) ) {
				throw new \Exception(
					sprintf(
					// translators: 1. Error message.
						__( 'HTTP Error: %s', 'bh-wp-slswc-client' ),
						$response->get_error_message()
					),
					$response->get_error_code()
				);
			}

			// There was a problem with the initial request.
			if ( ! isset( $response['response']['code'] ) ) {
				throw new \Exception(
				// 'slswc_no_response_code',
					__( 'wp_safe_remote_get() returned an unexpected result.', 'bh-wp-slswc-client' )
				);
			}

			// There is a validation error on the server side, output the problem.
			if ( 404 === $response['response']['code'] ) {
				$this->logger->error( $response['body'] );
				throw new \Exception(
					__( 'There was a problem with the license server.', 'bh-wp-slswc-client' ),
				);
			}
			if ( 400 === $response['response']['code'] ) {

				$body = json_decode( $response['body'] );

				foreach ( $body->data->params as $param => $message ) {
					throw new \Exception(
					// 'slswc_validation_failed',
						sprintf(
						// translators: %s: Error/response message.
							__( 'There was a problem with your license: %s', 'bh-wp-slswc-client' ),
							$message
						)
					);
				}
			}

			// The server is broken.
			if ( 500 === $response['response']['code'] ) {
				throw new \Exception(
				// 'slswc_internal_server_error',
					sprintf(
					// translators: %s: the http response code from the server.
						__( 'There was a problem with the license server: HTTP response code is : %s', 'bh-wp-slswc-client' ),
						$response['response']['code']
					)
				);
			}

			if ( 200 !== $response['response']['code'] ) {
				throw new \Exception(
				// 'slswc_unexpected_response_code',
					sprintf(
						__( 'HTTP response code is : % s, expecting ( 200 )', 'bh-wp-slswc-client' ),
						$response['response']['code']
					)
				);
			}

			if ( empty( $response['body'] ) ) {
				throw new \Exception(
				// 'slswc_no_response',
					__( 'The server returned no response.', 'bh-wp-slswc-client' )
				);
			}
		}
	}
}
