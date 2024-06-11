<?php
/**
 * Should only run in background
 * Should trigger when the wp transient is set and manually edit it afterwards/ set its own transient for same time
 *
 * @package brianhenryie/bh-wp-slswc-client
 *
 * Adapted from https://licenseserver.io/
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\Exception\Licence_Does_Not_Exist_Exception;
use BrianHenryIE\WP_SLSWC_Client\Exception\Licence_Key_Not_Set_Exception;
use BrianHenryIE\WP_SLSWC_Client\Exception\Max_Activations_Exception;
use BrianHenryIE\WP_SLSWC_Client\Exception\SLSWC_Exception_Abstract;
use BrianHenryIE\WP_SLSWC_Client\Exception\Slug_Not_Found_On_Server_Exception;
use BrianHenryIE\WP_SLSWC_Client\Server\SLSWC\Check_Updates_Response;
use BrianHenryIE\WP_SLSWC_Client\Server\SLSWC\License_Response;
use BrianHenryIE\WP_SLSWC_Client\Server\SLSWC\Product;
use BrianHenryIE\WP_SLSWC_Client\Server\SLSWC\Product_Response;
use BrianHenryIE\WP_SLSWC_Client\Server\SLSWC\Software_Details;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\CLI;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\Cron;
use DateTimeImmutable;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class API implements API_Interface {
	use LoggerAwareTrait;

	const REMOTE_REST_API_BASE = 'wp-json/slswc/v1/';

	protected Licence $licence;

	public function __construct(
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );
		$this->licence = $this->get_licence_details( false );
	}

	/**
	 * Set the licence key without activting it.
	 *
	 * Deactivates existing licence key if present.
	 *
	 * @param string $license_key
	 *
	 * @throws SLSWC_Exception_Abstract If failing to deactivate the existing licence.
	 */
	public function set_license_key( string $license_key ): Licence {

		$existing_key = $this->licence->get_licence_key();
		if ( $existing_key === $license_key ) {
			return $this->licence;
		}
		if ( ! empty( $existing_key ) ) {
			if ( $this->licence->get_status() === 'active' ) {
				$this->deactivate_licence();
			}
		}

		$this->licence->set_licence_key( $license_key );
		$this->save_licence_information( $this->licence );

		return $this->licence;
	}

	/**
	 * Get the licence information, maybe cached, maybe remote, maaybe an empty Licence object.
	 *
	 * @param bool|null $refresh True: force refresh from API; false: do not refresh; null: use cached value or refresh if missing.
	 *
	 * @throws SLSWC_Exception_Abstract
	 */
	public function get_licence_details( ?bool $refresh = null ): Licence {
		return match ( $refresh ) {
			true => $this->refresh_licence_details(),
			false => $this->get_saved_licence_information() ?? new Licence(),
			default => $this->get_saved_licence_information() ?? $this->refresh_licence_details(),
		};
	}

	/**
	 * Get the licence information from the WordPress options database table. Verifies it is a Licence object.
	 */
	protected function get_saved_licence_information(): ?Licence {
		$value = get_option(
			$this->settings->get_licence_data_option_name(),
			null
		);
		return $value instanceof Licence ? $value : null;
	}

	protected function save_licence_information( Licence $licence ): void {
		update_option(
			$this->settings->get_licence_data_option_name(),
			$licence
		);
	}

	/**
	 * Get the licence and product details from the licence server.
	 *
	 * @used-by Cron::handle_update_check_cron_job()
	 * @used-by CLI
	 * @throws SLSWC_Exception_Abstract
	 */
	protected function refresh_licence_details(): Licence {

		if ( is_null( $this->licence->get_licence_key() ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		// TODO: This should never be called on a pageload.

		// TODO: Do not continuously retry.

		$response = $this->server_request( 'check_update' ); // ?? "check update"? I think maybe this should be "activate".

		$this->licence->set_status( $response->get_status() );
		$this->licence->set_last_updated( new DateTimeImmutable() );

		return $this->licence;
	}

	/**
	 * Send a HTTP request to deactivate the licence from this site.
	 *
	 * Is this a good idea? Should it only be possible from the licence server?
	 *
	 * https://updatestest.bhwp.ie/wp-json/slswc/v1/deactivate?slug=a-plugin
	 *
	 * @throws SLSWC_Exception_Abstract
	 */
	public function deactivate_licence(): Licence {

		if ( is_null( $this->licence->get_licence_key() ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		$response = $this->server_request( 'deactivate' );

		$this->licence->set_status( $response->get_status() );
		$this->licence->set_expiry_date( $response->get_expires() );

		$this->licence->set_last_updated( new DateTimeImmutable() );

		return $this->licence;
	}

	/**
	 * Activate the licence on this site.
	 *
	 * https://bhwp.ie/wp-json/slswc/v1/activate?slug=a-plugin&license_key=ffa19a46c4202cf1dac17b8b556deff3f2a3cc9a
	 *
	 * @throws SLSWC_Exception_Abstract
	 */
	public function activate_licence(): Licence {

		if ( is_null( $this->licence->get_licence_key() ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		$response = $this->server_request( 'activate' );

		$this->licence->set_status( $response->get_status() );
		// TODO: Let's record "last successfully updated" as well as "last updated". (or use a rate limiter)
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
	 * null when first run and no cached product information.
	 */
	public function get_product_information( ?bool $refresh = null ): ?Product {

		if ( true !== $refresh ) {
			// TODO: Add a background task to refresh the product information.
			// TODO: Check the last time it was refreshed and rate limit the refreshing.
		}

		return match ( $refresh ) {
			true => $this->get_remote_product_information(),
			false => $this->get_cached_product_information(),
			default => $this->get_cached_product_information() ?? $this->get_remote_product_information(),
		};
	}

	protected function get_cached_product_information(): ?Product {
		$cached_product_information = get_option(
			// plugin_slug_plugin_information
			$this->settings->get_plugin_information_option_name(),
			null
		);
		if ( $cached_product_information instanceof Product ) {
			$this->logger->debug( 'returning cached product information for ' . $cached_product_information->get_software_slug() );
			return $cached_product_information;
		}
		$this->logger->debug( 'product not found in cache: ' . $this->settings->get_plugin_slug() );
		return null;
	}

	/**
	 * Returns null when it could not fetch the product information.
	 */
	protected function get_remote_product_information(): ?Product {

		// I think maybe the difference between check_update and product is one expects a valid licence
		/** @var Product_Response $response */
		$response = $this->server_request( 'product', Product_Response::class );

		update_option( $this->settings->get_plugin_information_option_name(), $response->get_product() );

		return $response->get_product();
	}


	/**
	 * Update information should be available regardless of licence status... alas, it is not.
	 *
	 * Get the remote product information for the {@see get_plugins()} information array.
	 *
	 * null when first run and no cached information.
	 */
	public function get_check_update( ?bool $refresh = null ): ?Software_Details {

		if ( true !== $refresh ) {
			// TODO: Add a background task to refresh the product information.
			// TODO: Check the last time it was refreshed and rate limit the refreshing.
		}

		return match ( $refresh ) {
			true => $this->get_remote_check_update(),
			false => $this->get_cached_check_update(),
			default => $this->get_cached_check_update() ?? $this->get_remote_check_update(),
		};
	}

	protected function get_cached_check_update(): ?Software_Details {
		$cached_product_information = get_option(
		// plugin_slug_update_information
			$this->settings->get_check_update_option_name(),
			null
		);
		if ( $cached_product_information instanceof Software_Details ) {
			$this->logger->debug( 'returning cached check_update for ' . $this->settings->get_plugin_slug() );
			return $cached_product_information;
		}
		$this->logger->debug( 'check_update Software_Details not found in cache: ' . $this->settings->get_plugin_slug() );
		return null;
	}

	/**
	 * Returns null when it could not fetch the product information.
	 */
	protected function get_remote_check_update(): ?Software_Details {

		// I think maybe the difference between check_update and product is one expects a valid licence.
		/** @var Check_Updates_Response $response */
		$response = $this->server_request( 'check_update', Check_Updates_Response::class );

		update_option( $this->settings->get_plugin_information_option_name(), $response->get_software_details() );

		return $response->get_software_details();
	}

	/**
	 * Append the REST path and action to the server provided in the Settings.
	 *
	 * If the provided server URL does not have http/https, https is assumed.
	 *
	 * https://my-domain.com/wp-json/slswc/v1/product
	 */
	protected function get_base_url( string $action ): string {
		$licence_server_host = $this->settings->get_licence_server_host();

		$scheme = wp_parse_url( $licence_server_host, PHP_URL_SCHEME ) ?? 'https';
		$host   = wp_parse_url( $licence_server_host, PHP_URL_HOST );
		$path   = wp_parse_url( $licence_server_host, PHP_URL_PATH );

		return trailingslashit( "{$scheme}://{$host}{$path}" ) . self::REMOTE_REST_API_BASE . $action;
	}

	/**
	 * Send a request to the server.
	 *
	 * @param   string $action activate|deactivate|check_update|product.
	 * @throws
	 */
	protected function server_request( string $action, string $type = License_Response::class ) {

		$request_info = array(
			'slug'        => $this->settings->get_plugin_slug(),
			'license_key' => $this->licence->get_licence_key(),
		);

		$request_info['domain'] = get_home_url();

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
			$response = wp_remote_get( $server_request_url, $request_options );
		} else {
			$type     = License_Response::class;
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
	 * @param \WP_Error|array $response
	 *
	 * @throws SLSWC_Exception_Abstract
	 */
	public function validate_response( array $request, $response ): void {

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

	/**
	 * TODO: semver compare.
	 */
	public function is_update_available( ?bool $refresh = null ): bool {
		return version_compare(
			$this->get_product_information( $refresh )?->get_version() ?? '0.0.0',
			get_plugins()[ $this->settings->get_plugin_basename() ]['Version'] ?? '0.0.0',
			'>'
		);
	}
}
