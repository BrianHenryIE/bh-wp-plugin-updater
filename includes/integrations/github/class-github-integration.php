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
use Github\Client as GitHub_Client;
use Github\HttpClient\Builder;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Syntatis\WPPluginReadMeParser\Parser as Readme_Parser;

class GitHub_Integration implements Integration_Interface {
	use LoggerAwareTrait;

	protected GitHub_API $github_api;

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
			$this->get_github_client(),
			$this->settings,
			$logger
		);
	}


	protected function get_github_client(): GitHub_Client {
		return GitHub_Client::createWithHttpClient( $this->http_client );
		// Issues with testing.
		return new GitHub_Client(
			new Builder( $this->http_client, $this->request_factory, $this->stream_factory )
		);
	}


	/**
	 * @see Integration_Interface::get_remote_check_update()
	 *
	 * @param Licence $licence
	 * @return Plugin_Update|null
	 */
	public function get_remote_check_update( Licence $licence ): ?Plugin_Update {

		$release = $this->github_api->get_release();

		if ( is_null( $release ) ) {
			return null;
		}

		$plugin_headers = $this->github_api->get_plugin_headers();

		$readme = $this->github_api->get_readme();

		// If no assets are attached to the GitHub release!
		if ( empty( $release->assets ) ) {
			$this->logger->info( 'No GitHub release assets' );
			return null;
		}

		$this->logger->debug( 'Returning new Plugin_Update' );

		return GitHub_Plugin_Update::from_release(
			$this->settings,
			$release,
			$plugin_headers,
			$readme,
		);
	}

	/**
	 * Fetches the github.com/user/repo/plugin-file.php for the latest release, parses the headers, and use the data
	 * to build a Plugin_Info object.
	 *
	 * @see Integration_Interface::get_remote_product_information()
	 * @param Licence $licence
	 * @return ?Plugin_Info
	 * @throws Plugin_Updater_Exception
	 */
	public function get_remote_product_information( Licence $licence ): ?Plugin_Info {

		$release        = $this->github_api->get_release();
		$plugin_headers = $this->github_api->get_plugin_headers();
		$changelog_text = $this->github_api->get_changelog_text() ?? '';

		if ( is_null( $release ) ) {
			throw new Plugin_Updater_Exception( 'Failed to fetch release information from GitHub repo' );
		}
		if ( is_null( $plugin_headers ) ) {
			throw new Plugin_Updater_Exception( 'Failed to update product information from GitHub' );
		}

		return GitHub_WP_Plugin_Info::from_release(
			$this->settings->get_plugin_slug(),
			$this->settings->get_licence_server_host(),
			$release,
			$plugin_headers,
			$changelog_text
		);
	}


	public function activate_licence( Licence $licence ): Licence {
		// TODO: Implement activate_licence() method.
		return new Licence();
	}

	public function deactivate_licence( Licence $licence ): Licence {
		// TODO: Implement deactivate_licence() method.
		return new Licence();
	}

	public function refresh_licence_details( Licence $licence ): Licence {
		// TODO: Implement refresh_licence_details() method.
		return new Licence();
	}
}
