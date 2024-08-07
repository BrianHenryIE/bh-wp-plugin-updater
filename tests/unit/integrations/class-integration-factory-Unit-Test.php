<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Factory
 */
class Integration_Factory_Unit_Test extends \Codeception\Test\Unit {

	public function test_logger_is_used() {

		$this->markTestIncomplete( 'I thought I could see the logger being used' );

		$logger = new ColorLogger();

		$settings = \Mockery::mock( Settings_Interface::class );

		$sut = new Integration_Factory( $logger );

		$integration = $sut->get_integration( $settings );

		$licence = new Licence();
		$licence->set_licence_key( 'abc123' );

		try {
			$integration->activate_licence( $licence );
		} catch ( \Exception $exception ) {

		}

		$this->assertTrue( $logger->hasErrorRecords() );
	}
}
