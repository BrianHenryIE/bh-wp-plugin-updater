<?php
/**
 * @see $data = rest_get_server()->get_data_for_routes( $routes, 'help' );
 */

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use lucatume\WPBrowser\TestCase\WPRestApiTestCase;
use Mockery;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\WP_Includes\Rest
 */
class Rest_WPUnit_Test extends WPRestApiTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	protected function tearDown(): void {
		parent::tearDown();

		global $wp_rest_server;
		$wp_rest_server = null;
	}

	public function test_get_licence(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_rest_base' )->andReturn( 'a-plugin' );

		$api  = Mockery::mock( API_Interface::class )->makePartial();
		$rest = new Rest( $api, $settings );

		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
		do_action( 'rest_api_init' );

		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$licence = new Licence(
			licence_key: 'abc123'
		);

		wp_set_current_user( 1 );

		$api->shouldReceive( 'get_licence_details' )->andReturn( $licence );

		$request = new \WP_REST_Request( 'GET', '/a-plugin/v1/licence' );

		$response = rest_get_server()->dispatch( $request );

		// We need to encode->decode to get the data as an array in tests rather than the original object.
		$result = json_decode( wp_json_encode( $response->get_data() ) );

		// {success:bool, message:string, data:object}

		$this->assertSame( 'abc123', $result->data->licence_key );
	}

	public function test_set_licence(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_rest_base' )->andReturn( 'a-plugin' );

		$api  = Mockery::mock( API_Interface::class )->makePartial();
		$rest = new Rest( $api, $settings );

		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
		do_action( 'rest_api_init' );

		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'inactive',
		);

		wp_set_current_user( 1 );

		$api->shouldReceive( 'set_license_key' )->once()->with( 'abc123' )->andReturn( $licence );
		$api->shouldReceive( 'activate_licence' )->never();

		$request = new \WP_REST_Request( 'POST', '/a-plugin/v1/licence/set-key' );

		$request->set_body_params( array( 'licence_key' => 'abc123' ) );

		$response = rest_get_server()->dispatch( $request );

		// We need to encode->decode to get the data as an array in tests rather than the original object.
		$result = json_decode( wp_json_encode( $response->get_data() ) );

		// {success:bool, message:string, data:object}

		$this->assertSame( 'inactive', $result->data->status );
	}

	public function test_set_and_activate_licence(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_rest_base' )->andReturn( 'a-plugin' );

		$api  = Mockery::mock( API_Interface::class )->makePartial();
		$rest = new Rest( $api, $settings );

		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
		do_action( 'rest_api_init' );

		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
		);

		wp_set_current_user( 1 );

		$api->shouldReceive( 'set_license_key' )->once()->with( 'abc123' )->andReturn( $licence );
		$api->shouldReceive( 'activate_licence' )->once()->andReturn( $licence );

		$request = new \WP_REST_Request( 'POST', '/a-plugin/v1/licence/set-key' );

		$request->set_body_params(
			array(
				'licence_key' => 'abc123',
				'activate'    => true,
			)
		);

		$response = rest_get_server()->dispatch( $request );

		// We need to encode->decode to get the data as an array in tests rather than the original object.
		$result = json_decode( wp_json_encode( $response->get_data() ) );

		// {success:bool, message:string, data:object}

		$this->assertSame( 'active', $result->data->status );
	}


	public function test_activate_licence(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_rest_base' )->andReturn( 'a-plugin' );

		$api  = Mockery::mock( API_Interface::class )->makePartial();
		$rest = new Rest( $api, $settings );

		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
		do_action( 'rest_api_init' );

		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$licence = new Licence(
			licence_key: 'abc123',
		);

		wp_set_current_user( 1 );

		$api->shouldReceive( 'activate_licence' )->once()->andReturn( $licence );

		$request = new \WP_REST_Request( 'POST', '/a-plugin/v1/licence/activate' );

		$response = rest_get_server()->dispatch( $request );

		// We need to encode->decode to get the data as an array in tests rather than the original object.
		$result = json_decode( wp_json_encode( $response->get_data() ) );

		// {success:bool, message:string, data:object}

		$this->assertSame( 'Licence activated.', $result->message );
	}


	public function test_deactivate_licence(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_rest_base' )->andReturn( 'a-plugin' );

		$api  = Mockery::mock( API_Interface::class )->makePartial();
		$rest = new Rest( $api, $settings );

		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
		do_action( 'rest_api_init' );

		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$licence = new Licence(
			licence_key: 'abc123',
		);

		wp_set_current_user( 1 );

		$api->shouldReceive( 'deactivate_licence' )->once()->andReturn( $licence );

		$request = new \WP_REST_Request( 'POST', '/a-plugin/v1/licence/deactivate' );

		$response = rest_get_server()->dispatch( $request );

		// We need to encode->decode to get the data as an array in tests rather than the original object.
		$result = json_decode( wp_json_encode( $response->get_data() ) );

		// {success:bool, message:string, data:object}

		$this->assertSame( 'Licence deactivated.', $result->message );
	}
}
