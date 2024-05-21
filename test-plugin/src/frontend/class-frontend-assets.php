<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Frontend;

use BrianHenryIE\WP_SLSWC_Client\Settings;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the frontend-facing stylesheet and JavaScript.
 */
class Frontend_Assets {

	/**
	 * The plugin settings.
	 *
	 * @uses Settings::get_plugin_version() for caching.
	 * @uses Settings::get_plugin_basename() for determining the plugin URL.
	 *
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * Constructor
	 *
	 * @param Settings $settings The plugin settings.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register the stylesheets for the frontend-facing side of the site.
	 *
	 * @hooked wp_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {
		$version = $this->settings->get_plugin_version();

		$plugin_url = plugin_dir_url( $this->settings->get_plugin_basename() );

		wp_enqueue_style( 'bh-wp-slswc-client', $plugin_url . 'assets/bh-wp-slswc-client-frontend.css', array(), $version, 'all' );
	}

	/**
	 * Register the JavaScript for the frontend-facing side of the site.
	 *
	 * @hooked wp_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {
		$version = $this->settings->get_plugin_version();

		$plugin_url = plugin_dir_url( $this->settings->get_plugin_basename() );

		wp_enqueue_script( 'bh-wp-slswc-client', $plugin_url . 'assets/bh-wp-slswc-client-frontend.js', array( 'jquery' ), $version, false );
	}
}
