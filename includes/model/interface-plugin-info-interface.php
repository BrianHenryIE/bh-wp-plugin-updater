<?php
/**
 * A class to model the information returned from the WordPress.org Plugin API.
 *
 * @see wp-admin/includes/plugin-install.php
 *
 * @see https://github.com/WordPress/wordpress.org/blob/trunk/wordpress.org/public_html/wp-content/plugins/plugin-directory/api/routes/class-plugin.php
 * @see http://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=woocommerce
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Model;

interface Plugin_Info_Interface {

	// protected string $author, // author name, or author name in html href
	// protected string $author_profile, // author profiles.wordpress.org link
	// protected array $contributors, // array<string, array{profile:string, avatar:string, display_name:string}> array of contributors indexed by user-nicename, containing profile url, avatar url and user display name.
	// protected string $requires, // or false
	// protected string $tested, // or false
	// protected string $requires_php, // or false
	// protected array $compatibility, // always an empty array?
	// protected int $rating, // "API outputs as 0..100"
	// protected array $ratings, // int[]  @see \WPORG_Ratings::get_rating_counts()
	// protected string $last_updated, // 'Y-m-d g:ia \G\M\T'
	// protected string $added, // 'Y-m-d'
	// protected string $homepage, // header_plugin_uri
	// protected array $screenshots, // array<array{src:string, caption:string}>
	// protected array $tags, // array<string, string> tag name indexed by slug
	// protected string $stable_tag, // default 'trunk'
	// protected array $versions,      // array<string, string> version download link indexed by version number, including trunk
	// protected ?string $business_model, // default `false`, commercial, community, canonical
	// protected string $repository_url, // default '', only for community business model plugins
	// protected string $commercial_support_url, // default '', only for commercial business model plugins
	// protected string $donate_link, // default ''
	// protected array $banners, // array<string, string> Index 'low' and 'high' [resolution] for 'banner' and 'banner_2x' @see Template::get_plugin_banner(
	// protected array $icons, // array<string, string> Index '1x', '2x', 'svg', 'default' @see Template::get_plugin_icon()
	// protected int $author_block_rating, // "Fun fact: ratings are stored as 1-5 in postmeta, but returned as percentages by the API"
	// protected array $language_packs, // array{type:string, slug:string, language:string, version:string, updated:string, package:string}[]

	/**
	 * @return array<string, string> sections of the plugin readme. Arbitrary sections followed by screenshots, reviews, faq
	 */
	public function get_sections(): array;

	public function get_name(): string;

	public function get_slug(): string;

	public function get_version(): string;

	public function get_author(): string;

	public function get_author_profile(): string;

	public function get_contributors(): array;

	public function get_requires(): string;

	public function get_tested(): string;

	public function get_requires_php(): string;

	public function get_requires_plugins(): array;

	public function get_compatibility(): array;

	public function get_rating(): int;

	public function get_ratings(): array;

	public function get_num_ratings(): int;

	public function get_support_url(): string;

	public function get_support_threads(): int;

	public function get_support_threads_resolved(): int;

	public function get_active_installs(): int;

	public function get_downloaded(): int;

	public function get_last_updated(): string;

	public function get_added(): string;

	public function get_homepage(): string;

	public function get_short_description(): string;

	public function get_description(): string;

	public function get_download_link(): string;

	public function get_upgrade_notice(): string;

	public function get_screenshots(): array;

	public function get_tags(): array;

	public function get_stable_tag(): string;

	public function get_versions(): array;

	public function get_business_model(): ?string;

	public function get_repository_url(): string;

	public function get_commercial_support_url(): string;

	public function get_donate_link(): string;

	public function get_banners(): array;

	public function get_icons(): array;

	public function get_blocks(): array;

	public function get_block_assets(): array;

	public function get_author_block_count(): int;

	public function get_author_block_rating(): int;

	public function get_blueprints(): array;

	public function get_preview_link(): array;

	public function get_language_packs(): array;

	public function get_block_translations(): array;
}
