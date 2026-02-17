<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub;

use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\WPUnit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\GitHub_Integration
 */
class GitHub_WPUnit_Test extends WPUnit_Testcase {

	/**
	 * @covers ::get_remote_check_update
	 */
	public function test_check_update(): void {

		$http_client     = new \PsrMock\Psr18\Client();
		$response_stream = $this->get_fixture_as_stream( 'tests/_data/github/releases.json' );
		$response        = ( new \PsrMock\Psr7\Response() )
			->withBody( $response_stream )
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
			function ( $pre, $parsed_args, string $url ) {
				switch ( $url ) {
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/CHANGELOG.md':
						return array(
							'body'     => $this->get_fixture_as_string( 'tests/_data/github/CHANGELOG.md' ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/README.txt':
						return array(
							'body'     => $this->get_fixture_as_string( 'tests/_data/github/README.txt' ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/bh-wp-autologin-urls.php':
						return array(
							'body'     => $this->get_fixture_as_string( 'tests/_data/github/bh-wp-autologin-urls.php' ),
							'response' => array( 'code' => 200 ),
						);
					default:
						$this->fail( 'Unexpected URL: ' . $url );
				}
			},
			10,
			3
		);

		$logger = $this->logger;

		$sut = new GitHub_Integration(
			$http_client,
			new \PsrMock\Psr17\RequestFactory(),
			new \PsrMock\Psr17\StreamFactory(),
			$settings,
			$logger
		);

		$result = $sut->get_remote_check_update( new Licence() );
		$this->assertEquals( '2.4.2', $result?->version );
	}

	/**
	 * @covers ::get_remote_product_information
	 */
	public function test_plugin_information(): void {

		$http_client     = new \PsrMock\Psr18\Client();
		$response_stream = $this->get_fixture_as_stream( 'tests/_data/github/releases.json' );
		$response        = ( new \PsrMock\Psr7\Response() )
			->withBody( $response_stream )
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
			function ( $pre, $parsed_args, string $url ) {
				switch ( $url ) {
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/CHANGELOG.md':
						return array(
							'body'     => $this->get_fixture_as_string( 'tests/_data/github/CHANGELOG.md' ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/README.txt':
						return array(
							'body'     => $this->get_fixture_as_string( 'tests/_data/github/README.txt' ),
							'response' => array( 'code' => 200 ),
						);
					case 'https://raw.githubusercontent.com/BrianHenryIE/bh-wp-autologin-urls/v2.4.2/bh-wp-autologin-urls.php':
						return array(
							'body'     => $this->get_fixture_as_string( 'tests/_data/github/bh-wp-autologin-urls.php' ),
							'response' => array( 'code' => 200 ),
						);
					default:
						$this->fail( 'Unexpected URL: ' . $url );
				}
			},
			10,
			3
		);

		$logger = $this->logger;

		$sut = new GitHub_Integration(
			$http_client,
			new \PsrMock\Psr17\RequestFactory(),
			new \PsrMock\Psr17\StreamFactory(),
			$settings,
			$logger
		);

		$result = $sut->get_remote_product_information( new Licence() );
		$this->assertEquals( '2.4.2', $result?->version );
	}
}
