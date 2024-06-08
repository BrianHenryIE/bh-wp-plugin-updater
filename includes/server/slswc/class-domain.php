<?php

// "domain": "",
// "slug": "a-plugin",
// "status": "active",
// "date_time": 1716840810,
// "version": null,
// "environment": "staging"
// }

namespace BrianHenryIE\WP_SLSWC_Client\Server\SLSWC;

class Domain {

	public function __construct(
		protected string $domain,
		protected string $slug,
		protected string $status,
		protected int $date_time,
		protected ?string $version,
		protected string $environment
	) {
	}

	public function get_domain(): string {
		return $this->domain;
	}

	public function get_slug(): string {
		return $this->slug;
	}

	public function get_status(): string {
		return $this->status;
	}

	public function get_date_time(): \DateTimeInterface {
		return new \DateTimeImmutable( '@' . $this->date_time );
	}

	public function get_version(): ?string {
		return $this->version;
	}

	public function get_environment(): string {
		return $this->environment;
	}
}
