<?php

namespace BrianHenryIE\WP_Plugin_Updater;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Licence
 */
class Licence_Unit_Test extends Unit_Testcase {

	public function test_serialize(): void {
		$this->markTestIncomplete( 'incomplete until we settle on what properties licence should have' );

		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		$expected = substr(
			serialize(
				array(
					'licence_key'  => 'abc123',
					'status'       => 'active',
					'expires'      => $licence->get_last_updated()?->format( DateTimeInterface::ATOM ),
					'last_updated' => $licence->get_last_updated()?->format( DateTimeInterface::ATOM ),
				)
			),
			3
		);

		$this->assertStringContainsString( $expected, serialize( $licence ) );
	}

	public function test_json_serialize(): void {
		$licence = new Licence(
			licence_key: 'abc123',
			status: 'active',
			expiry_date: new DateTimeImmutable(),
			last_updated: new DateTimeImmutable(),
		);

		$expected = json_encode(
			array(
				'licence_key'   => 'abc123',
				'status'        => 'active',
				'last_updated'  => $licence->get_last_updated()?->format( DateTimeInterface::ATOM ),
				'purchase_date' => null,
				'order_link'    => null,
				'expiry_date'   => $licence->get_last_updated()?->format( DateTimeInterface::ATOM ),
				'auto_renews'   => null,
				'renewal_link'  => null,
			)
		);

		$this->assertEquals( $expected, json_encode( $licence ) );
	}
}
