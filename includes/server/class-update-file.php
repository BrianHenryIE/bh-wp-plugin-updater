<?php

// "update_file": {
// "id": "40bb2001-48c3-4633-995a-447aa82b491d",
// "file": "https:\/\/updatestest.bhwp.ie\/wp-content\/uploads\/woocommerce_uploads\/2024\/05\/bh-wp-autologin-urls.2.3.0-alozbb.zip",
// "name": "bh-wp-autologin-urls.2.3.0-alozbb.zip"
// },

namespace BrianHenryIE\WP_SLSWC_Client\Server;

class Update_File {

	public function __construct(
		protected string $id,
		protected string $file,
		protected string $name
	) {
	}

	public function get_id(): string {
		return $this->id;
	}

	public function get_file(): string {
		return $this->file;
	}

	public function get_name(): string {
		return $this->name;
	}
}
