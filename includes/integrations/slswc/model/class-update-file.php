<?php

// "update_file": {
// "id": "40bb2001-48c3-4633-995a-447aa82b491d",
// "file": "https:\/\/updatestest.bhwp.ie\/wp-content\/uploads\/woocommerce_uploads\/2024\/05\/bh-wp-autologin-urls.2.3.0-alozbb.zip",
// "name": "bh-wp-autologin-urls.2.3.0-alozbb.zip"
// },

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class Update_File {

	/**
	 * @param string $id
	 * @param string $file
	 * @param string $name
	 */
	public function __construct(
		/**
		 * UUID
		 *
		 * @var string $id
		 */
		public readonly string $id,
		/**
		 * Download URL in the woocommerce_uploads directory.
		 * @var string $id
		 */
		public readonly string $file,
		/**
		 * Filename alone. I.e. the last part of the URL.
		 * @var string $id
		 */
		public readonly string $name
	) {
	}
}
