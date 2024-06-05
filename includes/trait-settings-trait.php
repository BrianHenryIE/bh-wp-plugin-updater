<?php

namespace BrianHenryIE\WP_SLSWC_Client\Includes;

use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use function BrianHenryIE\WP_SLSWC_Client\str_dash_to_underscore;

/**
 * @see Settings_Interface
 */
trait Settings_Trait {

	/**
	 * The plugin slug, i.e. the plugin directory name.
	 */
	public function get_plugin_slug(): string {
		return explode( '/', $this->get_plugin_basename() )[0];
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

	/**
	 * The Rest API base.
	 */
	public function get_rest_base(): ?string {
		return $this->get_plugin_slug();
	}
}
