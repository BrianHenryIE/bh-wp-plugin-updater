<?php

// {
// "status": "active",
// "slug": "a-plugin",
// "expires": "2025-05-27 00:00:00",
// "license": {
// "valid_slug": true,
// "exists": true,
// "deleted": false,
// "current_version": null,
// "error_message": null,
// "order": {
// "refunds": null,
// "customer_id": null
// }
// },
// "domain": {
// "domain": "",
// "slug": "a-plugin",
// "status": "active",
// "date_time": 1716840810,
// "version": null,
// "environment": "staging"
// }
// }

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

use DateTimeImmutable;

class License_Response {

	public function __construct(
		protected string $status,
		protected string $slug,
		protected string $expires,
		protected License $license,
		protected Domain $domain
	) {
	}

	/**
	 * active|deactivated
	 */
	public function get_status(): string {
		return $this->status;
	}

	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * 2025-05-27 00:00:00
	 */
	public function get_expires(): \DateTimeInterface {
		return DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $this->expires );
	}

	public function get_license(): License {
		return $this->license;
	}

	public function get_domain(): Domain {
		return $this->domain;
	}
}
