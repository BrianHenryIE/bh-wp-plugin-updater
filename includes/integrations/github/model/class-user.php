<?php
/**
 * @see https://github.com/github/rest-api-description
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model;

class User {

	public function __construct(
		protected string $login,
		protected int $id,
		protected string $node_id,
		protected string $avatar_url,
		protected string $gravatar_id,
		protected string $url,
		protected string $html_url,
		protected string $followers_url,
		protected string $following_url,
		protected string $gists_url,
		protected string $starred_url,
		protected string $subscriptions_url,
		protected string $organizations_url,
		protected string $repos_url,
		protected string $events_url,
		protected string $received_events_url,
		protected string $type,
		protected bool $site_admin
	) {
	}

	public function get_login(): string {
		return $this->login;
	}

	public function get_id(): int {
		return $this->id;
	}

	public function get_node_id(): string {
		return $this->node_id;
	}

	public function get_avatar_url(): string {
		return $this->avatar_url;
	}

	public function get_gravatar_id(): string {
		return $this->gravatar_id;
	}

	public function get_url(): string {
		return $this->url;
	}

	public function get_html_url(): string {
		return $this->html_url;
	}

	public function get_followers_url(): string {
		return $this->followers_url;
	}

	public function get_following_url(): string {
		return $this->following_url;
	}

	public function get_gists_url(): string {
		return $this->gists_url;
	}

	public function get_starred_url(): string {
		return $this->starred_url;
	}

	public function get_subscriptions_url(): string {
		return $this->subscriptions_url;
	}

	public function get_organizations_url(): string {
		return $this->organizations_url;
	}

	public function get_repos_url(): string {
		return $this->repos_url;
	}

	public function get_events_url(): string {
		return $this->events_url;
	}

	public function get_received_events_url(): string {
		return $this->received_events_url;
	}

	public function get_type(): string {
		return $this->type;
	}

	public function is_site_admin(): bool {
		return $this->site_admin;
	}
}
