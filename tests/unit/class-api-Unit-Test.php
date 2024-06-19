<?php

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_SLSWC_Client\Integrations\Integration_Factory_Interface;
use BrianHenryIE\WP_SLSWC_Client\Integrations\Integration_Interface;
use BrianHenryIE\WP_SLSWC_Client\Model\Plugin_Update;
use Mockery;
use WP_Mock;

/**
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\API
 */
class API_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		parent::tearDown();
	}

	protected function get_mock_integration_factory( ?Integration_Interface $integration_mock = null ): Integration_Factory_Interface {
		$mock_integration_factory = Mockery::mock( Integration_Factory_Interface::class )->makePartial();
		$mock_integration_factory->shouldReceive( 'get_integration' )
								->andReturn( $integration_mock ?? Mockery::mock( Integration_Interface::class ) );
		return $mock_integration_factory;
	}

	/**
	 * TODO: Test some nonsense versions
	 *
	 * @return array{local_version: string, remote_version: string, is_update: bool}
	 */
	public static function versions_data_provider(): array {
		return array(
			array(
				'local_version'  => '1.0.0',
				'remote_version' => '2.0.0',
				'is_update'      => true,
			),
			array(
				'local_version'  => '2.0.0',
				'remote_version' => '2.0.0',
				'is_update'      => false,
			),
			array(
				'local_version'  => '2.0.0',
				'remote_version' => '1.0.0',
				'is_update'      => false,
			),
		);
	}

	/**
	 * @covers ::is_update_available
	 *
	 * @dataProvider versions_data_provider
	 */
	public function test_is_update_available( string $local_version, string $remote_version, bool $is_update ): void {

		$settings = Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_plugin_basename' )
				->andReturn( 'plugin-slug/plugin-slug.php' );

		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );
		WP_Mock::userFunction( 'get_option' )
				->with( 'a_plugin_licence', null )
				->once()
				->andReturnFalse();

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory() );

		$settings->shouldReceive( 'get_check_update_option_name' )
			->once()
			->andReturn( 'plugin_slug_check_update' );

		// Used in logging.
		$settings->shouldReceive( 'get_plugin_slug' )
			->zeroOrMoreTimes()
			->andReturn( 'plugin-slug' );

		$plugin_update = Mockery::mock( Plugin_Update::class );

		WP_Mock::userFunction( 'get_option' )
				->once()
				->with( 'plugin_slug_check_update', null )
				->andReturn( $plugin_update );

		$plugin_update->shouldReceive( 'get_version' )
				->once()
				->andReturn( $remote_version );

		$new_version_array = array(
			'plugin-slug/plugin-slug.php' => array(
				'Version' => $local_version,
			),
		);

		WP_Mock::userFunction( 'get_plugins' )
			->once()
			->andReturn( $new_version_array );

		$result = $sut->is_update_available( false );

		$this->assertEquals( $is_update, $result );
	}

	/**
	 * @covers ::get_licence_details
	 */
	public function test_get_licence_details() {
		$licence = new Licence();
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expiry_date( new \DateTimeImmutable() );

		\WP_Mock::userFunction( 'get_option' )
				->with( 'a_plugin_licence', null )
				->andReturn( $licence->serialize() );

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory() );

		$this->assertEquals( 'abc123', $sut->get_licence_details( false )->get_licence_key() );
	}

	/**
	 * @covers ::set_license_key
	 */
	public function test_set_licence_key(): void {
		$licence = new Licence();
		$licence->set_status( 'invalid' );

		\WP_Mock::userFunction( 'get_option' )
				->with( 'a_plugin_licence', null )
				->andReturn( $licence->serialize() );

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$mock_integration = \Mockery::mock( Integration_Interface::class )->makePartial();
		$mock_integration->shouldReceive( 'activate_licence' )->never();
		$mock_integration->shouldReceive( 'deactivate_licence' )->never();

		\WP_Mock::userFunction( 'update_option' )->once()
				->withArgs(
					function ( $option_name, $value ) {
						return is_array( $value )
						&& $value['licence_key'] === 'qwerty';
					}
				)
				->andReturnTrue();

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory( $mock_integration ) );

		$sut->set_license_key( 'qwerty' );
	}

	/**
	 * @covers ::set_license_key
	 */
	public function test_set_licence_key_should_deactivate_existing_licence(): void {
		$licence = new Licence();
		$licence->set_licence_key( 'qwerty' );
		$licence->set_status( 'active' );

		\WP_Mock::userFunction( 'get_option' )
				->with( 'a_plugin_licence', null )
				->andReturn( $licence->serialize() );

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$mock_integration = \Mockery::mock( Integration_Interface::class )->makePartial();
		$mock_integration->shouldReceive( 'activate_licence' )->never();
		$mock_integration->shouldReceive( 'deactivate_licence' )->once()
			->withArgs(
				function ( Licence $licence ) {
					return $licence->get_licence_key() === 'qwerty';
				}
			);

		\WP_Mock::userFunction( 'update_option' )->once()
				->withArgs(
					function ( $option_name, $value ) {
						return is_array( $value )
							&& $value['licence_key'] === 'abc123';
					}
				)
				->andReturnTrue();

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory( $mock_integration ) );

		$sut->set_license_key( 'abc123' );
	}
	/**
	 * @covers ::set_license_key
	 */
	public function test_set_licence_key_should_return_early_when_its_the_same_key(): void {
		$licence = new Licence();
		$licence->set_licence_key( 'abc123' );

		/**
		 * @see API::__construct
		 */
		\WP_Mock::userFunction( 'get_option' )
				->with( 'a_plugin_licence', null )
				->andReturn( $licence->serialize() );

		/**
		 * @see API::get_licence_details()
		 * @see API::get_saved_licence_information()
		 */
		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$mock_integration = \Mockery::mock( Integration_Interface::class )->makePartial();
		$mock_integration->shouldReceive( 'activate_licence' )->never();
		$mock_integration->shouldReceive( 'deactivate_licence' )->never();

		\WP_Mock::userFunction( 'update_option' )->never();

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger, $this->get_mock_integration_factory( $mock_integration ) );

		$sut->set_license_key( 'abc123' );
	}
}
