<?php

namespace BrianHenryIE\WP_Plugin_Updater\Exception;

use BrianHenryIE\WP_Plugin_Updater\Unit_Testcase;
use WP_Mock;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Exception\Licence_Key_Not_Set_Exception
 */
class Licence_Key_Not_Set_Exception_Unit_Test extends Unit_Testcase {

	public function test_calls_translation_for_default_message(): void {

		WP_Mock::userFunction( '__' )
				->once()
				->with( Licence_Key_Not_Set_Exception::MESSAGE, 'bh-wp-plugin-updater' )
				->andReturnSelf();

		new Licence_Key_Not_Set_Exception();
	}

	public function test_does_not_translate_custom_message(): void {

		WP_Mock::userFunction( '__' )
				->never();

		new Licence_Key_Not_Set_Exception( 'custom error message' );
	}
}
