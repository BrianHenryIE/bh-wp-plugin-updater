<?php

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use Mockery;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\WP_Includes\WordPress_Updater
 */
class WordPress_Updater_Unit_Test extends \Codeception\Test\Unit {

	/**
	 * When the value passed to the set transient function is empty, that implies the transient was deleted
	 * in order to force an update check. In this case, a synchronous HTTP request is made to the API.
	 *
	 * @covers ::detect_force_update
	 */
	public function test_force_update(): void {

		$api = Mockery::mock( API_Interface::class )->makePartial();

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_plugin_basename' )->andReturn( 'test-plugin/test-plugin.php' );
		$settings->expects( 'get_plugin_slug' )->andReturn( 'test-plugin' );

		$logger = new ColorLogger();

		$sut = new WordPress_Updater( $api, $settings, $logger );

		$value               = new \stdClass();
		$value->last_checked = time();

		/**
		 * @see https://github.com/10up/wp_mock/issues/157
		 */
		\WP_Mock::userFunction( 'remove_filter' )
				->once()
				->with( 'pre_set_site_transient_update_plugins', array( $sut, 'detect_force_update' ) );

		$sut->detect_force_update( $value, 'update_plugins' );

		$plugin_data = array();
		$plugin_file = 'test-plugin/test-plugin.php';
		$locales     = array();

		$api->expects( 'get_check_update' )->once()->with( true );

		$sut->add_update_information( false, $plugin_data, $plugin_file, $locales );
	}
}
