<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC;

use BrianHenryIE\WP_Plugin_Updater\Exception\Licence_Does_Not_Exist_Exception;
use BrianHenryIE\WP_Plugin_Updater\Exception\Max_Activations_Exception;
use BrianHenryIE\WP_Plugin_Updater\Exception\Slug_Not_Found_On_Server_Exception;
use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model\Product;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use BrianHenryIE\WP_Plugin_Updater\WPUnit_Testcase;
use DateTimeImmutable;
use Mockery;
use Psr\Log\NullLogger;
use Throwable;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\SLSWC
 */
class SLSWC_WPUnit_Test extends WPUnit_Testcase {

	/**
	 * @covers ::activate_licence
	 * @covers ::server_request
	 * @covers ::validate_response
	 */
	public function test_activate_licence(): void {

		$body          = $this->get_fixture_as_string( 'tests/_data/slswc/activate-success.json' );
		$response_code = 200;

		add_filter(
			'pre_http_request',
			fn() => array(
				'body'     => $body,
				'response' => array( 'code' => $response_code ),
			)
		);

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_slug' )->zeroOrMoreTimes();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://whatever.127' );

		$logger = $this->logger;

		$sut = new SLSWC( $settings, $logger );

		/** @var Licence $result */
		$result = $sut->activate_licence( $licence );

		$this->assertEquals( 'active', $result->status );
	}

	/**
	 * TODO: This does not communicate to the user that the licence was already activated.
	 *
	 * @covers ::activate_licence
	 * @covers ::server_request
	 * @covers ::validate_response
	 */
	public function test_activate_licence_already_activated(): void {

		$body          = $this->get_fixture_as_string( 'tests/_data/slswc/activate-success.json' );
		$response_code = 200;

		add_filter(
			'pre_http_request',
			fn() => array(
				'body'     => $body,
				'response' => array( 'code' => $response_code ),
			)
		);

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_slug' )->zeroOrMoreTimes();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://whatever.127' );

		$logger = $this->logger;

		$sut = new SLSWC( $settings, $logger );

		/** @var Licence $result */
		$result = $sut->activate_licence( $licence );

		$this->assertEquals( 'active', $result->status );
	}

	public function test_deactivate_licence(): void {

		$body          = $this->get_fixture_as_string( 'tests/_data/slswc/deactivate-success.json' );
		$response_code = 200;

		add_filter(
			'pre_http_request',
			fn() => array(
				'body'     => $body,
				'response' => array( 'code' => $response_code ),
			)
		);

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_slug' )->zeroOrMoreTimes();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://whatever.127' );

		$logger = $this->logger;

		$sut = new SLSWC( $settings, $logger );

		/** @var Licence $result */
		$result = $sut->deactivate_licence( $licence );

		$this->assertEquals( 'deactivated', $result->status );
	}

	/**
	 * @covers ::get_remote_product_information
	 * @covers ::server_request
	 * @covers ::validate_response
	 */
	public function test_get_product_information(): void {
		$body          = $this->get_fixture_as_string( 'tests/_data/slswc/get-product-information-success.json' );
		$response_code = 200;

		add_filter(
			'pre_http_request',
			fn() => array(
				'body'     => $body,
				'response' => array( 'code' => $response_code ),
			)
		);

		$licence = new Licence(
			licence_key: '87486a5c45612f31ffdeb77506d20d4d3a157d37',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		update_option( 'a_plugin_licence', $licence );

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_plugin_information_option_name' )
				->andReturn( 'a_plugin_plugin_information' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://updatestest.bhwp.ie' );
		$settings->expects( 'get_plugin_slug' )
				->andReturn( 'test-plugin' );
		$settings->expects( 'get_plugin_name' )
				->andReturn( 'Test Plugin' );

		$logger = new NullLogger();

		$sut = new SLSWC( $settings, $logger );

		$result = $sut->get_remote_product_information( $licence );

		$this->assertEquals( 'test-plugin', $result?->slug );
	}

	/**
	 * @covers ::get_remote_check_update
	 * @covers ::server_request
	 * @covers ::validate_response
	 */
	public function test_check_update_success(): void {

		$body          = $this->get_fixture_as_string( 'tests/_data/slswc/check-update-success.json' );
		$response_code = 200;

		add_filter(
			'pre_http_request',
			fn() => array(
				'body'     => $body,
				'response' => array( 'code' => $response_code ),
			)
		);

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_plugin_information_option_name' )
				->andReturn( 'a_plugin_plugin_information' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://updatestest.bhwp.ie' );
		$settings->expects( 'get_plugin_slug' )
				->andReturn( 'test-plugin' );

		$logger = new NullLogger();

		$sut = new SLSWC( $settings, $logger );

		$result = $sut->get_remote_check_update( $licence );

		$this->assertEquals( '1.2.0', $result?->version );
	}

	// "Invalid parameter(s): slug" happens when the licence key is correct but does not match the plugin slug.

	// deactivating a licence twice results in the same success response from the server.

	/**
	 * Slug_Not_Found_On_Server_Exception::class
	 */
	public function test_validate_response_licence_not_found(): void {
		$this->expectExceptionForResponse(
			'tests/_data/slswc/invalid-parameters-licence-key-slug.json',
			400,
			Licence_Does_Not_Exist_Exception::class
		);
	}

	public function test_validate_response_max_activations(): void {
		$this->expectExceptionForResponse(
			'tests/_data/slswc/max-activations-reached.json',
			200,
			Max_Activations_Exception::class
		);
	}

	/**
	 * @param string                         $response_body_relative_filepath
	 * @param int                            $response_code
	 * @param string|class-string<Throwable> $expected_exception_class
	 */
	public function expectExceptionForResponse( string $response_body_relative_filepath, int $response_code, string $expected_exception_class ): void {
		$this->expectException( $expected_exception_class );

		$body = $this->get_fixture_as_string( $response_body_relative_filepath );

		add_filter(
			'pre_http_request',
			fn() => array(
				'body'     => $body,
				'response' => array( 'code' => $response_code ),
			)
		);

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		update_option( 'a_plugin_licence', $licence );

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_slug' )->zeroOrMoreTimes();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://whatever.127' );

		$logger = $this->logger;

		$sut = new SLSWC( $settings, $logger );

		$sut->activate_licence( $licence );
	}
}
