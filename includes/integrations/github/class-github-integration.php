<?php
/**
 * GitHub integration.
 *
 * If a plugin's header contains a GitHub URI, this class will be used to fetch the latest release from GitHub.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub;

use BrianHenryIE\WP_Plugin_Updater\Exception\Plugin_Updater_Exception;
use BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model\Release;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Interface;
use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Syntatis\WPPluginReadMeParser\Parser as Readme_Parser;

class GitHub_Integration implements Integration_Interface {
	use LoggerAwareTrait;

	protected GitHub_API $github_api;

	protected ?Release $release;
	protected ?string $changelog_text = null;

	protected ?Readme_Parser $readme;
	protected ?Plugin_Headers $plugin_headers;

	/**
	 * Constructor.
	 *
	 * @param ClientInterface         $http_client
	 * @param RequestFactoryInterface $request_factory
	 * @param StreamFactoryInterface  $stream_factory
	 * @param Settings_Interface      $settings
	 * @param LoggerInterface         $logger
	 */
	public function __construct(
		protected ClientInterface $http_client,
		protected RequestFactoryInterface $request_factory,
		protected StreamFactoryInterface $stream_factory,
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->setLogger( $logger );

		$this->github_api = new GitHub_API(
			$this->http_client,
			$this->request_factory,
			$this->stream_factory,
			$this->settings,
			$logger
		);
	}

	public function activate_licence( Licence $licence ) {
		// TODO: Implement activate_licence() method.
		return new Licence();
	}

	public function deactivate_licence( Licence $licence ) {
		// TODO: Implement deactivate_licence() method.
		return new Licence();
	}

	public function refresh_licence_details( Licence $licence ): Licence {
		// TODO: Implement refresh_licence_details() method.
		return new Licence();
	}

	public function get_remote_check_update( Licence $licence ): ?Plugin_Update {

		$release = $this->get_release();

		if(is_null( $release ) ) {
			return null;
		}

		$plugin_headers = $this->get_plugin_headers();

		$readme = $this->get_readme();

		// If not assets are attached to the GitHub release!
		if(empty($release->assets)){
			return null;
		}

		return new Plugin_Update(
			id: null,
			slug: $this->settings->get_plugin_slug(),
			version: ltrim( $release->tag_name, 'v' ),
			url: $plugin_headers->plugin_uri,
			package: $release->assets[0]->browser_download_url,
			new_version: $release->tag_name,
			tested: $readme->tested,
			requires_php: $readme->requires_php ?? $plugin_headers->requires_php ?? null,
			autoupdate: null,
			icons: null,
			banners: null,
			banners_rtl: null,
			translations: null,
		);
	}

	/**
	 * Fetches the github.com/user/repo/plugin-file.php for the latest release, parses the headers, and use the data
	 * to build a Plugin_Info object.
	 *
	 * @see Integration_Interface::get_remote_product_information()
	 * @param Licence $licence
	 * @return ?Plugin_Info
	 */
	public function get_remote_product_information( Licence $licence ): ?Plugin_Info {

		$release        = $this->get_release();
		$plugin_headers = $this->get_plugin_headers();

		if ( is_null( $this->plugin_headers ) ) {
			throw new Plugin_Updater_Exception( 'Failed to update product information from GitHub' );
		}

		return GitHub_WP_Plugin_Info::from_release(
			$this->settings->get_plugin_slug(),
			$this->settings->get_licence_server_host(),
			$release,
			$plugin_headers,
		);
	}


	protected function get_release(): ?Release {
		if ( ! isset( $this->release ) ) {
			$this->release = $this->github_api->get_release();
		}
		return $this->release;
	}

	protected function get_plugin_headers(): ?Plugin_Headers {
		if ( ! isset( $this->plugin_headers ) ) {
			$this->plugin_headers = $this->github_api->get_plugin_headers();
		}
		return $this->plugin_headers;
	}
	protected function get_readme(): ?Readme_Parser {
		if ( ! isset( $this->readme ) ) {
			$this->readme = $this->github_api->get_readme();
		}
		return $this->readme;
	}
}
