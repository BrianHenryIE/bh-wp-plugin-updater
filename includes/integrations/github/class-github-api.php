<?php
/**
 * GitHub integration.
 *
 * If a plugin's header contains a GitHub URI, this class will be used to fetch the latest release from GitHub.
 *
 * TODO: authentication. Claude says "GitHub's unauthenticated REST API rate-limit is 60 requests/hour per IP, which is very easy to exhaust on busy shared hosts."
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub;

use BrianHenryIE\WP_Plugin_Updater\Exception\Plugin_Updater_Exception;
use BrianHenryIE\WP_Plugin_Updater\Helpers\JsonMapper\JsonMapper_Helper;
use BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model\Release;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use Github\Client as GitHub_Client;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Syntatis\WPPluginReadMeParser\Parser as Readme_Parser;

class GitHub_API {
	use LoggerAwareTrait;

	protected string $github_username;
	protected string $github_repository;

	protected ?Release $release;
	protected ?string $changelog_text;

	protected ?Readme_Parser $readme;
	protected ?Plugin_Headers $plugin_headers;

	/**
	 * Constructor.
	 *
	 * @param Settings_Interface $settings
	 * @param LoggerInterface    $logger
	 */
	public function __construct(
		protected GitHub_Client $client,
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );
		$this->init();
	}

	protected function init(): void {

		/** @var array{user?:string, repo?:string} $output_array */
		if (
			1 !== preg_match( '/github.com\/(?<user>.*?)\/(?<repo>[^\/]*)/', $this->settings->get_licence_server_host(), $output_array )
			|| ! isset( $output_array['user'], $output_array['repo'] )
		) {
			throw new Plugin_Updater_Exception( 'Failed to parse GitHub URI user and repo from ' . $this->settings->get_licence_server_host() );
		}

		$this->github_username   = $output_array['user'];
		$this->github_repository = $output_array['repo'];
	}

	public function get_plugin_headers(): ?Plugin_Headers {
		if ( ! isset( $this->plugin_headers ) ) {
			$this->update();
		}
		if ( ! isset( $this->plugin_headers ) ) {
			$this->plugin_headers = null;
		}
		return $this->plugin_headers;
	}

	public function get_release(): ?Release {
		if ( ! isset( $this->release ) ) {
			$this->update();
		}
		if ( ! isset( $this->release ) ) {
			$this->release = null;
		}
		return $this->release;
	}

	public function get_readme(): ?Readme_Parser {
		if ( ! isset( $this->readme ) ) {
			$this->update();
		}
		if ( ! isset( $this->readme ) ) {
			$this->readme = null;
		}
		return $this->readme;
	}

	public function get_changelog_text(): ?string {
		if ( ! isset( $this->changelog_text ) ) {
			$this->update();
		}
		if ( ! isset( $this->changelog_text ) ) {
			$this->changelog_text = null;
		}
		return $this->changelog_text;
	}

	/**
	 * @param string $user
	 * @param string $repo
	 *
	 * @return Release[]
	 * @throws Plugin_Updater_Exception
	 * @throws \JsonMapper\Exception\BuilderException
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	protected function fetch_releases( string $user, string $repo ): array {

		/**
		 * @see Client::api()
		 * @see \Github\Api\Repo::releases()
		 * @see \Github\Api\Repository\Releases::all()
		 */
		$response = $this->client->api( 'repo' )->releases()->all( $user, $repo );

		$json_string_response = wp_json_encode( $response );

		if ( ! $json_string_response ) {
			throw new Plugin_Updater_Exception( 'Unable to parse JSON response.' );
		}

		$mapper = ( new JsonMapper_Helper() )->build();

		return $mapper->mapToClassArrayFromString( $json_string_response, Release::class );
	}

	/**
	 * Download an arbitrary file, e.g. changelog.md, from GitHub at tag commit.
	 *
	 * @param string $user
	 * @param string $repo
	 * @param string $tag_name
	 * @param string $path
	 */
	protected function fetch_raw_file( string $user, string $repo, string $tag_name, string $path ): string {

		$url              = "https://raw.githubusercontent.com/{$user}/{$repo}/{$tag_name}/{$path}";
		$request_response = wp_remote_get( $url );
		if ( 200 === wp_remote_retrieve_response_code( $request_response ) ) {
			return wp_remote_retrieve_body( $request_response );
		}

		throw new Plugin_Updater_Exception( 'Unable to get raw file: ' . $path );
	}

	/**
	 * @param array $releases
	 * @param bool  $allow_beta
	 * @return Release[]
	 */
	protected function filter_releases( array $releases, bool $allow_beta ): array {

		return $allow_beta
			? $releases
			: array_filter( $releases, fn( $release ) => ! $release->prerelease );
	}

	/**
	 * get readme.txt from GitHub at tag commit
	 *
	 * @param string $user
	 * @param string $repo
	 * @param string $tag_name
	 * @return Readme_Parser|null
	 */
	protected function fetch_readme( string $user, string $repo, string $tag_name ): ?Readme_Parser {

		$readme_names = array( 'readme.txt', 'README.txt' );
		foreach ( $readme_names as $readme_name ) {
			try {
				$readme = $this->fetch_raw_file( $user, $repo, $tag_name, $readme_name );
				return new Readme_Parser( $readme );
			} catch ( \Throwable ) {
			}
		}
		return null;
	}

	protected function fetch_plugin_headers( string $user, string $repo, string $tag_name, string $plugin_filename ): ?Plugin_Headers {

		$plugin_file = $this->fetch_raw_file( $user, $repo, $tag_name, $plugin_filename );

		return Plugin_Headers::from_file_string( $plugin_file );
	}

	/**
	 * @return void
	 * @throws Plugin_Updater_Exception
	 * @throws \JsonMapper\Exception\BuilderException
	 */
	protected function update(): void {

		$releases = $this->fetch_releases( $this->github_username, $this->github_repository );

		$allow_beta = false;

		$this->release = $this->filter_releases( $releases, $allow_beta )[0] ?? null;

		$this->changelog_text = $this->fetch_raw_file( $this->github_username, $this->github_repository, $this->release->tag_name, 'CHANGELOG.md' );

		$this->readme = $this->fetch_readme( $this->github_username, $this->github_repository, $this->release->tag_name );

		$plugin_file_name     = explode( '/', $this->settings->get_plugin_basename() )[1];
		$this->plugin_headers = $this->fetch_plugin_headers( $this->github_username, $this->github_repository, $this->release->tag_name, $plugin_file_name );
	}
}
