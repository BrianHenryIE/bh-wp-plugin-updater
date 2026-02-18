<?php

// "order": {
// "refunds": null,
// "customer_id": null
// }

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class Order {

	public function __construct(
		public readonly ?int $refunds,
		public readonly ?int $customer_id
	) {
	}
}
