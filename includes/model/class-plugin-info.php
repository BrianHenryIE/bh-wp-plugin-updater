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
		 * @return array<string, string> sections of the plugin readme. Arbitrary sections followed by screenshots, reviews, faq
		 */
		public readonly array $sections,
		public readonly string $name,
		public readonly string $slug,
		public readonly string $version,
		/**
		 * author name, or author name in HTML href
		 */
		public readonly ?string $author = null,
		/**
		 * Author link to {@see https://profiles.wordpress.org}.
		 */
		public readonly ?string $author_profile = null,
		/**
		 * array<string, array{profile:string, avatar:string, display_name:string}> array of contributors indexed by user-nicename, containing profile url, avatar url and user display name.
		 */
		public readonly ?array $contributors = null,
		/**
		 * @return string|false
		 */
		public readonly string|false $requires = false,
		/**
		 * @return string|false
		 */
		public readonly string|false $tested = false,
		/**
		 * @return string|false
		 */
		public readonly string|false $requires_php = false,
		public readonly ?array $requires_plugins = null,
		/**
		 * always an empty array?
		 */
		public readonly ?array $compatibility = null,
		/**
		 * "API outputs as 0..100"
		 *
		 * @return int<0,100>
		 */
		public readonly ?int $rating = null,
		/**
		 * int[]  @see \WPORG_Ratings::get_rating_counts()
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
		 */
		public readonly ?string $last_updated = null,
		/**
		 * 'Y-m-d'
		 */
		public readonly ?string $added = null,
		/**
		 * header_plugin_uri
		 */
		public readonly string $homepage = '',
		public readonly string $short_description = '',
		public readonly string $description = '',
		public readonly string $download_link = '',
		public readonly string $upgrade_notice = '',
		/**
		 * @return array<array{src:string, caption:string}>
		 */
		public readonly array $screenshots = array(),
		/**
		 * @return array<string, string> tag name indexed by slug
		 */
		public readonly array $tags = array(),
		/**
		 * default 'trunk'
		 */
		public readonly string $stable_tag = 'trunk',
		/**
		 * @return array<string, string> version download link indexed by version number, including trunk
		 */
		public readonly array $versions = array(),
		/**
		 * default `false`, commercial, community, canonical
		 */
		public readonly string|false $business_model = false,
		/**
		 * default '', only for community business model plugins
		 */
		public readonly string $repository_url = '',
		/**
		 * default '', only for commercial business model plugins
		 */
		public readonly string $commercial_support_url = '',
		/**
		 * default ''
		 */
		public readonly string $donate_link = '',
		/**
		 * array<string, string> Index 'low' and 'high' [resolution] for 'banner' and 'banner_2x' @see Template::get_plugin_banner(
		 */
		public readonly array $banners = array(),
		/**
		 * array<string, string> Index '1x', '2x', 'svg', 'default' @see Template::get_plugin_icon()
		 */
		public readonly array $icons = array(),
		public readonly array $blocks = array(),
		public readonly array $block_assets = array(),
		public readonly int $author_block_count = 0,
		/**
		 * "Fun fact: ratings are stored as 1-5 in post meta, but returned as percentages by the API"
		 */
		public readonly int $author_block_rating = 0,
		public readonly array $blueprints = array(),
		public readonly array $preview_link = array(),
		/**
		 * @return array{type:string, slug:string, language:string, version:string, updated:string, package:string}[]
		 */
		public readonly array $language_packs = array(),
		public readonly array $block_translations = array(),
	) {
	}
}
