<?php


namespace BrianHenryIE\WP_Plugin_Updater\Integrations\SLSWC\Model;

use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update_Interface;

class Software_Details {
	public function __construct(
		protected string $name,
		protected string $id,
		protected string $slug,
		protected string $plugin,
		protected string $version,
		protected string $last_updated,
		protected string $homepage,
		protected string $requires,
		protected string $tested,
		protected string $new_version,
		protected string $author,
		protected Sections $sections,
		protected Banners $banners,
		protected int $rating,
		protected array $ratings,
		protected int $num_ratings,
		protected int $active_installs,
		protected bool $external,
		protected string $package,
		protected string $download_link
	) {
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_id(): string {
		return $this->id;
	}

	public function get_slug(): string {
		return $this->slug;
	}

	public function get_plugin(): string {
		return $this->plugin;
	}

	public function get_version(): string {
		return $this->version;
	}

	public function get_last_updated(): string {
		return $this->last_updated;
	}

	public function get_homepage(): string {
		return $this->homepage;
	}

	public function get_requires(): string {
		return $this->requires;
	}

	public function get_tested(): string {
		return $this->tested;
	}

	public function get_new_version(): string {
		return $this->new_version;
	}

	public function get_author(): string {
		return $this->author;
	}

	public function get_sections(): Sections {
		return $this->sections;
	}

	public function get_banners(): Banners {
		return $this->banners;
	}

	public function get_rating(): int {
		return $this->rating;
	}

	public function get_ratings(): array {
		return $this->ratings;
	}

	public function get_num_ratings(): int {
		return $this->num_ratings;
	}

	public function get_active_installs(): int {
		return $this->active_installs;
	}

	public function get_external(): bool {
		return $this->external;
	}

	/**
	 * `package` and `download_link` in the JSON seem to be the same.
	 */
	public function get_package(): string {
		return $this->package;
	}

	/**
	 * `package` and `download_link` in the JSON seem to be the same.
	 */
	public function get_download_link(): string {
		return $this->download_link;
	}
}
