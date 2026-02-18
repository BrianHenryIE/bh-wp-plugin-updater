<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC;

use BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model\Product;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;

class SLSWC_Plugin_Info {
	public static function from_product( Settings_Interface $settings, Product $product ): Plugin_Info {
		return new Plugin_Info(
			sections: array(),
			name: $settings->get_plugin_name(),
			slug: $settings->get_plugin_slug(),
			version: ltrim( $product->version, 'v' ),
			download_link: $product->update_file->file,
		);
	}
}
