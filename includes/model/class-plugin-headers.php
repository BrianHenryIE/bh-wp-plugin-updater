<?php
/**
 * Strongly typed representation of the plugin headers.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Model;

/**
 * The only required header is 'Name', so all others are nullable.
 *
 * @phpstan-type Plugin_Headers_Array array{Name:string, PluginURI?:string|null, Version?:string|null, Description?:string|null, Author?:string|null, AuthorURI?:string|null, TextDomain?:string|null, DomainPath?:string|null, Network?:string|null, RequiresWP?:string|null, RequiresPHP?:string|null, UpdateURI?:string|null, RequiresPlugins?:string|null}
 * @phpstan-type Plugin_Headers_Class_As_Array array{name:string, plugin_uri:string|null, version:string|null, description:string|null, author:string|null, author_uri:string|null, text_domain:string|null, domain_path:string|null, network:string|null, requires_wp:string|null, requires_php:string|null, update_uri:string|null, requires_plugins:null|array<string>}
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

	/**
	 * Parse the plugin headers from a string.
	 *
	 * WordPress's {@see get_file_data()} which parses the headers from a file, requires an actual file, so we
	 * write to the temp directory first and then delete it.
	 *
	 * @param string $plugin_php_file The contents of a main plugin file.
	 */
	public static function from_file_string( string $plugin_php_file ): Plugin_Headers {
		try {
			$plugin_filename      = 'plugin_headers';
			$tmp_plugin_file_path = tempnam( get_temp_dir(), $plugin_filename );
			file_put_contents( $tmp_plugin_file_path, $plugin_php_file );
			return self::from_file( $tmp_plugin_file_path );
		} finally {
			if ( isset( $tmp_plugin_file_path ) && file_exists( $tmp_plugin_file_path ) ) {
				wp_delete_file( $tmp_plugin_file_path );
			}
		}
	}

	/**
	 * Given a filepath, parse its plugin headers.
	 *
	 * @param string $plugin_php_filepath An absolute path, ending in something like `wp-content/plugins/my-plugin/my-plugin.php`.
	 */
	public static function from_file( string $plugin_php_filepath ): Plugin_Headers {

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

		return self::from_array( get_file_data( $plugin_php_filepath, $default_headers ) );
	}

	/**
	 * Given an array of headers, construct this object.
	 *
	 * @param Plugin_Headers_Array&array $header_array A PHP array of the standard plugin headers.
	 */
	public static function from_array( array $header_array ): Plugin_Headers {
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
				: null
		);
	}
}
