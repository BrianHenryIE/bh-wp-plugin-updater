<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Plugin_Updater\Licence;
use Psr\Http\Message\ResponseInterface;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\GitHub
 */
class GitHub_WPUnit_Test extends \lucatume\WPBrowser\TestCase\WPTestCase {

	public function test_check_update() {

		$http_client    = new \PsrMock\Psr18\Client();
		$streamFactory  = new \PsrMock\Psr17\StreamFactory();
		$responseStream = $streamFactory->createStream(
			json_encode(
				json_decode(
					file_get_contents( codecept_root_dir( 'tests/_data/github/releases.json' ) )
				)
			)
		);
		$response       = ( new \PsrMock\Psr7\Response() )
			->withBody( $responseStream )
			->withHeader( 'Content-Type', 'application/json' );

		$http_client->addResponse(
			'GET',
			'https://api.github.com/repos/BrianHenryIE/bh-wp-autologin-urls/releases',
			$response
		);

		$settings = \Mockery::mock( \BrianHenryIE\WP_Plugin_Updater\Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_server_host' )->andReturn( 'https://github.com/BrianHenryIE/bh-wp-autologin-urls' );
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'bh-wp-autologin-urls/bh-wp-autologin-urls.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'bh-wp-autologin-urls' );

		add_filter(
			'pre_http_request',
			function ( $pre, $parsed_args, $url ) {
				switch ( $url ) {
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/CHANGELOG.md':
						return array(
							'body'     => file_get_contents( codecept_root_dir( 'tests/_data/github/CHANGELOG.md' ) ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/README.txt':
						return array(
							'body'     => file_get_contents( codecept_root_dir( 'tests/_data/github/README.txt' ) ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/bh-wp-autologin-urls.php':
						return array(
							'body'     => file_get_contents( codecept_root_dir( 'tests/_data/github/bh-wp-autologin-urls.php' ) ),
							'response' => array( 'code' => 200 ),
						);
					default:
						$this->fail( 'Unexpected URL: ' . $url );
				}
			},
			10,
			3
		);

		$logger = new ColorLogger();

		$sut = new GitHub( $http_client, $settings, $logger );

		$result = $sut->get_remote_check_update( new Licence() );
		$this->assertEquals( '2.4.2', $result->get_version() );
	}

	public function test_plugin_information() {

		$http_client    = new \PsrMock\Psr18\Client();
		$streamFactory  = new \PsrMock\Psr17\StreamFactory();
		$responseStream = $streamFactory->createStream(
			json_encode(
				json_decode(
					file_get_contents( codecept_root_dir( 'tests/_data/github/releases.json' ) )
				)
			)
		);
		$response       = ( new \PsrMock\Psr7\Response() )
			->withBody( $responseStream )
			->withHeader( 'Content-Type', 'application/json' );

		$http_client->addResponse(
			'GET',
			'https://api.github.com/repos/BrianHenryIE/bh-wp-autologin-urls/releases',
			$response
		);

		$settings = \Mockery::mock( \BrianHenryIE\WP_Plugin_Updater\Settings_Interface::class )->makePartial();
		$settings->shouldReceive( 'get_licence_server_host' )->andReturn( 'https://github.com/BrianHenryIE/bh-wp-autologin-urls' );
		$settings->shouldReceive( 'get_plugin_basename' )->andReturn( 'bh-wp-autologin-urls/bh-wp-autologin-urls.php' );
		$settings->shouldReceive( 'get_plugin_slug' )->andReturn( 'bh-wp-autologin-urls' );

		add_filter(
			'pre_http_request',
			function ( $pre, $parsed_args, $url ) {
				switch ( $url ) {
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/CHANGELOG.md':
						return array(
							'body'     => file_get_contents( codecept_root_dir( 'tests/_data/github/CHANGELOG.md' ) ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/README.txt':
						return array(
							'body'     => file_get_contents( codecept_root_dir( 'tests/_data/github/README.txt' ) ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/bh-wp-autologin-urls.php':
						return array(
							'body'     => file_get_contents( codecept_root_dir( 'tests/_data/github/bh-wp-autologin-urls.php' ) ),
							'response' => array( 'code' => 200 ),
						);
					default:
						$this->fail( 'Unexpected URL: ' . $url );
				}
			},
			10,
			3
		);

		$logger = new ColorLogger();

		$sut = new GitHub( $http_client, $settings, $logger );

		$result = $sut->get_remote_product_information( new Licence() );
		$this->assertEquals( '2.4.2', $result->get_version() );
	}
}
