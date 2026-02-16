<?php


namespace BrianHenryIE\WP_Plugin_Updater\Model;


class Plugin_Update implements Plugin_Update_Interface {
	public function __construct(
		protected ?string $id,
		protected string $slug,
		protected string $version,
		protected string $url,
		protected string $package,
		protected ?string $tested,
		protected ?string $requires_php,
		protected ?bool $autoupdate,
		protected ?array $icons,
		protected ?array $banners,
		protected ?array $banners_rtl,
		protected ?array $translations,
	) {
	}

	public function get_id(): ?string {
		return $this->id;
	}

	public function get_slug(): string {
		return $this->slug;
	}

	public function get_version(): string {
		return $this->version;
	}

	public function get_url(): string {
		return $this->url;
	}

	public function get_package(): string {
		return $this->package;
	}

	public function get_tested(): ?string {
		return $this->tested;
	}

	public function get_requires_php(): ?string {
		return $this->requires_php;
	}

	public function get_autoupdate(): ?bool {
		return $this->autoupdate;
	}

	public function get_icons(): ?array {
		return $this->icons;
	}

	public function get_banners(): ?array {
		return $this->banners;
	}

	public function get_banners_rtl(): ?array {
		return $this->banners_rtl;
	}

	public function get_translations(): ?array {
		return $this->translations;
	}


	/**
	 * Serialize the object to an array.
	 *
	 * @used-by serialize()
	 */
	public function __serialize(): array {
		return get_object_vars( $this );
	}
}
