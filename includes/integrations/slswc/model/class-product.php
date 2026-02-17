<?php

// "product": {
// "software": 1,
// "software_type": "plugin",
// "allow_staging": "yes",
// "renewal_period": "annual",
// "software_slug": "a-plugin",
// "version": "",
// "author": "",
// "required_wp": "",
// "compatible_to": "",
// "updated": "",
// "activations": "1",
// "staging_activations": "3",
// "description": "",
// "change_log": "",
// "installation": "",
// "documentation_link": "",
// "banner_low": "",
// "banner_high": "",
// "update_file_id": "40bb2001-48c3-4633-995a-447aa82b491d",
// "update_file_url": "https:\/\/updatestest.bhwp.ie\/wp-content\/uploads\/woocommerce_uploads\/2024\/05\/bh-wp-autologin-urls.2.3.0-alozbb.zip",
// "update_file_name": "bh-wp-autologin-urls.2.3.0-alozbb.zip",
// "update_file": {
// "id": "40bb2001-48c3-4633-995a-447aa82b491d",
// "file": "https:\/\/updatestest.bhwp.ie\/wp-content\/uploads\/woocommerce_uploads\/2024\/05\/bh-wp-autologin-urls.2.3.0-alozbb.zip",
// "name": "bh-wp-autologin-urls.2.3.0-alozbb.zip"
// },
// "thumbnail": false
// }

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class Product {

	public function __construct(
		public readonly int $software,
		public readonly string $software_type,
		public readonly string $allow_staging,
		public readonly string $renewal_period,
		public readonly string $software_slug,
		public readonly string $version,
		public readonly string $author,
		public readonly string $required_wp,
		public readonly string $compatible_to,
		public readonly string $updated,
		public readonly string $activations,
		public readonly string $staging_activations,
		public readonly string $description,
		public readonly string $change_log,
		public readonly string $installation,
		public readonly string $documentation_link,
		public readonly string $banner_low,
		public readonly string $banner_high,
		public readonly string $update_file_id,
		public readonly string $update_file_url,
		public readonly string $update_file_name,
		public readonly Update_File $update_file,
		public readonly bool $thumbnail // TODO: this looks wrong.
	) {
	}
}
