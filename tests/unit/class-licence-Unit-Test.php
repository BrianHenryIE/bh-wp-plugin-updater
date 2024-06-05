<?php

namespace BrianHenryIE\WP_SLSWC_Client;

/**
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\Licence
 */
class Licence_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	public function test_construct() {
		$serialized = json_encode(
			array(
				'licence_key'  => 'abc123',
				'status'       => 'active',
				'expires'      => ( new \DateTimeImmutable() )->format( \DateTimeInterface::ATOM ),
				'last_updated' => ( new \DateTimeImmutable() )->format( \DateTimeInterface::ATOM ),
			)
		);

		\WP_Mock::userFunction( 'get_option' )
			->with( 'a_plugin_licence' )
			->andReturn( $serialized );

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		$sut = new Licence( $settings );

		$this->assertEquals( 'abc123', $sut->get_licence_key() );
	}

	public function test_serialize(): void {

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		\WP_Mock::userFunction( 'get_option' )->once()->andReturnFalse();

		\WP_Mock::userFunction(
			'update_option',
		);

		$licence = new Licence( $settings );
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expires( new \DateTimeImmutable() );

		$expected = substr(
			serialize(
				array(
					'licence_key'  => 'abc123',
					'status'       => 'active',
					'expires'      => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
					'last_updated' => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
				)
			),
			3
		);

		$this->assertStringContainsString( $expected, serialize( $licence ) );
	}

	public function test_json_serialize(): void {
		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_data_option_name' )->andReturn( 'a_plugin_licence' );

		\WP_Mock::userFunction( 'get_option' )->once()->andReturnFalse();

		\WP_Mock::userFunction(
			'update_option',
		);

		$licence = new Licence( $settings );
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expires( new \DateTimeImmutable() );

		$expected = json_encode(
			array(
				'licence_key'  => 'abc123',
				'status'       => 'active',
				'expires'      => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
				'last_updated' => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
			)
		);

		$this->assertEquals( $expected, json_encode( $licence ) );
	}
}
