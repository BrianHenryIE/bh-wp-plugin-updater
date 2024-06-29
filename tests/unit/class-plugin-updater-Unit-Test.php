<?php
/**
 * Tests main library singleton.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use Psr\Log\NullLogger;
use WP_Mock;

/**
 * @coversDefaultClass  \BrianHenryIE\WP_Plugin_Updater\Plugin_Updater
 */
class Plugin_Updater_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		parent::tearDown();
		\Patchwork\restoreAll();
	}

	/**
	 * @covers ::get_instance
	 */
	public function test_get_instance_exception_without_settings(): void {

		$this->expectException( \Exception::class );

		Plugin_Updater::get_instance( null );
	}

	/**
	 * @covers ::get_instance
	 */
	public function test_get_instance_exception(): void {

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();

		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'plugin_slug_licence' );
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'plugin-slug/plugin-slug.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'plugin-slug' );

		WP_Mock::userFunction( 'get_option' );

		// Prevents code-coverage counting, and removes the need to define the WordPress functions that are used in that class.
		\Patchwork\redefine(
			array( Actions::class, '__construct' ),
			function ( $api, $settings, $logger ) {}
		);

		$instance = Plugin_Updater::get_instance( $settings );

		$result = $instance->get_licence_details( false );

		$this->assertEquals( 'unknown', $result->get_status() );
	}
}
