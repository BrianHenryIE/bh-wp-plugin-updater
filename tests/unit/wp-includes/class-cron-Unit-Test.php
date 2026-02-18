<?php

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use BrianHenryIE\WP_Plugin_Updater\Unit_Testcase;
use Mockery;
use Psr\Log\LoggerInterface;
use WP_Mock;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\WP_Includes\Cron
 */
class Cron_Unit_Test extends Unit_Testcase {

	protected function get_sut(
		?API_Interface $api = null,
		?Settings_Interface $settings = null,
		?LoggerInterface $logger = null,
	): Cron {
		return new Cron(
			api: $api ?? Mockery::mock( API_Interface::class ),
			settings: $settings ?? Mockery::mock( Settings_Interface::class ),
			logger: $logger ?? $this->logger,
		);
	}

	/**
	 * @covers ::get_update_check_cron_job_name
	 * @covers ::__construct
	 */
	public function test_get_update_check_cron_job_name(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_slug' )->andReturn( 'my-plugin' );

		$sut = $this->get_sut(
			settings: $settings
		);

		$result = $sut->get_update_check_cron_job_name();

		$this->assertEquals( 'my_plugin_update_check', $result );
	}

	/**
	 * @covers ::get_immediate_update_check_cron_job_name
	 */
	public function test_get_immediate_update_check_cron_job_name(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_slug' )->andReturn( 'test-plugin' );

		$sut = $this->get_sut(
			settings: $settings
		);

		$result = $sut->get_immediate_update_check_cron_job_name();

		$this->assertEquals( 'test_plugin_update_check_immediate', $result );
	}

	/**
	 * @covers ::register_cron_job
	 */
	public function test_register_cron_job(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_slug' )
				->once()
				->andReturn( 'my-plugin' );

		$sut = $this->get_sut(
			settings: $settings
		);

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

		WP_Mock::userFunction(
			'is_wp_error',
			array(
				'times'  => 1,
				'return' => false,
			)
		);

		$sut->register_cron_job();
	}


	/**
	 * @covers ::register_cron_job
	 */
	public function test_register_cron_job_already_registered(): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_slug' )
				->once()
				->andReturn( 'my-plugin' );

		$sut = $this->get_sut(
			settings: $settings
		);

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
		$api->shouldReceive( 'get_plugin_information' )
				->once()
				->with( true );
		$api->shouldReceive( 'get_check_update' )
				->once()
				->with( true );

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();

		$sut = $this->get_sut(
			api: $api,
			settings: $settings
		);

		$sut->handle_update_check_cron_job();
	}
}
