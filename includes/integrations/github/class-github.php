<?php
/**
 * GitHub integration.
 *
 * If a plugin's header contains a GitHub URI, this class will be used to fetch the latest release from GitHub.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub;

use BrianHenryIE\WP_Plugin_Updater\Integrations\GitHub\Model\Release;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Interface;
use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;
use Github\Client;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Syntatis\WPPluginReadMeParser\Parser as Readme_Parser;

class GitHub implements Integration_Interface {
	use LoggerAwareTrait;

	protected \Github\Client $client;

	protected Release $release;
	protected ?string $changelog_text = null;

	protected ?Readme_Parser $readme = null;
	protected ?array $plugin_headers = null;

	public function __construct(
		ClientInterface $http_client,
		protected Settings_Interface $settings,
		LoggerInterface $logger,
	) {
		$this->client = \Github\Client::createWithHttpClient( $http_client );
		$this->setLogger( $logger );
	}

	protected function update(): void {

		if ( 1 !== preg_match( '/github.com\/(?<user>.*?)\/(?<repo>[^\/]*)/', $this->settings->get_licence_server_host(), $output_array ) ) {
			throw new \Exception( 'Failed to parse GitHub URI user and repo from ' . $this->settings->get_licence_server_host() );
		}

		$user = $output_array['user'];
		$repo = $output_array['repo'];

		// TODO: catch not-found exception, no connection etc.
		/**
		 * @see Client::api()
		 * @see \Github\Api\Repo::releases()
		 * @see \Github\Api\Repository\Releases::all()
		 */
		$response = $this->client->api( 'repo' )->releases()->all( $user, $repo );

		// TODO: remove '->with' that are not needed.
		$factory_registry = new FactoryRegistry();
		$mapper           = JsonMapperBuilder::new()
											->withDocBlockAnnotationsMiddleware()
											->withObjectConstructorMiddleware( $factory_registry )
											->withPropertyMapper( new PropertyMapper( $factory_registry ) )
											->withTypedPropertiesMiddleware()
											->withNamespaceResolverMiddleware()
											->build();

		/** @var Release[] $release_object */
		$releases = $mapper->mapToClassArrayFromString( json_encode( $response ), Release::class );

		$allow_beta = false;

		if ( $allow_beta ) {
			$this->release = $releases[0];
		} else {
			foreach ( $releases as $release ) {
				if ( ! $release->is_prerelease() ) {
					$this->release = $release;
					break;
				}
			}
		}

		// get changelog.md from GitHub at tag commit
		$changelog_url              = "https://raw.githubusercontent.com/{$user}/{$repo}/{$this->release->get_tag_name()}/CHANGELOG.md";
		$changelog_request_response = wp_remote_get( $changelog_url );
		if ( 200 === wp_remote_retrieve_response_code( $changelog_request_response ) ) {
			$this->changelog_text = $changelog_request_response['body'];
		}

		// TODO: this is case sensitive.
		// get readme.txt from GitHub at tag commit
		$readme_url              = "https://raw.githubusercontent.com/{$user}/{$repo}/{$this->release->get_tag_name()}/README.txt";
		$readme_request_response = wp_remote_get( $readme_url );
		if ( 200 === wp_remote_retrieve_response_code( $readme_request_response ) ) {
			$this->readme = new Readme_Parser( $readme_request_response['body'] );
		}

		$plugin_file_name             = explode( '/', $this->settings->get_plugin_basename() )[1];
		$plugin_file_url              = "https://raw.githubusercontent.com/{$user}/{$repo}/{$this->release->get_tag_name()}/{$plugin_file_name}";
		$plugin_file_request_response = wp_remote_get( $plugin_file_url );
		if ( 200 === wp_remote_retrieve_response_code( $plugin_file_request_response ) ) {

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
			$tmp_plugin_file_path = get_temp_dir() . $plugin_file_name;
			file_put_contents( $tmp_plugin_file_path, $plugin_file_request_response['body'] );
			$this->plugin_headers = new Plugin_Headers( get_file_data( $tmp_plugin_file_path, $default_headers ) );
			unlink( $tmp_plugin_file_path );
		}
	}

	protected function get_release(): Release {
		if ( ! isset( $this->release ) ) {
			$this->update();
		}
		return $this->release;
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

	public function get_remote_check_update( Licence $licence ): ?Plugin_Update_Interface {

		$release = $this->get_release();

		return new Plugin_Update(
			id: null,
			slug: $this->settings->get_plugin_slug(),
			version: ltrim( $release->get_tag_name(), 'v' ),
			url: $this->plugin_headers->get_plugin_uri(),
			package: $release->get_assets()[0]->get_browser_download_url(),
			tested: $this->readme->tested,
			requires_php: $this->readme->requires_php ?? $this->plugin_headers->get_requires_php() ?? null,
			autoupdate: null,
			icons: null,
			banners: null,
			banners_rtl: null,
			translations: null,
		);
	}

	public function get_remote_product_information( Licence $licence ): ?Plugin_Info_Interface {
		// TODO: Implement get_remote_product_information() method.
		return null;
	}
}
