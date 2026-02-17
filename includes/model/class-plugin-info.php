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

class Plugin_Info {

	public function __construct(
		/**
		 * @var array<string, string> $sections sections of the plugin readme. Arbitrary sections followed by screenshots, reviews, faq
		 */
		public readonly array $sections,
		public readonly string $name,
		public readonly string $slug,
		public readonly string $version,
		/**
		 * author name, or author name in HTML href
		 *
		 * @var ?string $author
		 */
		public readonly ?string $author = null,
		/**
		 * Author link to {@see https://profiles.wordpress.org}.
		 * @var ?string $author_profile
		 */
		public readonly ?string $author_profile = null,
		/**
		 * @var ?array<string, array{profile:string, avatar:string, display_name:string}> $contributors array of contributors indexed by user-nicename, containing profile url, avatar url and user display name.
		 */
		public readonly ?array $contributors = null,
		/**
		 * @var string|false $requires
		 */
		public readonly string|false $requires = false,
		/**
		 * @var string|false $tested
		 */
		public readonly string|false $tested = false,
		/**
		 * @var string|false $requires_php
		 */
		public readonly string|false $requires_php = false,
		public readonly ?array $requires_plugins = null,
		/**
		 * always an empty array?
		 * @var array $compatibility
		 */
		public readonly ?array $compatibility = null,
		/**
		 * "API outputs as 0..100"
		 *
		 * @var int<0,100> $rating
		 */
		public readonly ?int $rating = null,
		/**
		 * @var int[] $ratings @see \WPORG_Ratings::get_rating_counts()
		 */
		public readonly ?array $ratings = null,
		public readonly ?int $num_ratings = null,
		public readonly ?string $support_url = null,
		public readonly ?int $support_threads = null,
		public readonly ?int $support_threads_resolved = null,
		public readonly ?int $active_installs = null,
		public readonly ?int $downloaded = null,
		/**
		 * 'Y-m-d g:ia \G\M\T'
		 *
		 * @var ?string $last_updated
		 */
		public readonly ?string $last_updated = null,
		/**
		 * 'Y-m-d'
		 * @var ?string $added
		 */
		public readonly ?string $added = null,
		/**
		 * header_plugin_uri
		 * @var ?string $homepage
		 */
		public readonly string $homepage = '',
		public readonly string $short_description = '',
		public readonly string $description = '',
		public readonly string $download_link = '',
		public readonly string $upgrade_notice = '',
		/**
		 * @var array<array{src:string, caption:string}> $screenshots
		 */
		public readonly array $screenshots = array(),
		/**
		 * @var array<string, string> $tags tag name indexed by slug, e.g. "online-store": "online store".
		 */
		public readonly array $tags = array(),
		/**
		 * default 'trunk'
		 * @var string $stable_tag
		 */
		public readonly string $stable_tag = 'trunk',
		/**
		 * @var array<string, string> $versions version download link indexed by version number, including trunk
		 */
		public readonly array $versions = array(),
		/**
		 * default `false`, commercial, community, canonical
		 *
		 * @var string|false $business_model
		 */
		public readonly string|false $business_model = false,
		/**
		 * default '', only for community business model plugins
		 * @var string $repository_url
		 */
		public readonly string $repository_url = '',
		/**
		 * default '', only for commercial business model plugins
		 * @var string $commercial_support_url
		 */
		public readonly string $commercial_support_url = '',
		/**
		 * default ''
		 * @var string $donate_link
		 */
		public readonly string $donate_link = '',
		/**
		 * @var array<string, string> $banners Index 'low' and 'high' [resolution] for 'banner' and 'banner_2x' @see Template::get_plugin_banner(
		 */
		public readonly array $banners = array(),
		/**
		 * @var array<string, string> $icons Index '1x', '2x', 'svg', 'default' @see Template::get_plugin_icon()
		 */
		public readonly array $icons = array(),
		public readonly array $blocks = array(),
		public readonly array $block_assets = array(),
		public readonly int $author_block_count = 0,
		/**
		 * "Fun fact: ratings are stored as 1-5 in post meta, but returned as percentages by the API"
		 *
		 * @var int $author_block_rating
		 */
		public readonly int $author_block_rating = 0,
		public readonly array $blueprints = array(),
		public readonly array $preview_link = array(),
		/**
		 * @var array<array{type:string, slug:string, language:string, version:string, updated:string, package:string}> $language_packs
		 */
		public readonly array $language_packs = array(),
		public readonly array $block_translations = array(),
	) {
	}
}
