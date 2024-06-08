<?php

// "order": {
// "refunds": null,
// "customer_id": null
// }

namespace BrianHenryIE\WP_SLSWC_Client\Server\SLSWC;

class Order {

	public function __construct(
		protected ?int $refunds,
		protected ?int $customer_id
	) {
	}

	public function get_refunds(): ?int {
		return $this->refunds;
	}

	public function get_customer_id(): ?int {
		return $this->customer_id;
	}
}
