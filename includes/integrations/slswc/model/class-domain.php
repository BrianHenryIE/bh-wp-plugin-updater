<?php

// "domain": "",
// "slug": "a-plugin",
// "status": "active",
// "date_time": 1716840810,
// "version": null,
// "environment": "staging"
// }

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

use DateTimeImmutable;

class Domain {

	public function __construct(
		public readonly string $domain,
		public readonly string $slug,
		public readonly string $status,
		public readonly int $date_time,
		public readonly ?string $version,
		public readonly string $environment
	) {
	}

	/**
	 * @throws \Exception
	 */
	public function get_date_time(): \DateTimeInterface {
		return new DateTimeImmutable( '@' . $this->date_time );
	}
}
