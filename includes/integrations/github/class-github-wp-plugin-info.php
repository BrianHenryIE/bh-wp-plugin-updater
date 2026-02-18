<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub;

use BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model\Release;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info;

class GitHub_WP_Plugin_Info {
	public static function from_release(
		string $slug,
		string $repository_url,
		Release $release,
		Plugin_Headers $headers,
		string $changelog
	): Plugin_Info {

		// TODO: add a filter here.

		return new Plugin_Info(
			sections: !empty( $changelog ) ? array( 'changelog' => $changelog ) : array(),
			name: $headers->name,
			slug: $slug,
			version: ltrim( $release->tag_name, 'v' ),
			author: $headers->author,
			author_profile: $headers->author_uri,
			contributors: array(),
			requires: $headers->requires_wp ?? false,
			tested: false,
			requires_php: $headers->requires_php ?? false,
			requires_plugins: $headers->requires_plugins,
			compatibility: array(),
			rating: 0,
			ratings: array(),
			num_ratings: 0,
			support_url: '',
			support_threads: 0,
			support_threads_resolved: 0,
			active_installs: 0,
			downloaded: 0,
			last_updated: $release->created_at,
			added: '',
			homepage: $headers->plugin_uri ?? '',
			short_description: $headers->description ?? '',
			description: '',
			download_link: $release->assets[0]->browser_download_url,
			upgrade_notice: '',
			screenshots: array(), // TODO: look for/in the .wordpress directory in the repo.
			tags: array(),
			stable_tag: $release->tag_name,
			versions: array(),
			business_model: false,
			repository_url: $repository_url,
			commercial_support_url: '',
			donate_link: $headers->author_uri ?? '',
			banners: array(),
			icons: array(),
			blocks: array(),
			block_assets: array(),
			author_block_count: 0,
			author_block_rating: 0,
			blueprints: array(),
			preview_link: array(),
			language_packs: array(),
			block_translations: array(),
		);
	}
}
