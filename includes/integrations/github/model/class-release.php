<?php
/**
 *
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model;

class Release {

	/**
	 * @param Assets[] $assets
	 */
	public function __construct(
		public readonly string $url,
		public readonly string $assets_url,
		public readonly string $upload_url,
		public readonly string $html_url,
		public readonly int $id,
		public readonly User $author,
		public readonly string $node_id,
		public readonly string $tag_name,
		public readonly string $target_commitish,
		public readonly string $name,
		public readonly bool $draft,
		public readonly bool $prerelease,
		public readonly string $created_at,
		public readonly string $published_at,
		public readonly array $assets,
		public readonly string $tarball_url,
		public readonly string $zipball_url,
		public readonly string $body,
		public readonly int $mentions_count
	) {
	}
}
