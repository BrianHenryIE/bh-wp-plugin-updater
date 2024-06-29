<?php
/**
 *
 * @see https://licenseserver.io/
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC;

use BrianHenryIE\WP_SLSWC_Client\Exception\Licence_Does_Not_Exist_Exception;
use BrianHenryIE\WP_SLSWC_Client\Exception\Licence_Key_Not_Set_Exception;
use BrianHenryIE\WP_SLSWC_Client\Exception\Max_Activations_Exception;
use BrianHenryIE\WP_SLSWC_Client\Exception\SLSWC_Exception_Abstract;
use BrianHenryIE\WP_SLSWC_Client\Exception\Slug_Not_Found_On_Server_Exception;
use BrianHenryIE\WP_SLSWC_Client\Integrations\Integration_Interface;
use BrianHenryIE\WP_SLSWC_Client\Licence;
use BrianHenryIE\WP_SLSWC_Client\Model\Plugin_Info_Interface;
use BrianHenryIE\WP_SLSWC_Client\Model\Plugin_Update_Interface;
use BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model\Check_Updates_Response;
use BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model\License_Response;
use BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model\Product_Response;
use BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model\Software_Details;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\CLI;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\Cron;
use DateTimeImmutable;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class SLSWC implements Integration_Interface {
	use LoggerAwareTrait;

	const REMOTE_REST_API_BASE = 'wp-json/slswc/v1/';

	protected Licence $licence;

	public function __construct(
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );
	}


	/**
	 * Get the licence and product details from the licence server.
	 *
	 * @used-by Cron::handle_update_check_cron_job()
	 * @used-by CLI
	 * @throws SLSWC_Exception_Abstract
	 */
	public function refresh_licence_details( Licence $licence ): Licence {

		if ( is_null( $licence->get_licence_key() ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		// TODO: This should never be called on a pageload.

		// TODO: Do not continuously retry.

		$response = $this->server_request( $licence, 'check_update' ); // ?? "check update"? I think maybe this should be "activate".

		$licence->set_status( $response->get_status() );
		$licence->set_last_updated( new DateTimeImmutable() );

		return $licence;
	}

	/**
	 * Send a HTTP request to deactivate the licence from this site.
	 *
	 * Is this a good idea? Should it only be possible from the licence server?
	 *
	 * https://updatestest.bhwp.ie/wp-json/slswc/v1/deactivate?slug=a-plugin
	 *
	 * @param Licence $licence The licence to deactivate.
	 *
	 * @throws SLSWC_Exception_Abstract
	 */
	public function deactivate_licence( Licence $licence ): Licence {

		if ( is_null( $licence->get_licence_key() ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		$response = $this->server_request( $licence, 'deactivate' );

		$licence->set_status( $response->get_status() );
		$licence->set_expiry_date( $response->get_expires() );

		return $licence;
	}

	/**
	 * Activate the licence on this site.
	 *
	 * https://bhwp.ie/wp-json/slswc/v1/activate?slug=a-plugin&license_key=ffa19a46c4202cf1dac17b8b556deff3f2a3cc9a
	 *
	 * @param Licence $licence The licence to activate.
	 *
	 * @throws SLSWC_Exception_Abstract
	 */
	public function activate_licence( Licence $licence ): Licence {

		if ( is_null( $licence->get_licence_key() ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		$response = $this->server_request( $licence, 'activate', License_Response::class );

		$licence->set_status( $response->get_status() );

		// TODO: string -> DateTime
		// $licence->set_expires( $response_body->expires );

		return $licence;
	}

	/**
	 * Returns null when it could not fetch the product information.
	 *
	 * @param Licence $licence
	 */
	public function get_remote_product_information( Licence $licence ): ?Plugin_Info_Interface {

		// I think maybe the difference between check_update and product is one expects a valid licence
		/** @var Product_Response $response */
		$response = $this->server_request( $licence, 'product', Product_Response::class );

		return $response->get_product();
	}

	/**
	 * Returns null when it could not fetch the product information.
	 *
	 * @param Licence $licence
	 */
	public function get_remote_check_update( Licence $licence ): ?Plugin_Update_Interface {

		// I think maybe the difference between check_update and product is one expects a valid licence.
		/** @var Check_Updates_Response $response */
		$response = $this->server_request( $licence, 'check_update', Check_Updates_Response::class );

		// if ( update_option( $this->settings->get_check_update_option_name(), $response->get_software_details() ) ) {
		// $this->logger->debug( 'Updated check_update option with `Software_Details` object' );
		// }

		return $this->software_details_to_plugin_update( $response->get_software_details() );
	}

	/**
	 * Convert a Software_Details object to a Plugin_Update_Interface object.
	 *
	 * @param Software_Details $software_details
	 */
	protected function software_details_to_plugin_update( Software_Details $software_details ): Plugin_Update_Interface {
		return new class( $software_details ) implements Plugin_Update_Interface {
			public function __construct(
				protected Software_Details $software_details
			) {
			}

			public function get_id(): ?string {
				return $this->software_details->get_id();
			}

			public function get_slug(): string {
				return $this->software_details->get_slug();
			}

			public function get_version(): string {
				return $this->software_details->get_version();
			}

			public function get_url(): string {
				return $this->software_details->get_homepage();
			}

			public function get_package(): string {
				return $this->software_details->get_package();
			}

			public function get_tested(): ?string {
				return $this->software_details->get_tested();
			}

			public function get_requires_php(): ?string {
				return $this->software_details->get_requires();
			}

			public function get_autoupdate(): ?bool {
				// null, //        return $this->software_details->get_autoupdate();
				// TODO: Implement get_autoupdate() method.
			}

			public function get_icons(): ?array {
				return $this->software_details->get_icons();
			}

			public function get_banners(): ?array {
				// null, // TODO $software_details->get_banners();
				// TODO: Implement get_banners() method.
			}

			public function get_banners_rtl(): ?array {
				return $this->software_details->get_banners_rtl();
			}

			public function get_translations(): ?array {
				return $this->software_details->get_translations();
			}
		};
	}

	/**
	 * Append the REST path and action to the server provided in the Settings.
	 *
	 * If the provided server URL does not have http/https, https is assumed.
	 *
	 * https://my-domain.com/wp-json/slswc/v1/product
	 */
	protected function get_rest_url( string $action ): string {
		$licence_server_host = $this->settings->get_licence_server_host();

		$scheme = wp_parse_url( $licence_server_host, PHP_URL_SCHEME ) ?? 'https';
		$host   = wp_parse_url( $licence_server_host, PHP_URL_HOST );
		$path   = wp_parse_url( $licence_server_host, PHP_URL_PATH );

		return trailingslashit( "{$scheme}://{$host}{$path}" ) . self::REMOTE_REST_API_BASE . $action;
	}

	/**
	 * Send a request to the server.
	 *
	 * @param Licence $licence
	 * @param string  $action activate|deactivate|check_update|product.
	 * @param string  $type The class to map the response to.
	 * @throws
	 */
	protected function server_request( Licence $licence, string $action, string $type = License_Response::class ) {

		$request_info = array(
			'slug'        => $this->settings->get_plugin_slug(),
			'license_key' => $licence->get_licence_key(),
			'domain'      => get_home_url(), // Ideally, the server would use the HTTP user agent header, which contains the URL.
		);

		/**
		 * Build the server url api end point fix url build to support the WordPress API.
		 *
		 * https://my-domain.com/wp-json/slswc/v1/product/?slug=plugin-slug&licence_key=licence-key
		 */
		$server_request_url = add_query_arg(
			$request_info,
			$this->get_rest_url( $action )
		);

		// Options to parse the wp_safe_remote_get() call.
		$request_options = array( 'timeout' => 30 );

		// Query the license server.
		$endpoint_get_actions = apply_filters( 'slswc_client_get_actions', array( 'product', 'products' ) );
		if ( in_array( $action, $endpoint_get_actions, true ) ) {
			$response = wp_remote_get( $server_request_url, $request_options );
		} else {
			$response = wp_remote_post( $server_request_url, $request_options );
		}

		// Validate that the response is valid not what the response is.
		// Check if there is an error and display it if there is one, otherwise process the response.
		// @throws
		$this->validate_response(
			array(
				'server_request_url' => $server_request_url,
				'request_options'    => $request_options,
			),
			$response
		);

		$factory_registry = new FactoryRegistry();
		$mapper           = JsonMapperBuilder::new()
											->withDocBlockAnnotationsMiddleware()
											->withObjectConstructorMiddleware( $factory_registry )
											->withPropertyMapper( new PropertyMapper( $factory_registry ) )
											->withTypedPropertiesMiddleware()
											->withNamespaceResolverMiddleware()
											->build();

		return $mapper->mapToClassFromString( wp_remote_retrieve_body( $response ), $type );
	}

	/**
	 * Validate the license server response to ensure its valid response not what the response is.
	 *
	 * @param array           $request
	 * @param \WP_Error|array $response
	 *
	 * @throws SLSWC_Exception_Abstract
	 */
	protected function validate_response( array $request, $response ): void {

		$this->logger->debug(
			'Validating response',
			array(
				'request'  => $request,
				'response' => $response,
			)
		);

		$this->logger->debug( $response['body'] );

		if ( ! empty( $response ) ) {

			// Can't talk to the server at all, output the error.
			if ( is_wp_error( $response ) ) {
				throw new \Exception(
					sprintf(
					// translators: 1. Error message.
						__( 'HTTP Error: %1$s. %2$s', 'bh-wp-slswc-client' ),
						$response->get_error_message(),
						$request['server_request_url']
					),
					(int) $response->get_error_code()
				);
			}

			// There was a problem with the initial request.
			if ( ! isset( $response['response']['code'] ) ) {
				throw new \Exception(
				// 'slswc_no_response_code',
					__( 'wp_safe_remote_get() returned an unexpected result.', 'bh-wp-slswc-client' )
				);
			}

			// Slug_Not_Found_On_Server_Exception

			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				$body = json_decode( $response['body'] );
				if ( isset( $body->message ) ) {
					switch ( substr( $body->message, 0, 30 ) ) {
						case substr( 'You have reached the maximum number of allowed activations on staging domain', 0, 30 ):
							throw new Max_Activations_Exception();
						default:
							break;
					}
				}
			}

			// There is a validation error on the server side, output the problem.
			if ( 404 === $response['response']['code'] ) {
				// This could be because the server does not have the License Server plugin active.

				// This could be because the plugin slug is not found on the server.

				$this->logger->error( $response['body'] );

				throw new \Exception(
					__( '404 There was a problem with the license server. ' . $request['server_request_url'], 'bh-wp-slswc-client' ),
				);
			}

			if ( 400 === $response['response']['code'] ) {

				$body = json_decode( $response['body'] );

				$this->logger->error( '`json:' . json_encode( $body ) . '`' );

				switch ( substr( $body->message, 0, 30 ) ) {
					case substr( 'Invalid parameter(s): license_key, slug', 0, 30 ):
						throw new Licence_Does_Not_Exist_Exception();
					case substr( 'Invalid parameter(s): slug', 0, 30 ):
						throw new Slug_Not_Found_On_Server_Exception();
					default:
						break;
				}

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

			// TODO: delete. When the json fails to parse, that will throw an error.
			if ( empty( $response['body'] ) ) {
				throw new \Exception(
				// 'slswc_no_response',
					__( 'The server returned no response.', 'bh-wp-slswc-client' )
				);
			}
		}
	}
}
