<?php
/**
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Plugin_Updater\Admin\Admin_Assets;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\CLI;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\Cron;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\Rest;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\WordPress_Updater;
use Mockery;
use WP_Mock;
use WP_Mock\Matcher\AnyInstance;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Actions
 */
class Actions_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();

		WP_Mock::passthruFunction( 'wp_unslash' );
		WP_Mock::passthruFunction( 'sanitize_key' );
		WP_Mock::passthruFunction( 'sanitize_url' );

		WP_Mock::userFunction( 'wp_parse_url' )
				->andReturn( 'bhwp.ie' );
	}

	protected function tearDown(): void {
		parent::tearDown();
		WP_Mock::tearDown();

		global $pagenow;
		unset( $pagenow );
		unset( $_GET['plugin'] );
	}

	protected function add_actions(): void {
		$api      = Mockery::mock( API_Interface::class )->makePartial();
		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'a-plugin/a-plugin.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'a-plugin' );
		$settings->shouldReceive( 'get_licence_server_host' )->andReturn( 'https://bhwp.ie' );

		$logger = new ColorLogger();
		new Actions( $api, $settings, $logger );
	}

	/**
	 * @covers ::add_assets_hooks
	 * @covers ::__construct
	 */
	public function test_admin_hooks(): void {
		global $pagenow;
		$pagenow        = 'plugin-install.php';
		$_GET['plugin'] = 'a-plugin';

		WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_styles' )
		);

		WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_script' )
		);

		$this->add_actions();
	}

	/**
	 * @covers ::add_cron_hooks
	 */
	public function test_cron_hooks(): void {
		WP_Mock::expectActionAdded(
			'activate_a-plugin',
			array( new AnyInstance( Cron::class ), 'register_cron_job' )
		);

		WP_Mock::expectActionAdded(
			'a_plugin_update_check',
			array( new AnyInstance( Cron::class ), 'handle_update_check_cron_job' )
		);

		$this->add_actions();
	}

	/**
	 * @covers ::add_rest_hooks
	 */
	public function test_rest_hooks(): void {
		WP_Mock::expectActionAdded(
			'rest_api_init',
			array( new AnyInstance( Rest::class ), 'register_routes' )
		);

		$this->add_actions();
	}

	/**
	 * @covers ::add_cli_hooks
	 */
	public function test_cli_hooks(): void {
		WP_Mock::expectActionAdded(
			'cli_init',
			array( new AnyInstance( CLI::class ), 'register_commands' )
		);

		$this->add_actions();
	}

	/**
	 * @covers ::add_wordpress_updater_hooks
	 */
	public function test_wordpress_updater_hooks(): void {
		WP_Mock::expectFilterAdded(
			'pre_set_site_transient_update_plugins',
			array( new AnyInstance( WordPress_Updater::class ), 'detect_force_update' ),
			10,
			2
		);

		WP_Mock::expectFilterAdded(
			'update_plugins_bhwp.ie',
			array( new AnyInstance( WordPress_Updater::class ), 'add_update_information' ),
			10,
			4
		);

		$this->add_actions();
	}
}
