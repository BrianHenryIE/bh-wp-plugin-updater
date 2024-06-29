<?php
/**
 * An object of metadata about the available plugin update.
 *
 * This is the data consumed by WordPress itself – it aims to document the stdClass expected by the WordPress
 * core function `wp_update_plugins()`.
 *
 * Hopefully by using __serialize() – and seeing WordPress's `$sanitize_plugin_update_payload`, we can have a typed,
 * documented class that words with WordPress core.
 *
 * This is saved in the `update_plugins` transient.
 *
 * While nice to have an OO class here, the aim is to be consumed by the WordPress core function `wp_update_plugins()`
 * which traditionally casts to an stdClass with public properties.
 *
 * @see wp_update_plugins()
 * @see https://github.com/WordPress/WordPress/blob/e67e9caef43512751aae60f37d91cf589dce78b0/wp-includes/update.php#L482-L508
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Model;

/**
 * `wpdoc` here refers to the comments in the WordPress core code.
 *
 * @wpdoc The plugin update data with the latest details.
 */
interface Plugin_Update_Interface {

	/**
	 * Constructor
	 *
	 * @param string|null $id
	 * @param string      $slug
	 * @param string      $version
	 * @param string      $url
	 * @param string      $package The update download URL.
	 * @param string|null $tested WordPress version the plugin is tested up to.
	 * @param string|null $requires_php PHP version the plugin requires.
	 * @param bool|null   $autoupdate
	 * @param array|null  $icons
	 * @param array|null  $banners
	 * @param array|null  $banners_rtl
	 * @param array|null  $translations
	 */


	/**
	 * @wpdoc ID of the plugin for update purposes, should be a URI specified in the `Update URI` header field.
	 *
	 * This will be overwritten by `$plugin_data['UpdateURI']` in update.php.
	 */
	public function get_id(): ?string;

	/**
	 * @wpdoc Slug of the plugin.
	 */
	public function get_slug(): string;

	/**
	 * @wpdoc The version of the plugin.
	 */
	public function get_version(): string;

	/**
	 * @wpdoc The URL for details of the plugin.
	 */
	public function get_url(): string;

	/**
	 * @wpdoc The update ZIP for the plugin.
	 */
	public function get_package(): string;

	/**
	 * @wpdoc The version of WordPress the plugin is tested against.
	 */
	public function get_tested(): ?string;

	/**
	 * @wpdoc The version of PHP which the plugin requires.
	 */
	public function get_requires_php(): ?string;

	/**
	 * @wpdoc Whether the plugin should automatically update.
	 *
	 * TODO: does this mean the plugi author suggests it, or it's used as a record of the site admin enabling it?
	 */
	public function get_autoupdate(): ?bool;

	/**
	 * @wpdoc Array of plugin icons.
	 */
	public function get_icons(): ?array;

	/**
	 * @wpdoc Array of plugin banners.
	 */
	public function get_banners(): ?array;

	/**
	 * @wpdoc Array of plugin RTL banners.
	 */
	public function get_banners_rtl(): ?array;

	/**
	 * @wpdoc List of translation updates for the plugin.
	 * The language the translation update is for.
	 * The version of the plugin this translation is for. This is not the version of the language file.
	 * The update timestamp of the translation file. Should be a date in the `YYYY-MM-DD HH:MM:SS` format.
	 * The ZIP location containing the translation update.
	 * Whether the translation should be automatically installed.
	 *
	 * @return array<array{language:string,version:string,updated:string,package:string,autoupdate:string}>
	 */
	public function get_translations(): ?array;
}
