<?php
/**
 * @package brianhenryie/bh-wp-slswc-client
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\Admin\Admin_Assets;
use BrianHenryIE\WP_SLSWC_Client\Frontend\Frontend_Assets;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\I18n;
use WP_Mock\Matcher\AnyInstance;

/**
 * Class BH_WP_SLSWC_Client_Unit_Test
 *
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\BH_WP_SLSWC_Client
 */
class BH_WP_SLSWC_Client_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::set_locale
	 */
	public function test_set_locale_hooked(): void {

		\WP_Mock::expectActionAdded(
			'init',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

		$settings = $this->make( Settings::class );
		new BH_WP_SLSWC_Client( $settings );
	}

	/**
	 * @covers ::define_admin_hooks
	 */
	public function test_admin_hooks(): void {

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_styles' )
		);

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_scripts' )
		);

		$settings = $this->make( Settings::class );
		new BH_WP_SLSWC_Client( $settings );
	}

	/**
	 * @covers ::define_frontend_hooks
	 */
	public function test_frontend_hooks(): void {

		\WP_Mock::expectActionAdded(
			'wp_enqueue_scripts',
			array( new AnyInstance( Frontend_Assets::class ), 'enqueue_styles' )
		);

		\WP_Mock::expectActionAdded(
			'wp_enqueue_scripts',
			array( new AnyInstance( Frontend_Assets::class ), 'enqueue_scripts' )
		);

		$settings = $this->make( Settings::class );
		new BH_WP_SLSWC_Client( $settings );
	}
}
