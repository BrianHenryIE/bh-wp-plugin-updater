<?php

namespace BrianHenryIE\WP_SLSWC_Client;

class Settings implements Settings_Interface {

	/**
	 * @var array{Name:string}
	 */
	protected array $plugin_data;

	protected string $plugin_slug;


	public function __construct(
		protected string $plugin_basename, // Path to the plugin file or directory, relative to the plugins directory.
		protected string $license_server_host
	) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		$this->plugin_slug = explode( '/', $plugin_basename )[0];
		$this->plugin_data = get_plugins( $this->get_plugin_slug() );
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

	/**
	 * The plugin slug, i.e. the plugin directory name.
	 */
	public function get_plugin_slug(): string {
		return $this->plugin_slug;
	}

	/**
	 * The plugin directory name and filename.
	 *
	 * E.g. `bh-wp-autologin-urls/bh-wp-autologin-urls.php`.
	 */
	public function get_plugin_basename(): string {
		return $this->plugin_basename;
	}

	/**
	 * Option name - wp option name for license and update information stored as `slug_license`.
	 *
	 * E.g. `bh-wp-autologin-urls` -> `bh_wp_autologin_urls_licence`
	 */
	public function get_licence_data_option_name(): string {
		return str_dash_to_underscore( $this->get_plugin_slug() ) . '_licence';
	}

	/**
	 * Option name - wp option name for plugin information stored as `slug_plugin_information`.
	 *
	 * The data used by {@see get_plugins()}.
	 *
	 * E.g. `bh-wp-autologin-urls` -> `bh_wp_autologin_urls_plugin_information`
	 */
	public function get_plugin_information_option_name(): string {
		return str_dash_to_underscore( "{$this->get_plugin_slug()}_plugin_information" );
	}

	/**
	 * The WP CLI command base.
	 *
	 * E.g. `wp my-plugin licence get-status`.
	 *
	 * @see CLI
	 */
	public function get_cli_base(): ?string {
		return $this->get_plugin_slug();
	}
}
