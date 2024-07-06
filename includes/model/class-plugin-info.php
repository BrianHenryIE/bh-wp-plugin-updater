<?php

namespace BrianHenryIE\WP_Plugin_Updater\Model;

class Plugin_Info implements Plugin_Info_Interface {

	public function __construct(
		protected array $sections,
		protected string $name,
		protected string $slug,
		protected string $version,
		protected string $author,
		protected string $author_profile,
		protected array $contributors,
		protected ?string $requires,
		protected ?string $tested,
		protected ?string $requires_php,
		protected array $requires_plugins,
		protected array $compatibility,
		protected int $rating,
		protected array $ratings,
		protected int $num_ratings,
		protected string $support_url,
		protected int $support_threads,
		protected int $support_threads_resolved,
		protected int $active_installs,
		protected int $downloaded,
		protected string $last_updated,
		protected string $added,
		protected string $homepage,
		protected string $short_description,
		protected string $description,
		protected string $download_link,
		protected string $upgrade_notice,
		protected array $screenshots,
		protected array $tags,
		protected string $stable_tag,
		protected array $versions,
		protected ?string $business_model,
		protected string $repository_url,
		protected string $commercial_support_url,
		protected string $donate_link,
		protected array $banners,
		protected array $icons,
		protected array $blocks,
		protected array $block_assets,
		protected int $author_block_count,
		protected int $author_block_rating,
		protected array $blueprints,
		protected array $preview_link,
		protected array $language_packs,
		protected array $block_translations,
	) {
	}

	public function get_sections(): array {
		return $this->sections;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_slug(): string {
		return $this->slug;
	}

	public function get_version(): string {
		return $this->version;
	}

	public function get_author(): string {
		return $this->author;
	}

	public function get_author_profile(): string {
		return $this->author_profile;
	}

	public function get_contributors(): array {
		return $this->contributors;
	}

	public function get_requires(): ?string {
		return $this->requires;
	}

	public function get_tested(): ?string {
		return $this->tested;
	}

	public function get_requires_php(): ?string {
		return $this->requires_php;
	}

	public function get_requires_plugins(): array {
		return $this->requires_plugins;
	}

	public function get_compatibility(): array {
		return $this->compatibility;
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

	public function get_support_url(): string {
		return $this->support_url;
	}

	public function get_support_threads(): int {
		return $this->support_threads;
	}

	public function get_support_threads_resolved(): int {
		return $this->support_threads_resolved;
	}

	public function get_active_installs(): int {
		return $this->active_installs;
	}

	public function get_downloaded(): int {
		return $this->downloaded;
	}

	public function get_last_updated(): string {
		return $this->last_updated;
	}

	public function get_added(): string {
		return $this->added;
	}

	public function get_homepage(): string {
		return $this->homepage;
	}

	public function get_short_description(): string {
		return $this->short_description;
	}

	public function get_description(): string {
		return $this->description;
	}

	public function get_download_link(): string {
		return $this->download_link;
	}

	public function get_upgrade_notice(): string {
		return $this->upgrade_notice;
	}

	public function get_screenshots(): array {
		return $this->screenshots;
	}

	public function get_tags(): array {
		return $this->tags;
	}

	public function get_stable_tag(): string {
		return $this->stable_tag;
	}

	public function get_versions(): array {
		return $this->versions;
	}

	public function get_business_model(): ?string {
		return $this->business_model;
	}

	public function get_repository_url(): string {
		return $this->repository_url;
	}

	public function get_commercial_support_url(): string {
		return $this->commercial_support_url;
	}

	public function get_donate_link(): string {
		return $this->donate_link;
	}

	public function get_banners(): array {
		return $this->banners;
	}

	public function get_icons(): array {
		return $this->icons;
	}

	public function get_blocks(): array {
		return $this->blocks;
	}

	public function get_block_assets(): array {
		return $this->block_assets;
	}

	public function get_author_block_count(): int {
		return $this->author_block_count;
	}

	public function get_author_block_rating(): int {
		return $this->author_block_rating;
	}

	public function get_blueprints(): array {
		return $this->blueprints;
	}

	public function get_preview_link(): array {
		return $this->preview_link;
	}

	public function get_language_packs(): array {
		return $this->language_packs;
	}

	public function get_block_translations(): array {
		return $this->block_translations;
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