<?php

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\Includes\Settings_Trait;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\CLI;

class Settings implements Settings_Interface {
	use Settings_Trait;

	/**
	 * @var array{Name:string}
	 */
	protected array $plugin_data;

	protected string $plugin_slug;


	public function __construct(
		protected string $plugin_basename, // Path to the plugin file or directory, relative to the plugins directory.
		protected string $license_server_host
	) {
		require_once constant( 'ABSPATH' ) . '/wp-admin/includes/plugin.php';
		$this->plugin_slug = explode( '/', $plugin_basename )[0];
		// This `get_plugins($slug)` call is adding blank space to the admin UI.
		// $this->plugin_data = get_plugins( $this->get_plugin_slug() );
		// this does not:
		$this->plugin_data = get_plugins()[ $this->get_plugin_basename() ];
	}

	/**
	 * The plugin directory name and filename.
	 *
	 * E.g. `bh-wp-autologin-urls/bh-wp-autologin-urls.php`.
	 */
	public function get_plugin_basename(): string {
		return $this->plugin_basename;
	}

	public function get_plugin_name(): string {
		return $this->plugin_data['Name'] ?? 'Plugin';
	}

	/**
	 * The license server host.
	 */
	public function get_licence_server_host(): string {
		// $license_server_host = @wp_parse_url( $this->settings->get_license_server_url(), PHP_URL_HOST );
		return $this->license_server_host;
	}
}
