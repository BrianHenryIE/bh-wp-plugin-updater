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
			'/licence-product',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_product_information' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$route_namespace,
			'/licence',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_licence_details' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$route_namespace,
			'/licence/set-key',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'set_licence_key' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			$route_namespace,
			'/licence/activate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'activate_licence' ),
				'permission_callback' => '__return_true',
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

	public function get_product_information( WP_REST_Request $request ): WP_REST_Response {
		$refresh = $request->get_param( 'refresh' );

		$result = $this->api->get_product_information();

		return new WP_REST_Response( $result );
	}

	public function get_licence_details( WP_REST_Request $request ): WP_REST_Response {
		$refresh = $request->get_param( 'refresh' );

		$result = $this->api->get_licence_details( $refresh );

		return new WP_REST_Response( $result );
	}


	public function set_licence_key( WP_REST_Request $request ): WP_REST_Response {
		$licence_key = $request->get_param( 'licence_key' );

		$result = $this->api->activate_licence( sanitize_key( wp_unslash( $licence_key ) ) );

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
