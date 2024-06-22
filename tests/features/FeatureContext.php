<?php

namespace BrianHenryIE\WP_SLSWC_Client;

use WP_CLI;

class FeatureContext extends \WP_CLI\Tests\Context\FeatureContext {

	/**
	 * @Given /^a plugin located at ([^\s]+)$/
	 *
	 * @see \AmpProject\AmpWP\Tests\Behat\FeatureContext::given_a_wp_installation_with_the_amp_plugin()
	 * @see https://github.com/ampproject/amp-wp/blob/d4200c4b26446541282aef3c3cc2acd3b93674d7/tests/php/src/Behat/FeatureContext.php#L79-L93
	 */
	public function given_a_plugin_located_at( $path ) {

		$project_dir = realpath( self::get_vendor_dir() . '/../' );

		// path could be relative, the directory, or the plugin file.
		switch ( true ) {
			case is_dir( $path ):
				$source_dir = realpath( $path );
				break;
			case is_file( $path ):
				$source_dir = realpath( dirname( $path ) );
				break;
			case is_dir( $project_dir . '/' . $path ):
				$source_dir = $project_dir . '/' . $path;
				break;
			case is_file( $project_dir . '/' . $path ):
				$source_dir = $project_dir . '/' . dirname( $path );
				break;
			default:
				WP_CLI::error( "Path not found: {$path}" );
		}

		$plugin_slug = basename( $source_dir );

		// Symlink the current project folder into the WP folder as a plugin.
		$wp_plugins_dir = $this->variables['RUN_DIR'] . '/wp-content/plugins';
		$this->proc( "ln -s {$source_dir} {$wp_plugins_dir}/{$plugin_slug}" )->run_check();

		// Activate the plugin.
		$this->proc( "wp plugin activate {$plugin_slug}" )->run_check();
	}

	/**
	 * @Given /^a request to (.*?) responds? with (.*)$/
	 */
	public function given_a_request_to_a_url_respond_with_file( $url_substring_pattern, $remote_request_response_file ) {

		$project_dir = realpath( self::get_vendor_dir() . '/../' );

		switch ( true ) {
			case is_file( $remote_request_response_file ):
				$response_file = realpath( $remote_request_response_file );
				break;
			case is_file( ltrim( $remote_request_response_file, './' ) ):
				$response_file = realpath( ltrim( $remote_request_response_file, './' ) );
				break;
			case is_file( $project_dir . '/' . $remote_request_response_file ):
				$response_file = $project_dir . '/' . $remote_request_response_file;
				break;
			default:
				WP_CLI::error( "File not found: {$remote_request_response_file}" );
		}

		if ( substr( $url_substring_pattern, 0, 1 ) !== substr( $url_substring_pattern, -1 ) ) {
			$url_substring_pattern = '/' . preg_quote( $url_substring_pattern, '/' ) . '/';
		}

		$mu_plugin_name = basename( $remote_request_response_file );

		$mu_php = <<<MU_PHP
<?php
/**
 * Plugin Name: $mu_plugin_name
 * Description: Mock a response for a remote request matching `$url_substring_pattern`.
 */
 
/**
 * Filter the HTTP request to return a mock response.
 *
 * @hooked pre_http_request
 * @see \WP_Http::request()
 * 
 * @param false|array \$pre
 * @param array \$parsed_args
 * @param string \$url The request URL.
 * 
 * @return false|array{headers:array,body:string,response:array{code:int|false,message:bool|string},cookies:array,http_response:null|array}
 */
add_filter( 'pre_http_request', function( \$pre, \$parsed_args, \$url ) { 
	if ( 1 !== preg_match( '$url_substring_pattern', \$url ) ) {
		return \$pre;
	}

	return include '$response_file';
}, 10, 3 );
MU_PHP;

		file_put_contents(
			$this->variables['RUN_DIR'] . '/wp-content/mu-plugins/' . $mu_plugin_name,
			$mu_php
		);
	}
}
