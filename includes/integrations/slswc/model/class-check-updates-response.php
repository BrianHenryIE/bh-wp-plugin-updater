<?php

// {
// "status": "active",
// "slug": "test-plugin",
// "message": "License Valid",
// "software_details": {
// "name": "test-plugin",
// "id": "3",
// "slug": "test-plugin",
// "plugin": "test-plugin",
// "version": "1.2.0",
// "last_updated": "2024-06-11 02:33:17",
// "homepage": "https:\/\/updatestest.bhwp.ie\/product\/test-plugin\/",
// "requires": "",
// "tested": "",
// "new_version": "1.2.0",
// "author": "",
// "sections": {
// "description": "",
// "installation": "",
// "changelog": ""
// },
// "banners": {
// "low": "",
// "high": ""
// },
// "rating": 0,
// "ratings": [],
// "num_ratings": 0,
// "active_installs": 1,
// "external": true,
// "package": "https:\/\/updatestest.bhwp.ie\/?download_file=21&order=wc_order_lckerVdejfHHI&email&key=40bb2001-48c3-4633-995a-447aa82b491d",
// "download_link": "https:\/\/updatestest.bhwp.ie\/?download_file=21&order=wc_order_lckerVdejfHHI&email&key=40bb2001-48c3-4633-995a-447aa82b491d"
// }
// }

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class Check_Updates_Response {

	public function __construct(
		protected string $status,
		protected string $slug,
		protected string $message,
		protected Software_Details $software_details
	) {
	}

	public function get_status(): string {
		return $this->status;
	}

	public function get_slug(): string {
		return $this->slug;
	}

	public function get_message(): string {
		return $this->message;
	}

	public function get_software_details(): Software_Details {
		return $this->software_details;
	}
}
