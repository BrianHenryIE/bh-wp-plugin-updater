<?php

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model\Product;
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
		$sut    = new API( $settings, $logger );

		$settings->shouldReceive( 'get_plugin_information_option_name' )
			->once()
			->andReturn( 'plugin_slug_product_information' );

		$product = Mockery::mock( Product::class );

		WP_Mock::userFunction( 'get_option' )
				->once()
				->with( 'plugin_slug_product_information', null )
				->andReturn( $product );

		$product->shouldReceive( 'get_version' )
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
				->andReturn( $licence );

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$logger = new ColorLogger();
		$sut    = new API( $settings, $logger );

		$this->assertEquals( 'abc123', $sut->get_licence_details( false )->get_licence_key() );
	}
}
