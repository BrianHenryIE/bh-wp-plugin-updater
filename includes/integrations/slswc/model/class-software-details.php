<?php


namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class Software_Details {
	public function __construct(
		public readonly string $name,
		public readonly string $id,
		public readonly string $slug,
		public readonly string $plugin,
		public readonly string $version,
		public readonly string $last_updated,
		public readonly string $homepage,
		public readonly string $requires,
		public readonly string $tested,
		public readonly string $new_version,
		public readonly string $author,
		public readonly Sections $sections,
		public readonly Banners $banners,
		public readonly int $rating,
		public readonly array $ratings,
		public readonly int $num_ratings,
		public readonly int $active_installs,
		public readonly bool $external,
		/**
		 * `package` and `download_link` in the JSON seem to be the same.
		 */
		public readonly string $package,
		/**
		 * `package` and `download_link` in the JSON seem to be the same.
		 */
		public readonly string $download_link
	) {
	}
}
