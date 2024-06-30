<?php
/**
 * A class to model the information returned from the WordPress.org Plugin API.
 *
 * @see wp-admin/includes/plugin-install.php
 *
 * @see https://github.com/WordPress/wordpress.org/blob/trunk/wordpress.org/public_html/wp-content/plugins/plugin-directory/api/routes/class-plugin.php
 * @see http://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=woocommerce
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Model;

interface Plugin_Info_Interface {

	/**
	 * @return array<string, string> sections of the plugin readme. Arbitrary sections followed by screenshots, reviews, faq
	 */
	public function get_sections(): array;

	public function get_name(): string;

	public function get_slug(): string;

	public function get_version(): string;

	/**
	 * author name, or author name in html href
	 */
	public function get_author(): string;

	/**
	 * author profiles.wordpress.org link
	 */
	public function get_author_profile(): string;

	/**
	 * array<string, array{profile:string, avatar:string, display_name:string}> array of contributors indexed by user-nicename, containing profile url, avatar url and user display name.
	 */
	public function get_contributors(): array;

	/**
	 * @return string|false
	 */
	public function get_requires(): ?string;

	/**
	 * @return string|false
	 */
	public function get_tested(): ?string;

	/**
	 * @return string|false
	 */
	public function get_requires_php(): ?string;

	public function get_requires_plugins(): array;

	/**
	 * always an empty array?
	 */
	public function get_compatibility(): array;

	/**
	 * "API outputs as 0..100"
	 */
	public function get_rating(): int;

	/**
	 * int[]  @see \WPORG_Ratings::get_rating_counts()
	 */
	public function get_ratings(): array;

	public function get_num_ratings(): int;

	public function get_support_url(): string;

	public function get_support_threads(): int;

	public function get_support_threads_resolved(): int;

	public function get_active_installs(): int;

	public function get_downloaded(): int;

	/**
	 * 'Y-m-d g:ia \G\M\T'
	 */
	public function get_last_updated(): string;

	/**
	 * 'Y-m-d'
	 */
	public function get_added(): string;

	/**
	 * header_plugin_uri
	 */
	public function get_homepage(): string;

	public function get_short_description(): string;

	public function get_description(): string;

	public function get_download_link(): string;

	public function get_upgrade_notice(): string;

	/**
	 * @return array<array{src:string, caption:string}>
	 */
	public function get_screenshots(): array;

	/**
	 * @return array<string, string> tag name indexed by slug
	 */
	public function get_tags(): array;

	/**
	 * default 'trunk'
	 */
	public function get_stable_tag(): string;

	/**
	 * @return array<string, string> version download link indexed by version number, including trunk
	 */
	public function get_versions(): array;

	/**
	 * default `false`, commercial, community, canonical
	 */
	public function get_business_model(): ?string;

	/**
	 * default '', only for community business model plugins
	 */
	public function get_repository_url(): string;

	/**
	 * default '', only for commercial business model plugins
	 */
	public function get_commercial_support_url(): string;

	/**
	 * default ''
	 */
	public function get_donate_link(): string;

	/**
	 * array<string, string> Index 'low' and 'high' [resolution] for 'banner' and 'banner_2x' @see Template::get_plugin_banner(
	 */
	public function get_banners(): array;

	/**
	 * array<string, string> Index '1x', '2x', 'svg', 'default' @see Template::get_plugin_icon()
	 */
	public function get_icons(): array;

	public function get_blocks(): array;

	public function get_block_assets(): array;

	public function get_author_block_count(): int;

	/**
	 * "Fun fact: ratings are stored as 1-5 in postmeta, but returned as percentages by the API"
	 */
	public function get_author_block_rating(): int;

	public function get_blueprints(): array;

	public function get_preview_link(): array;

	/**
	 * @return array{type:string, slug:string, language:string, version:string, updated:string, package:string}[]
	 */
	public function get_language_packs(): array;

	public function get_block_translations(): array;
}
