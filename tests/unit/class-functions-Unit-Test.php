<?php

namespace BrianHenryIE\WP_SLSWC_Client;

class Functions_Unit_Test extends \Codeception\Test\Unit {

	public function test_str_underscore_to_dash(): void {
		$result = str_underscore_to_dash( 'string_with_underscores' );

		$this->assertEquals( 'string-with-underscores', $result );
	}

	public function test_str_dash_to_underscore(): void {
		$result = str_dash_to_underscore( 'string-with-dashes' );

		$this->assertEquals( 'string_with_dashes', $result );
	}
}
