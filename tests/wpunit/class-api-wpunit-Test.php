<?php

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\Server\Product;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\API
 */
class API_WPUnit_Test extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * @covers ::activate_licence
	 */
	public function test_activate_licence() {
		$this->markTestIncomplete();

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'bh_wp_autologin_urls_licence' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://updatestest.bhwp.ie' );
		$settings->expects( 'get_plugin_slug' )
				->andReturn( 'a-plugin' );

		$logger = new NullLogger();

		$api = new API( $settings, $logger );

		$licence_key = 'ffa19a46c4202cf1dac17b8b556deff3f2a3cc9a';

		/** @var Licence $result */
		$result = $api->activate_licence( $licence_key );

		$this->assertEquals( 'active', $result->get_status() );
	}

	public function test_deactivate_licence(): void {
		$this->markTestIncomplete();

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://updatestest.bhwp.ie' );
		$settings->expects( 'get_plugin_slug' )
				->andReturn( 'a-plugin' );

		$licence = \Mockery::mock( Licence::class );
		$licence->expects( 'get_licence_key' )
				->andReturn( 'ffa19a46c4202cf1dac17b8b556deff3f2a3cc9a' );
		$licence->expects( 'set_status' )->with( 'deactivated' );
		$licence->expects( 'set_expires' );
		$licence->expects( 'set_last_updated' );

		add_filter(
			'pre_option_a_plugin_licence',
			function () use ( $licence ) {
				return $licence;
			}
		);

		$logger = new NullLogger();

		$api = new API( $settings, $logger );

		/** @var Licence $result */
		$api->deactivate_licence();
	}

	public function test_get_product_information(): void {
		$this->markTestIncomplete();

		$settings = \Mockery::mock( Settings_Interface::class )->makePartial();
		$settings->expects( 'get_licence_data_option_name' )
				->andReturn( 'a_plugin_licence' );
		$settings->expects( 'get_plugin_information_option_name' )
				->andReturn( 'a_plugin_plugin_information' );
		$settings->expects( 'get_licence_server_host' )
				->andReturn( 'https://updatestest.bhwp.ie' );
		$settings->expects( 'get_plugin_slug' )
				->andReturn( 'a-plugin' );

		$logger = new NullLogger();

		$api = new API( $settings, $logger );

		/** @var Product $result */
		$result = $api->get_product_information();
	}
}
