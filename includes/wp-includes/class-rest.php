<?php

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use WP_REST_Request;
use WP_REST_Response;

class Rest {
	/**
	 * Constructor.
	 *
	 * @param API_Interface $api The main API class where the functionality is implemented.
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings
	) {
	}

	/**
	 * @hooked rest_api_init
	 */
	public function register_routes(): void {
		$route_namespace = "{$this->settings->get_rest_base()}/v1";

		register_rest_route(
			$route_namespace,
			'/licence',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_licence_details' ),
					'args'                => array(),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
				),
				'schema' => array( $this, 'get_licence_response_schema' ),
			),
		);

		register_rest_route(
			$route_namespace,
			'/licence/set-key',
			array(
				'methods'             => 'POST',
				'args'                => array(
					'licence_key' => array(
						'required' => false,
					),
					'activate'    => array(
						'required' => false,
						// default false
					),
				),
				'callback'            => array( $this, 'set_licence_key' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// register_rest_route(
		// $route_namespace,
		// '/licence/activate',
		// array(
		// 'methods'             => 'POST',
		// 'callback'            => array( $this, 'activate_licence' ),
		// 'permission_callback' => '__return_true',
		// )
		// );
		//
		// register_rest_route(
		// $route_namespace,
		// '/licence/deactivate',
		// array(
		// 'methods'             => 'POST',
		// 'callback'            => array( $this, 'deactivate_licence' ),
		// 'permission_callback' => '__return_true',
		// )
		// );
	}

	public function get_licence_details( WP_REST_Request $request ): WP_REST_Response {
		$refresh = $request->get_param( 'refresh' ) === true;

		try {
			$licence = $this->api->get_licence_details( $refresh );
		} catch ( \Exception $exception ) {
			$result = array(
				'success' => false,
				'error'   => get_class( $exception ),
				'message' => $exception->getMessage(),
			);

			return new WP_REST_Response( $result, $exception->get_http_status_code() );
		}

		$result = array(
			'success' => true,
			'message' => 'Licence details retrieved.',
			'data'    => $licence,
		);

		return new WP_REST_Response( $result );
	}

	/**
	 * The argument schema / the arguments required in the request
	 */
	public function get_licence_details_args(): array {
		$args = array();

		// Here we add our PHP representation of JSON Schema.
		$args['refresh'] = array(
			'description'  => esc_html__( 'Should the licence check perform a remote request to the licence server.', 'bh-wp-slswc-client' ),
			'type'         => 'boolean',
			// 'validate_callback' => 'prefix_validate_my_arg',
			// 'sanitize_callback' => 'prefix_sanitize_my_arg',
				'required' => true,
			// default: false
		);

		return $args;
	}

	/**
	 * @return array{schema:string,title:string,type:string,properties:array}
	 */
	public function get_licence_response_schema(): array {
		$schema = array(
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title'      => 'licence',
			'type'       => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties' => array(
				'success' => array(
					'description' => esc_html__( 'Was the request successful?', 'bh-wp-slswc-client' ),
					'type'        => 'boolean',
				),
				'message' => array(
					'description' => esc_html__( 'A friendly message.', 'bh-wp-slswc-client' ),
					'type'        => 'string',
				),
				'data'    => array(
					'description' => esc_html__( 'The licence data.', 'bh-wp-slswc-client' ),
					'type'        => 'object',
					'properties'  => $this->get_licence_object_schema_properties(),
				),
			),
		);

		return $schema;
	}
	protected function get_licence_object_schema_properties(): array {
		return array(
			'licence_key'   => array(
				'description' => esc_html__( 'The licence key.', 'bh-wp-slswc-client' ),
				'type'        => 'string',
		// 'minimum'          => 1, // TODO: Is there a set length the key will be?
		// 'exclusiveMinimum' => true,
		// 'maximum'          => 3,
		// 'exclusiveMaximum' => true,
			),
			'status'        => array(
				'description' => esc_html__( 'The licence status.', 'bh-wp-slswc-client' ),
				'type'        => 'string',
				// 'enum' => array(
				// 'invalid',
				// ),
			),
			'last_updated'  => array(
				'description' => esc_html__( 'The last time the license server was successfully contacted.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'purchase_date' => array(
				'description' => esc_html__( 'The date of original purchase.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'order_link'    => array(
				'description' => esc_html__( 'A link to the original order domain.com/my-account/orders/123.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'uri',
			),
			'expiry_date'   => array(
				'description' => esc_html__( 'The expiry date.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'auto_renews'   => array(
				'description' => esc_html__( 'Will the licence auto-renew?', 'bh-wp-slswc-client' ),
				'type'        => array( 'boolean', 'null' ),
			),
			'renewal_link'  => array(
				'description' => esc_html__( 'A link to domain.com to renew the licence.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'uri',
			),
		);
	}


	public function set_licence_key( WP_REST_Request $request ): WP_REST_Response {
		$licence_key      = $request->get_param( 'licence_key' );
		$activate_licence = (bool) $request->get_param( 'activate' );

		try {
			$licence = $this->api->set_license_key( $licence_key );
			if ( $activate_licence ) {
				$licence = $this->api->activate_licence();
			}
		} catch ( \Exception $exception ) {
			$result = array(
				'success' => false,
				'error'   => get_class( $exception ),
				'message' => $exception->getMessage(),
			);

			return new WP_REST_Response( $result, $exception->get_http_status_code() );
		}

		// assert( $licence->get_licence_key() === $licence_key );

		$result = array(
			'success' => true,
			'message' => 'Licence key set.',
			'data'    => $licence,
		);

		return new WP_REST_Response( $result );
	}

	public function activate_licence( WP_REST_Request $request ): WP_REST_Response {
		$licence_key = $request->get_param( 'licence_key' );

		$result = $this->api->activate_licence( sanitize_key( wp_unslash( $licence_key ) ) );

		return new WP_REST_Response( $result );
	}

	public function deactivate_licence( WP_REST_Request $request ): WP_REST_Response {
		$licence_key = $request->get_param( 'licence_key' );

		$result = $this->api->deactivate_licence();

		return new WP_REST_Response( $result );
	}
}
