<?php
namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model;

class Release {

	/**
	 * @param Assets[] $assets
	 */
	public function __construct(
		protected string $url,
		protected string $assets_url,
		protected string $upload_url,
		protected string $html_url,
		protected int $id,
		protected User $author,
		protected string $node_id,
		protected string $tag_name,
		protected string $target_commitish,
		protected string $name,
		protected bool $draft,
		protected bool $prerelease,
		protected string $created_at,
		protected string $published_at,
		protected array $assets,
		protected string $tarball_url,
		protected string $zipball_url,
		protected string $body,
		protected int $mentions_count
	) {
	}

	public function get_url(): string {
		return $this->url;
	}

	public function get_assets_url(): string {
		return $this->assets_url;
	}

	public function get_upload_url(): string {
		return $this->upload_url;
	}

	public function get_html_url(): string {
		return $this->html_url;
	}

	public function get_id(): int {
		return $this->id;
	}

	public function get_author(): User {
		return $this->author;
	}

	public function get_node_id(): string {
		return $this->node_id;
	}

	public function get_tag_name(): string {
		return $this->tag_name;
	}

	public function get_target_commitish(): string {
		return $this->target_commitish;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function is_draft(): bool {
		return $this->draft;
	}

	public function is_prerelease(): bool {
		return $this->prerelease;
	}

	public function get_created_at(): string {
		return $this->created_at;
	}

	public function get_published_at(): string {
		return $this->published_at;
	}

	/**
	 * @return Assets[]
	 */
	public function get_assets(): array {
		return $this->assets;
	}

	public function get_tarball_url(): string {
		return $this->tarball_url;
	}

	public function get_zipball_url(): string {
		return $this->zipball_url;
	}

	public function get_body(): string {
		return $this->body;
	}

	public function get_mentions_count(): int {
		return $this->mentions_count;
	}
}
