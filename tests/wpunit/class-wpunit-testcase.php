<?php

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\ColorLogger\ColorLogger;
use lucatume\WPBrowser\TestCase\WPTestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;

class WPUnit_Testcase extends WPTestCase {

	/**
	 * PSR logger with convenience functions for assertions. Prints logs in the console as they are generated.
	 *
	 * @var LoggerInterface&TestLogger&ColorLogger
	 */
	protected ColorLogger $logger;

	protected function setUp(): void {
		parent::setUp();
		$this->logger = new ColorLogger();
	}

	protected function get_installed_major_version( string $plugin_basename ): int {
		$plugin_headers = get_plugin_data( codecept_root_dir( WP_PLUGIN_DIR . '/' . $plugin_basename ) );
		if ( 1 === preg_match( '/(\d+)/', $plugin_headers['Version'], $output_array ) ) {
			return (int) $output_array[1];
		} else {
			return -1;
		}
	}

	protected function is_activate_and_major_version( string $plugin_basename, int $major_version ): bool {
		$is_active = is_plugin_active( $plugin_basename );
		if ( ! $is_active ) {
			return false;
		}
		return $this->get_installed_major_version( $plugin_basename ) === $major_version;
	}

	protected function get_fixture_as_stream( string $fixture_relative_file_path ): StreamInterface {
		$stream_factory              = new \PsrMock\Psr17\StreamFactory();
		$json_string_encoded_fixture = wp_json_encode(
			json_decode(
				file_get_contents( codecept_root_dir( $fixture_relative_file_path ) ) ?: ''
			)
		);
		if ( empty( $json_string_encoded_fixture ) ) {
			$this->fail( 'Failed to parse fixture at: ' . $fixture_relative_file_path );
		}
		return $stream_factory->createStream( $json_string_encoded_fixture );
	}

	protected function get_fixture_as_string( string $fixture_relative_file_path ): string {
		$absolute_path = codecept_root_dir( $fixture_relative_file_path );
		if ( ! is_string( $absolute_path ) ) {
			$this->fail( 'Failed to read fixture at: ' . $fixture_relative_file_path );
		}
		$file_contents = file_get_contents( $absolute_path );
		if ( false === $file_contents ) {
			$this->fail( 'Failed to read fixture at: ' . $fixture_relative_file_path );
		}
		return $file_contents;
	}
}
