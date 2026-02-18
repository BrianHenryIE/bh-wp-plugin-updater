<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations;

use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use BrianHenryIE\WP_Plugin_Updater\Unit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Factory
 */
class Integration_Factory_Unit_Test extends Unit_Testcase {

	public function test_logger_is_used(): void {

		$this->markTestIncomplete( 'I thought I could see the logger being used' );

		$logger = $this->logger;

		$settings = \Mockery::mock( Settings_Interface::class );

		$sut = new Integration_Factory( $logger );

		$integration = $sut->get_integration( $settings );

		$licence = new Licence(
			licence_key: 'abc123',
		);

		try {
			$integration->activate_licence( $licence );
		} catch ( \Exception ) {

		}

		$this->assertTrue( $logger->hasErrorRecords() );
	}
}
