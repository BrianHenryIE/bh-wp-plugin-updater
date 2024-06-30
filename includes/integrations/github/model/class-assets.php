<?php
/**
 * @see https://jacobdekeizer.github.io/json-to-php-generator/
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model;

class Assets {

	public function __construct(
		protected string $url,
		protected int $id,
		protected string $node_id,
		protected string $name,
		protected string $label,
		protected User $uploader,
		protected string $content_type,
		protected string $state,
		protected int $size,
		protected int $download_count,
		protected string $created_at,
		protected string $updated_at,
		protected string $browser_download_url
	) {
	}

	public function get_url(): string {
		return $this->url;
	}

	public function get_id(): int {
		return $this->id;
	}

	public function get_node_id(): string {
		return $this->node_id;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_label(): string {
		return $this->label;
	}

	public function get_uploader(): User {
		return $this->uploader;
	}

	public function get_content_type(): string {
		return $this->content_type;
	}

	public function get_state(): string {
		return $this->state;
	}

	public function get_size(): int {
		return $this->size;
	}

	public function get_download_count(): int {
		return $this->download_count;
	}

	public function get_created_at(): string {
		return $this->created_at;
	}

	public function get_updated_at(): string {
		return $this->updated_at;
	}

	public function get_browser_download_url(): string {
		return $this->browser_download_url;
	}
}
