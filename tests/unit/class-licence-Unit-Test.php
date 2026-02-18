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

		$this->markTestSkipped( 'This fails because of milli/nanosecond precision, please rewrite to cover the important regerssions' );

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
				'expiry_date'   => $licence->last_updated,
				'last_updated'  => $licence->last_updated,
				'purchase_date' => null,
				'order_link'    => null,
				'auto_renews'   => null,
				'renewal_link'  => null,
			)
		);

		$this->assertEquals( $expected, json_encode( $licence ) );
	}
}
