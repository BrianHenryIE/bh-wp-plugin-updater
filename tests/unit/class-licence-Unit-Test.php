<?php

namespace BrianHenryIE\WP_SLSWC_Client;

/**
 * @coversDefaultClass \BrianHenryIE\WP_SLSWC_Client\Licence
 */
class Licence_Unit_Test extends \Codeception\Test\Unit {

	public function test_serialize(): void {

		$settings = new class() implements \BrianHenryIE\WP_SLSWC_Client\Settings_Interface {

			public function get_plugin_name(): string {
				return 'Test Plugin';
			}

			public function get_log_level(): string {
				return 'debug';
			}

			public function get_licence_server_host(): string {
				return 'https://example.com';
			}

			public function get_cli_base(): ?string {
				return 'test-plugin';
			}

			public function get_licence_data_option_name(): string {
				return 'test-plugin-licence';
			}

			public function get_plugin_information_option_name(): string {
				return 'test-plugin-information';
			}

			public function get_plugin_version(): string {
				return '1.2.3';
			}

			public function get_plugin_slug(): string {
				return 'test-plugin';
			}

			public function get_plugin_basename(): string {
				return 'test-plugin/test-plugin.php';
			}
		};

		\WP_Mock::userFunction(
			'update_option',
		);

		$licence = new Licence( $settings );
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expires( new \DateTimeImmutable() );

		$expected = substr(
			serialize(
				array(
					'licence_key'  => 'abc123',
					'status'       => 'active',
					'expires'      => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
					'last_updated' => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
				)
			),
			3
		);

		$this->assertStringContainsString( $expected, serialize( $licence ) );
	}

	public function test_json_serialize(): void {

		$settings = new class() implements \BrianHenryIE\WP_SLSWC_Client\Settings_Interface {

			public function get_plugin_name(): string {
				return 'Test Plugin';
			}

			public function get_log_level(): string {
				return 'debug';
			}

			public function get_licence_server_host(): string {
				return 'https://example.com';
			}

			public function get_cli_base(): ?string {
				return 'test-plugin';
			}

			public function get_licence_data_option_name(): string {
				return 'test-plugin-licence';
			}

			public function get_plugin_information_option_name(): string {
				return 'test-plugin-information';
			}

			public function get_plugin_version(): string {
				return '1.2.3';
			}

			public function get_plugin_slug(): string {
				return 'test-plugin';
			}

			public function get_plugin_basename(): string {
				return 'test-plugin/test-plugin.php';
			}
		};

		\WP_Mock::userFunction(
			'update_option',
		);

		$licence = new Licence( $settings );
		$licence->set_licence_key( 'abc123' );
		$licence->set_status( 'active' );
		$licence->set_last_updated( new \DateTimeImmutable() );
		$licence->set_expires( new \DateTimeImmutable() );

		$expected = json_encode(
			array(
				'licence_key'  => 'abc123',
				'status'       => 'active',
				'expires'      => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
				'last_updated' => $licence->get_last_updated()->format( \DateTimeInterface::ATOM ),
			)
		);

		$this->assertEquals( $expected, json_encode( $licence ) );
	}
}
