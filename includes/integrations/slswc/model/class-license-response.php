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
use DateTimeInterface;

class License_Response {

	public function __construct(
		/**
		 * active|deactivated
		 */
		public readonly string $status,
		public readonly string $slug,
		/**
		 * 2025-05-27 00:00:00
		 */
		public readonly string $expires,
		public readonly License $license,
		public readonly Domain $domain
	) {
	}

	public function get_expires(): DateTimeInterface {
		return DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $this->expires );
	}
}
