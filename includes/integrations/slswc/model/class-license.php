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


namespace BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model;

class License {

	public function __construct(
		protected bool $valid_slug,
		protected bool $exists,
		protected bool $deleted,
		protected ?string $current_version,
		protected ?string $error_message,
		protected Order $order
	) {
	}

	public function is_valid_slug(): bool {
		return $this->valid_slug;
	}

	public function is_exists(): bool {
		return $this->exists;
	}

	public function is_deleted(): bool {
		return $this->deleted;
	}

	public function get_current_version(): ?string {
		return $this->current_version;
	}

	public function get_error_message(): ?string {
		return $this->error_message;
	}

	public function get_order(): Order {
		return $this->order;
	}
}
