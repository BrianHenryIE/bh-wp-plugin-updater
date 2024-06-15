<?php


namespace BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model;

class Banners {
	public function __construct(
		protected string $low,
		protected string $high
	) {
	}

	public function get_low(): string {
		return $this->low;
	}

	public function get_high(): string {
		return $this->high;
	}
}
