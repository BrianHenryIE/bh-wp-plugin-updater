<?php

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use Mockery;
use WP_Mock;

/**
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\WP_Includes\Cron
 */
class Cron_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * @covers ::get_update_check_cron_job_name
	 * @covers ::__construct
	 */
	public function test_get_update_check_cron_job_name(): void {

		$api = Mockery::mock( API_Interface::class )->makePartial();

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_slug' )->andReturn( 'my-plugin' );

		$sut = new Cron( $api, $settings );

		$result = $sut->get_update_check_cron_job_name();

		$this->assertEquals( 'my_plugin_update_check', $result );
	}

	/**
	 * @covers ::register_cron_job
	 */
	public function test_register_cron_job(): void {

		$api = Mockery::mock( API_Interface::class )->makePartial();

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_slug' )
				->twice()
				->andReturn( 'my-plugin' );

		$sut = new Cron( $api, $settings );

		WP_Mock::userFunction(
			'wp_next_scheduled',
			array(
				'times'  => 1,
				'args'   => array( 'my_plugin_update_check' ),
				'return' => false,
			)
		);

		WP_Mock::userFunction(
			'wp_schedule_event',
			array(
				'times'  => 1,
				'args'   => array(
					WP_Mock\Functions::type( 'int' ),
					'daily',
					'my_plugin_update_check',
				),
				'return' => null,
			)
		);

		$sut->register_cron_job();
	}


	/**
	 * @covers ::register_cron_job
	 */
	public function test_register_cron_job_already_registered(): void {

		$api = Mockery::mock( API_Interface::class )->makePartial();

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_slug' )
				->once()
				->andReturn( 'my-plugin' );

		$sut = new Cron( $api, $settings );

		WP_Mock::userFunction(
			'wp_next_scheduled',
			array(
				'times'  => 1,
				'args'   => array( 'my_plugin_update_check' ),
				'return' => true,
			)
		);

		WP_Mock::userFunction(
			'wp_schedule_event',
			array(
				'times' => 0,
			)
		);

		$sut->register_cron_job();
	}

	/**
	 * @covers ::handle_update_check_cron_job
	 */
	public function test_handle_update_check_cron_job(): void {

		$api = Mockery::mock( API_Interface::class )->makePartial();
		$api->shouldReceive( 'get_licence_details' )
				->once()
				->with( true );
		$api->shouldReceive( 'get_product_information' )
				->once()
				->with( true );

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();

		$sut = new Cron( $api, $settings );

		$sut->handle_update_check_cron_job();
	}
}
