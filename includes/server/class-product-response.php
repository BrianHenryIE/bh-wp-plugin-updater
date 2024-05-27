<?php

// {
// "status": "ok",
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
// }

namespace BrianHenryIE\WP_SLSWC_Client\Server;

class Product_Response {

	public function __construct(
		protected string $status,
		protected Product $product
	) {
	}

	public function get_status(): string {
		return $this->status;
	}

	public function get_product(): Product {
		return $this->product;
	}
}
