<?php


namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

class Banners {
	public function __construct(
		public readonly string $low,
		public readonly string $high
	) {
	}
}
