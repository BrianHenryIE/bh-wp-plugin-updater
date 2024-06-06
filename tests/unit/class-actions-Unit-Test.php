<?php
/**
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_SLSWC_Client\Admin\Admin_Assets;
use Mockery;
use WP_Mock;
use WP_Mock\Matcher\AnyInstance;

/**
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\Actions
 */
class Actions_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		WP_Mock::tearDown();
	}

	/**
	 * @covers ::add_assets_hooks
	 */
	public function test_admin_hooks(): void {

		WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_styles' )
		);

		WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_script' )
		);

		$api      = Mockery::mock( API_Interface::class )->makePartial();
		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'a-plugin/a-plugin.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'a-plugin' );
		$logger = new ColorLogger();
		new Actions( $api, $settings, $logger );
	}
}
