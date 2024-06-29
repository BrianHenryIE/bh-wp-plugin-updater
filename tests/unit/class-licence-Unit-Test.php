<?php

namespace BrianHenryIE\WP_Plugin_Updater;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Licence
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

	public function test_serialize(): void {
		$this->markTestIncomplete( 'incomplete until we settle on what properties licence should have' );

		$licence = new Licence();
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expiry_date( new \DateTimeImmutable() );

		$expected = substr(
			serialize(
				array(
					'licence_key'  => 'abc123',
					'status'       => 'active',
					'expires'      => $licence->get_last_updated()?->format( \DateTimeInterface::ATOM ),
					'last_updated' => $licence->get_last_updated()?->format( \DateTimeInterface::ATOM ),
				)
			),
			3
		);

		$this->assertStringContainsString( $expected, serialize( $licence ) );
	}

	public function test_json_serialize(): void {
		$licence = new Licence();
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expiry_date( new \DateTimeImmutable() );

		$expected = json_encode(
			array(
				'licence_key'   => 'abc123',
				'status'        => 'active',
				'last_updated'  => $licence->get_last_updated()?->format( \DateTimeInterface::ATOM ),
				'purchase_date' => null,
				'order_link'    => null,
				'expiry_date'   => $licence->get_last_updated()?->format( \DateTimeInterface::ATOM ),
				'auto_renews'   => null,
				'renewal_link'  => null,
			)
		);

		$this->assertEquals( $expected, json_encode( $licence ) );
	}
}
