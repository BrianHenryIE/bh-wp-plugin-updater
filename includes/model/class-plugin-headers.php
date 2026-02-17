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

	/**
	 * @param string  $name Plugin Name
	 * @param ?string $plugin_uri
	 * @param ?string $version
	 * @param ?string $description
	 * @param ?string $author
	 * @param ?string $author_uri
	 * @param ?string $text_domain
	 * @param ?string $domain_path
	 * @param ?string $network
	 * @param ?string $requires_wp
	 * @param ?string $requires_php
	 * @param ?string $update_uri
	 * @param ?array  $requires_plugins
	 */
	public function __construct(
		/** Plugin Name */
		public readonly string $name,
		/** Plugin URI */
		public readonly ?string $plugin_uri = null,
		/** Version */
		public readonly ?string $version = null,
		/** Description */
		public readonly ?string $description = null,
		/** Author */
		public readonly ?string $author = null,
		/** Author URI */
		public readonly ?string $author_uri = null,
		/** Text Domain */
		public readonly ?string $text_domain = null,
		/** Domain Path */
		public readonly ?string $domain_path = null,
		/** Network */
		public readonly ?string $network = null,
		/** Requires at least */
		public readonly ?string $requires_wp = null,
		/** Requires PHP */
		public readonly ?string $requires_php = null,
		/** Update URI */
		public readonly ?string $update_uri = null,
		/** Requires Plugins */
		public readonly ?array $requires_plugins = null,
	) {
	}

	public static function from_array( array $header_array ): self {
		return new Plugin_Headers(
			name: $header_array['Name'],
			plugin_uri: $header_array['PluginURI'] ?? null,
			version: $header_array['Version'] ?? null,
			description: $header_array['Description'] ?? null,
			author: $header_array['Author'] ?? null,
			author_uri: $header_array['AuthorURI'] ?? null,
			text_domain: $header_array['TextDomain'] ?? null,
			domain_path: $header_array['DomainPath'] ?? null,
			network: $header_array['Network'] ?? null,
			requires_wp: $header_array['RequiresWP'] ?? null,
			requires_php: $header_array['RequiresPHP'] ?? null,
			update_uri: $header_array['UpdateURI'] ?? null,
			requires_plugins: isset( $header_array['RequiresPlugins'] )
				? array_map( 'trim', explode( ',', $header_array['RequiresPlugins'] ) )
				: array()
		);
	}
	public static function from_file_string( string $plugin_php_file ): Plugin_Headers {

		/**
		 * TODO: remove unused.
		 *
		 * @see get_plugin_data()
		 */
		$default_headers = array(
			'Name'            => 'Plugin Name',
			'PluginURI'       => 'Plugin URI',
			'Version'         => 'Version',
			'Description'     => 'Description',
			'Author'          => 'Author',
			'AuthorURI'       => 'Author URI',
			'TextDomain'      => 'Text Domain',
			'DomainPath'      => 'Domain Path',
			'Network'         => 'Network',
			'RequiresWP'      => 'Requires at least',
			'RequiresPHP'     => 'Requires PHP',
			'UpdateURI'       => 'Update URI',
			'RequiresPlugins' => 'Requires Plugins',
		);

		// write to tmp dir
		$plugin_filename      = 'plugin_headers';
		$tmp_plugin_file_path = get_temp_dir() . $plugin_filename;
		file_put_contents( $tmp_plugin_file_path, $plugin_php_file );
		$plugin_headers = self::from_array( get_file_data( $tmp_plugin_file_path, $default_headers ) );
		unlink( $tmp_plugin_file_path );

		return $plugin_headers;
	}
}
