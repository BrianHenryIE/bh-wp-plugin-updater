<?php
/**
 * An object of metadata about the available plugin update.
 *
 * This is the data consumed by WordPress itself – it aims to document the stdClass expected by the WordPress
 * core function `wp_update_plugins()`.
 *
 * Hopefully by using __serialize() – and seeing WordPress's `$sanitize_plugin_update_payload`, we can have a typed,
 * documented class that works with WordPress core.
 *
 * This is saved in the `update_plugins` transient.
 *
 * While nice to have an OO class here, the aim is to be consumed by the WordPress core function `wp_update_plugins()`
 * which traditionally casts to an stdClass with public properties.
 *
 * @see wp_update_plugins()
 * @see https://github.com/WordPress/WordPress/blob/e67e9caef43512751aae60f37d91cf589dce78b0/wp-includes/update.php#L482-L508
 * @see https://api.wordpress.org/plugins/update-check/1.1/
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Model;

/**
 * `@wpdoc` here refers to the comments in the WordPress core code.
 *
 * @wpdoc: The plugin update data with the latest details.
 *
 * @phpstan-type Plugin_Update_Array array{id:null|string,slug:string,version:string,url:string,package:string,tested:null|string,requires_php:null|string,autoupdate:null|bool,icons:null|array<string>,banners:null|string[],banners_rtl:null|string[],translations:null|array<array{language:string,version:string,updated:string,package:string,autoupdate:string}>}
 */
class Plugin_Update {

	/**
	 * Constructor
	 *
	 * @param ?string                                                                                       $id
	 * @param string                                                                                        $slug
	 * @param string                                                                                        $version
	 * @param string                                                                                        $url
	 * @param string                                                                                        $package The update download URL.
	 * @param ?string                                                                                       $tested WordPress version the plugin is tested up to.
	 * @param ?string                                                                                       $requires_php PHP version the plugin requires.
	 * @param ?bool                                                                                         $autoupdate
	 * @param ?string[]                                                                                     $icons
	 * @param ?string[]                                                                                     $banners
	 * @param ?string[]                                                                                     $banners_rtl
	 * @param ?array<array{language:string,version:string,updated:string,package:string,autoupdate:string}> $translations
	 */
	public function __construct(
		/**
		 * @wpdoc: ID of the plugin for update purposes, should be a URI specified in the `Update URI` header field.
		 *
		 * This will be overwritten by `$plugin_data['UpdateURI']` in update.php.
		 *
		 * @var ?string $id
		 */
		public readonly ?string $id,
		/**
		 * @wpdoc: Slug of the plugin.
		 *
		 * @var string $slug
		 */
		public readonly string $slug,
		/**
		 * @wpdoc: The version of the plugin.
		 *
		 * @var string $version
		 */
		public readonly string $version,
		/**
		 * @wpdoc: The URL for details of the plugin.
		 *
		 * @var string $url
		 */
		public readonly string $url,
		/**
		 * @wpdoc: The update ZIP for the plugin.
		 *
		 * @var string $package
		 */
		public readonly string $package,
		public readonly ?string $new_version = null,
		/**
		 * @wpdoc: The version of WordPress the plugin is tested against.
		 *
		 * @var ?string $tested
		 */
		public readonly ?string $tested = null,
		/**
		 * @wpdoc: The version of PHP which the plugin requires.
		 *
		 * @var ?string $requires_php
		 */
		public readonly ?string $requires_php = null,
		/**
		 * @wpdoc: Whether the plugin should automatically update.
		 *
		 * TODO: does this mean the plugin author suggests it, or it's used as a record of the site admin enabling it?
		 *
		 * @var ?bool $autoupdate
		 */
		public readonly ?bool $autoupdate = null,
		/**
		 * @wpdoc: Array of plugin icons.
		 *
		 * @var ?string[]
		 */
		public readonly ?array $icons = null,
		/**
		 * @wpdoc: Array of plugin banners.
		 *
		 * @var ?string[]
		 */
		public readonly ?array $banners = null,
		/**
		 * @wpdoc: Array of plugin RTL banners.
		 *
		 * @var ?string[]
		 */
		public readonly ?array $banners_rtl = null,
		/**
		 * @wpdoc: List of translation updates for the plugin.
		 * The language the translation update is for.
		 * The version of the plugin this translation is for. This is not the version of the language file.
		 * The update timestamp of the translation file. Should be a date in the `YYYY-MM-DD HH:MM:SS` format.
		 * The ZIP location containing the translation update.
		 * Whether the translation should be automatically installed.
		 *
		 * @var ?array<array{language:string,version:string,updated:string,package:string,autoupdate:string}> $translations
		 */
		public readonly ?array $translations = null,
	) {
	}
}
