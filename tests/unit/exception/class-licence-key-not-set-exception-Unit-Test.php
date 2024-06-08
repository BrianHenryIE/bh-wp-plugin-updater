<?php

namespace BrianHenryIE\WP_SLSWC_Client\Exception;

use WP_Mock;

/**
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\Exception\Licence_Key_Not_Set_Exception
 */
class Licence_Key_Not_Set_Exception_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		WP_Mock::tearDown();
	}

	public function test_calls_translation_for_default_message(): void {

		WP_Mock::userFunction( '__' )
				->once()
				->with( Licence_Key_Not_Set_Exception::MESSAGE, 'bh-wp-slswc-client' );

		new Licence_Key_Not_Set_Exception();
	}

	public function test_does_not_translate_custom_message(): void {

		WP_Mock::userFunction( '__' )
				->never();

		new Licence_Key_Not_Set_Exception( 'custom error message' );
	}
}
