<?php
/**
 * REST API for setting licence key, activating, deactivating and getting licence details and product information.
 *
 * {@see openapi/example-plugin-openapi.json} for the generated OpenAPI spec.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
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
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
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
						'required' => false, // i.e. can be null to remove it.
						'type'     => 'string',
					),
					'activate'    => array(
						'required' => false,
						// default false
						'type'     => 'boolean',
					),
				),
				'callback'            => array( $this, 'set_licence_key' ),
				'permission_callback' => fn() => current_user_can( 'manage_options' ),
			)
		);

		register_rest_route(
			$route_namespace,
			'/licence/activate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'activate_licence' ),
				'permission_callback' => fn() => current_user_can( 'manage_options' ),
			)
		);

		register_rest_route(
			$route_namespace,
			'/licence/deactivate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'deactivate_licence' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function get_licence_details( WP_REST_Request $request ): WP_REST_Response {
		$refresh = $request->get_param( 'refresh' ) === true;

		try {
			$licence = $this->api->get_licence_details( $refresh );
		} catch ( \Exception $exception ) {
			$result = array(
				'success' => false,
				'error'   => $exception::class,
				'message' => $exception->getMessage(),
			);

			return new WP_REST_Response( $result, 500 );
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
	 *
	 * @return array{refresh:array{description:string,type:string,required:bool}}
	 */
	public function get_licence_details_args(): array {
		$args = array();

		// Here we add our PHP representation of JSON Schema.
		$args['refresh'] = array(
			'description'  => esc_html__( 'Should the licence check perform a remote request to the licence server.', 'bh-wp-plugin-updater' ),
			'type'         => 'boolean',
			// 'validate_callback' => 'prefix_validate_my_arg',
			// 'sanitize_callback' => 'prefix_sanitize_my_arg',
				'required' => true,
			// default: false
		);

		return $args;
	}

	/**
	 * @return array{"$schema": string, title: string, type: string, properties:array{success: array{description: string, type: string}, message: array{description: string, type: string}, data: array{description: string, type: string, properties: array<string, array{description: string, type: array<string>|string, format: string}>}}}
	 */
	public function get_licence_response_schema(): array {
		return array(
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title'      => 'licence',
			'type'       => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties' => array(
				'success' => array(
					'description' => esc_html__( 'Was the request successful?', 'bh-wp-plugin-updater' ),
					'type'        => 'boolean',
				),
				'message' => array(
					'description' => esc_html__( 'A friendly message.', 'bh-wp-plugin-updater' ),
					'type'        => 'string',
				),
				'data'    => array(
					'description' => esc_html__( 'The licence data.', 'bh-wp-plugin-updater' ),
					'type'        => 'object',
					'properties'  => Licence::get_licence_object_schema_properties(),
				),
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
				'error'   => $exception::class,
				'message' => $exception->getMessage(),
			);

			return new WP_REST_Response( $result, 500 );
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

		try {
			$licence = $this->api->activate_licence();
		} catch ( \Exception $exception ) {
			$result = array(
				'success' => false,
				'error'   => $exception::class,
				'message' => $exception->getMessage(),
			);

			return new WP_REST_Response( $result, 500 );
		}

		$result = array(
			'success' => true,
			'message' => 'Licence activated.',
			'data'    => $licence,
		);

		return new WP_REST_Response( $result );
	}

	public function deactivate_licence( WP_REST_Request $request ): WP_REST_Response {

		try {
			$licence = $this->api->deactivate_licence();
		} catch ( \Exception $exception ) {
			$result = array(
				'success' => false,
				'error'   => $exception::class,
				'message' => $exception->getMessage(),
			);

			return new WP_REST_Response( $result, 500 );
		}

		$result = array(
			'success' => true,
			'message' => 'Licence deactivated.',
			'data'    => $licence,
		);

		return new WP_REST_Response( $result );
	}
}
