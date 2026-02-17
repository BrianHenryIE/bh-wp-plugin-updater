<?php

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


namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class License {

	public function __construct(
		public readonly bool $valid_slug,
		public readonly bool $exists,
		public readonly bool $deleted,
		public readonly ?string $current_version,
		public readonly ?string $error_message,
		public readonly Order $order
	) {
	}
}
