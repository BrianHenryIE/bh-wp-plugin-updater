<?php

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Factory_Interface;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use Mockery;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\API
 */
class API_WPUnit_Test extends \lucatume\WPBrowser\TestCase\WPTestCase {

	protected function get_mock_integration_factory( Integration_Interface $integration_mock ): Integration_Factory_Interface {
		$mock_integration_factory = Mockery::mock( Integration_Factory_Interface::class )->makePartial();
		$mock_integration_factory->shouldReceive( 'get_integration' )
								->andReturn( $integration_mock );
		return $mock_integration_factory;
	}

	/**
	 * I want to serialize a licence as an array, not as the typed class, which is liable to cause crashes if the data
	 * shape changes in the future.
	 *
	 * @covers ::save_licence_information
	 * @covers ::get_saved_licence_information
	 */
	public function test_save_licence_information(): void {
		$licence = new Licence();
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expiry_date( new \DateTimeImmutable() );

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		update_option( 'a_plugin_licence', $licence->__serialize() );

		$integration = Mockery::mock( Integration_Interface::class )->makePartial();
		$integration->expects( 'activate_licence' )->once()->andReturn( $licence );

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory( $integration ) );

		$result = $sut->activate_licence();

		$saved_licence = get_option( 'a_plugin_licence' );

		$this->assertEquals( $result->get_status(), $saved_licence['status'] );
	}

	/**
	 * @covers ::get_check_update
	 * @covers ::get_remote_check_update
	 */
	public function test_plugin_update_cache_set(): void {
		$plugin_update = new Plugin_Update(
			'id',
			'slug',
			'version',
			'url',
			'package',
			'tested',
			'8.0.0',
			true,
			array( 'icons' ),
			array( 'banners' ),
			array( 'banners_rtl' ),
			array( 'translations' )
		);

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );
		$settings->shouldReceive( 'get_check_update_option_name' )->andReturn( 'a_plugin_update' );

		$integration = Mockery::mock( Integration_Interface::class )->makePartial();
		$integration->expects( 'get_remote_check_update' )->once()->andReturn( $plugin_update );

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory( $integration ) );

		$result = $sut->get_check_update( true );

		$saved_plugin_update = get_option( 'a_plugin_update' );

		$this->assertEquals( $result->get_version(), $saved_plugin_update['version'] );
	}

	/**
	 * @covers ::get_check_update
	 * @covers ::get_cached_check_update
	 */
	public function test_plugin_update_cache_get(): void {
		$plugin_update = new Plugin_Update(
			'id',
			'slug',
			'version',
			'url',
			'package',
			'tested',
			'8.0.0',
			true,
			array( 'icons' ),
			array( 'banners' ),
			array( 'banners_rtl' ),
			array( 'translations' )
		);

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'a-plugin' );
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );
		$settings->shouldReceive( 'get_check_update_option_name' )->andReturn( 'a_plugin_update' );

		$integration = Mockery::mock( Integration_Interface::class )->makePartial();
		$integration->expects( 'get_remote_check_update' )->once()->andReturn( $plugin_update );

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory( $integration ) );

		$sut->get_check_update( true );

		$result = $sut->get_check_update( false );

		$this->assertEquals( $result->get_version(), $plugin_update->get_version() );
	}
}
