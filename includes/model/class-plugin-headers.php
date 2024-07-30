<?php
/**
 * Strongly typed representation of the plugin headers.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Model;

/**
 * The only required header is 'Name', so all others are nullable
 */
class Plugin_Headers {

	public function __construct(
		protected array $header_array,
	) {
	}
	public function get_by_name( string $header_name ): ?string {
		return $this->header_array[ $header_name ] ?? null;
	}

	/** Plugin Name */
	public function get_name(): ?string {
		return $this->header_array['Name'] ?? null;
	}

	/** Plugin URI */
	public function get_plugin_uri(): ?string {
		return $this->header_array['PluginURI'] ?? null;
	}

	/** Version */
	public function get_version(): ?string {
		return $this->header_array['Version'] ?? null;
	}

	/** Description */
	public function get_description(): ?string {
		return $this->header_array['Description'] ?? null;
	}

	/** Author */
	public function get_author(): ?string {
		return $this->header_array['Author'] ?? null;
	}

	/** Author URI */
	public function get_author_uri(): ?string {
		return $this->header_array['AuthorURI'] ?? null;
	}

	/** Text Domain */
	public function get_text_domain(): ?string {
		return $this->header_array['TextDomain'] ?? null;
	}

	/** Domain Path */
	public function get_domain_path(): ?string {
		return $this->header_array['DomainPath'] ?? null;
	}

	/** Network */
	public function get_network(): ?string {
		return $this->header_array['Network'] ?? null;
	}

	/** Requires at least */
	public function get_requires_wp(): ?string {
		return $this->header_array['RequiresWP'] ?? null;
	}

	/** Requires PHP */
	public function get_requires_php(): ?string {
		return $this->header_array['RequiresPHP'] ?? null;
	}

	/** Update URI */
	public function get_update_uri(): ?string {
		return $this->header_array['UpdateURI'] ?? null;
	}

	/** Requires Plugins */
	public function get_requires_plugins(): array {
		return $this->header_array['RequiresPlugins'] ?? false
			? array_map( 'trim', explode( ',', $this->header_array['RequiresPlugins'] ) )
			: array();
	}
}
