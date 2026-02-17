<?php
/**
 * Adds a REST endpoint for querying transients.
 *
 * Like `/wp/v2/settings`
 *
 * But `bh-wp-plugin-updater/v1/transients`.
 */

namespace BrianHenryIE\WP_Plugin_Updater\Development_Plugin\Rest;

use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class Transients_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'bh-wp-plugin-updater/v1';
		$this->rest_base = 'transients';
	}

	/**
	 * @hooked rest_api_init
	 */
	public function register_routes(): void {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<transient>[\w\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
					'args'                => array(
						'value'      => array(
							'description' => 'The value to store.',
							'required'    => true,
						),
						'expiration' => array(
							'description' => 'Seconds until expiration. 0 means no expiration.',
							'type'        => 'integer',
							'required'    => false,
							'default'     => 0,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/network/(?P<transient>[\w\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_network_item' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_network_item' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
					'args'                => array(
						'value'      => array(
							'description' => 'The value to store.',
							'required'    => true,
						),
						'expiration' => array(
							'description' => 'Seconds until expiration. 0 means no expiration.',
							'type'        => 'integer',
							'required'    => false,
							'default'     => 0,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_network_item' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
				),
			)
		);
	}

	public function get_item( $request ): WP_REST_Response {
		$transient = $request->get_param( 'transient' );
		$value     = get_transient( $transient );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'transient' => $transient,
					'value'     => $value,
					'exists'    => false !== $value,
				),
			)
		);
	}

	public function create_item( $request ): WP_REST_Response {
		$transient  = $request->get_param( 'transient' );
		$value      = $request->get_param( 'value' );
		$expiration = (int) $request->get_param( 'expiration' );

		$result = set_transient( $transient, $value, $expiration );

		return new WP_REST_Response(
			array(
				'success' => $result,
				'data'    => array(
					'transient' => $transient,
				),
			)
		);
	}

	public function delete_item( $request ): WP_REST_Response {
		$transient = $request->get_param( 'transient' );
		$result    = delete_transient( $transient );

		return new WP_REST_Response(
			array(
				'success' => $result,
				'data'    => array(
					'transient' => $transient,
				),
			)
		);
	}

	public function get_network_item( $request ): WP_REST_Response {
		$transient = $request->get_param( 'transient' );
		$value     = get_site_transient( $transient );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'transient' => $transient,
					'value'     => $value,
					'exists'    => false !== $value,
				),
			)
		);
	}

	public function create_network_item( $request ): WP_REST_Response {
		$transient  = $request->get_param( 'transient' );
		$value      = $request->get_param( 'value' );
		$expiration = (int) $request->get_param( 'expiration' );

		$result = set_site_transient( $transient, $value, $expiration );

		return new WP_REST_Response(
			array(
				'success' => $result,
				'data'    => array(
					'transient' => $transient,
				),
			)
		);
	}

	public function delete_network_item( $request ): WP_REST_Response {
		$transient = $request->get_param( 'transient' );
		$result    = delete_site_transient( $transient );

		return new WP_REST_Response(
			array(
				'success' => $result,
				'data'    => array(
					'transient' => $transient,
				),
			)
		);
	}
}
