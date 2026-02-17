<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class Sections {
	public function __construct(
		public readonly string $description,
		public readonly string $installation,
		public readonly string $changelog
	) {
	}
}
