<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub;

use BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model\Release;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use Syntatis\WPPluginReadMeParser\Parser as Readme_Parser;
class GitHub_Plugin_Update {
	public static function from_release(
		Settings_Interface $settings,
		Release $release,
		Plugin_Headers $plugin_headers,
		Readme_Parser $readme,
	): Plugin_Update {

		return new Plugin_Update(
			id: null,
			slug: $settings->get_plugin_slug(),
			version: ltrim( $release->tag_name, 'v' ),
			url: $plugin_headers->plugin_uri,
			package: $release->assets[0]->browser_download_url,
			new_version: $release->tag_name,
			tested: $readme->tested,
			requires_php: $readme->requires_php ?? $plugin_headers->requires_php ?? null,
			autoupdate: null,
			icons: null,
			banners: null,
			banners_rtl: null,
			translations: null,
		);
	}
}
