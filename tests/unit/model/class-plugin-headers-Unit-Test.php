<?php

namespace BrianHenryIE\WP_Plugin_Updater\Model;

use BrianHenryIE\WP_Plugin_Updater\Unit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers
 */
class Plugin_Headers_Unit_Test extends Unit_Testcase {

	/**
	 * @covers ::from_array
	 */
	public function test_get_requires_plugins(): void {
		$sut = Plugin_Headers::from_array(
			array(
				'Name'            => 'my-plugin',
				'RequiresPlugins' => 'a-plugin, another-plugin',
			)
		);

		$result = $sut->requires_plugins;

		$this->assertCount( 2, $result );

		$this->assertEquals( 'a-plugin', $result[0] );
		$this->assertEquals( 'another-plugin', $result[1] );
	}

	/**
	 * @covers ::from_array
	 */
	public function test_get_requires_plugins_not_set(): void {
		$sut = Plugin_Headers::from_array( array( 'Name' => 'the-plugin' ) );

		$result = $sut->requires_plugins;

		$this->assertEmpty( $result );
	}
}
