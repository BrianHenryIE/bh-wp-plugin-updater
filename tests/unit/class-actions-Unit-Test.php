<?php
/**
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_SLSWC_Client\Admin\Admin_Assets;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\CLI;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\Cron;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\Rest;
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
	 * @covers ::__construct
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

	/**
	 * @covers ::add_cron_hooks
	 */
	public function test_cron_hooks(): void {
		$api      = Mockery::mock( API_Interface::class )->makePartial();
		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'a-plugin/a-plugin.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'a-plugin' );

		WP_Mock::expectActionAdded(
			'activate_a-plugin',
			array( new AnyInstance( Cron::class ), 'register_cron_job' )
		);

		WP_Mock::expectActionAdded(
			'a_plugin_update_check',
			array( new AnyInstance( Cron::class ), 'handle_update_check_cron_job' )
		);

		$logger = new ColorLogger();
		new Actions( $api, $settings, $logger );
	}

	/**
	 * @covers ::add_rest_hooks
	 */
	public function test_rest_hooks(): void {
		$api      = Mockery::mock( API_Interface::class )->makePartial();
		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'a-plugin/a-plugin.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'a-plugin' );

		WP_Mock::expectActionAdded(
			'rest_api_init',
			array( new AnyInstance( Rest::class ), 'register_routes' )
		);

		$logger = new ColorLogger();
		new Actions( $api, $settings, $logger );
	}

	/**
	 * @covers ::add_cli_hooks
	 */
	public function test_cli_hooks(): void {
		$api      = Mockery::mock( API_Interface::class )->makePartial();
		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'a-plugin/a-plugin.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'a-plugin' );

		WP_Mock::expectActionAdded(
			'cli_init',
			array( new AnyInstance( CLI::class ), 'register_commands' )
		);

		$logger = new ColorLogger();
		new Actions( $api, $settings, $logger );
	}
}
