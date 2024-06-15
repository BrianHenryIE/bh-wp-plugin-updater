<?php

namespace BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model;

class Sections {
	public function __construct(
		protected string $description,
		protected string $installation,
		protected string $changelog
	) {
	}

	public function get_description(): string {
		return $this->description;
	}

	public function get_installation(): string {
		return $this->installation;
	}

	public function get_changelog(): string {
		return $this->changelog;
	}
}
