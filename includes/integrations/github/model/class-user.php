<?php
/**
 * @see https://github.com/github/rest-api-description
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model;

class User {

	public function __construct(
		public readonly string $login,
		public readonly int $id,
		public readonly string $node_id,
		public readonly string $avatar_url,
		public readonly string $gravatar_id,
		public readonly string $url,
		public readonly string $html_url,
		public readonly string $followers_url,
		public readonly string $following_url,
		public readonly string $gists_url,
		public readonly string $starred_url,
		public readonly string $subscriptions_url,
		public readonly string $organizations_url,
		public readonly string $repos_url,
		public readonly string $events_url,
		public readonly string $received_events_url,
		public readonly string $type,
		public readonly bool $site_admin
	) {
	}
}
