<?php
/**
 *
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model;

class Assets {

	public function __construct(
		public readonly string $url,
		public readonly int $id,
		public readonly string $node_id,
		public readonly string $name,
		public readonly string $label,
		public readonly User $uploader,
		public readonly string $content_type,
		public readonly string $state,
		public readonly int $size,
		public readonly int $download_count,
		public readonly string $created_at,
		public readonly string $updated_at,
		public readonly string $browser_download_url
	) {
	}
}
