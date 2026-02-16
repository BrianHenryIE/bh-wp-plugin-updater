<?php

namespace BrianHenryIE\WP_Plugin_Updater\Model;

use BrianHenryIE\WP_Plugin_Updater\Unit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers
 */
class Plugin_Headers_Unit_Test extends Unit_Testcase {

	/**
	 * @covers ::get_requires_plugins
	 */
	public function test_get_requires_plugins() {
		$sut = new Plugin_Headers( array( 'RequiresPlugins' => 'a-plugin, another-plugin' ) );

		$result = $sut->get_requires_plugins();

		$this->assertCount( 2, $result );

		$this->assertEquals( 'a-plugin', $result[0] );
	}

	/**
	 * @covers ::get_requires_plugins
	 */
	public function test_get_requires_plugins_not_set() {
		$sut = new Plugin_Headers( array() );

		$result = $sut->get_requires_plugins();

		$this->assertEmpty( $result );
	}
}
