<?php
/**
 * The minimal/customisable settings for the plugin.
 *
 * The Settings class implements this with some default values/convenience inferences.
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\WP_Includes\CLI;

interface Settings_Interface {

	/**
	 * HTTP hostname for software updates.
	 *
	 * Must include the https:// prefix.
	 */
	public function get_licence_server_host(): string;

	/**
	 * The basename of the licensed plugin.
	 */
	public function get_plugin_basename(): string;

	/**
	 * The slug of the licensed plugin.
	 */
	public function get_plugin_slug(): string;

	/**
	 * The friendly display name of the licensed plugin.
	 */
	public function get_plugin_name(): string;

	/**
	 * Optional base for CLI commands.
	 *
	 * @see CLI
	 */
	public function get_cli_base(): ?string;

	/**
	 * The wp_options name for saving the licence information.
	 *
	 * @see Licence
	 */
	public function get_licence_data_option_name(): string;

	/**
	 * The wp_options name for caching the plugin information.
	 */
	public function get_plugin_information_option_name(): string;
}
