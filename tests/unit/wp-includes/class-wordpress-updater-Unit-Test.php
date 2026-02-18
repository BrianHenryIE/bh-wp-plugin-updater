<?php

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use BrianHenryIE\WP_Plugin_Updater\Unit_Testcase;
use Mockery;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\WP_Includes\WordPress_Updater
 */
class WordPress_Updater_Unit_Test extends Unit_Testcase {

	/**
	 * When the value passed to the set transient function is empty, that implies the transient was deleted
	 * in order to force an update check. In this case, a synchronous HTTP request is made to the API.
	 *
	 * @covers ::on_set_transient_update_plugins
	 */
	public function test_force_update(): void {

		$api = Mockery::mock( API_Interface::class )->makePartial();
		$api->expects( 'schedule_immediate_background_update' )->never();

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_basename' )->andReturn( 'test-plugin/test-plugin.php' )->times( 3 );
		// $settings->expects( 'get_plugin_slug' )->andReturn( 'test-plugin' );

		$logger = $this->logger;

		$sut = new WordPress_Updater( $api, $settings, $logger );

		$value               = new \stdClass();
		$value->last_checked = time();

		\WP_Mock::userFunction( 'is_admin' )
				->once()
				->andReturnFalse();

		/**
		 * @see https://github.com/10up/wp_mock/issues/157
		 */
		// \WP_Mock::userFunction( 'remove_filter' )
		// ->once()
		// ->with( 'pre_set_site_transient_update_plugins', array( $sut, 'on_set_transient_update_plugins' ) );

		$sut->on_set_transient_update_plugins( $value, 'update_plugins' );

		$plugin_data = array();
		$plugin_file = 'test-plugin/test-plugin.php';
		$locales     = array();

		$api->expects( 'get_check_update' )->once()->with( true );

		$sut->add_update_information( false, $plugin_data, $plugin_file, $locales );
	}

	/**
	 * If an admin opens plugins.php and there is no update transient, schedule a background update.
	 *
	 * @covers ::on_set_transient_update_plugins
	 */
	public function test_force_update_immediately(): void {

		$api = Mockery::mock( API_Interface::class )->makePartial();
		$api->expects( 'schedule_immediate_background_update' )->once();

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_basename' )->andReturn( 'test-plugin/test-plugin.php' )->times( 3 );
		// $settings->expects( 'get_plugin_slug' )->andReturn( 'test-plugin' );

		$logger = $this->logger;

		$sut = new WordPress_Updater( $api, $settings, $logger );

		$value               = new \stdClass();
		$value->last_checked = time();

		/**
		 * @see https://github.com/10up/wp_mock/issues/157
		 */
		// \WP_Mock::userFunction( 'remove_filter' )
		// ->once()
		// ->with( 'pre_set_site_transient_update_plugins', array( $sut, 'on_set_transient_update_plugins' ) );

		\WP_Mock::userFunction( 'is_admin' )
				->once()
				->andReturnTrue();

		$sut->on_set_transient_update_plugins( $value, 'update_plugins' );

		$plugin_data = array();
		$plugin_file = 'test-plugin/test-plugin.php';
		$locales     = array();

		$api->expects( 'get_check_update' )->once()->with( false );

		$sut->add_update_information( false, $plugin_data, $plugin_file, $locales );
	}
}
