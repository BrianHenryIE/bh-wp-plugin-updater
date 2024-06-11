<?php
/**
 * CLI commands to view/set the licence key and status, to activate/deactivate the licence, and to refresh
 * product information from the update server.
 *
 * No need for a plugin update command, since updates should work as normal through `wp plugin update`.
 *
 * `wp plugin list --fields=name,version,update_version,update_package`
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Exception\Licence_Key_Not_Set_Exception;
use BrianHenryIE\WP_SLSWC_Client\Exception\SLSWC_Exception_Abstract;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use Exception;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WP_CLI;
use WP_CLI\Formatter;

/**
 * `wp {$cli_base} licence get-status`
 */
class CLI {
	use LoggerAwareTrait;

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api The main API class where the functionality is implemented.
	 * @param Settings_Interface $settings The plugin settings.
	 * @param LoggerInterface    $logger A PSR logger.
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
		LoggerInterface $logger
	) {
		$this->setLogger( $logger );
	}

	/**
	 * Register the WP-CLI commands.
	 *
	 * If the CLI base is not set in {@see Settings_Interface::get_cli_base()} no commands will be registered.
	 */
	public function register_commands(): void {

		$cli_base = $this->settings->get_cli_base();

		if ( is_null( $cli_base ) ) {
			return;
		}

		try {
			WP_CLI::add_command( "{$cli_base} licence get", array( $this, 'get_licence' ) );
			WP_CLI::add_command( "{$cli_base} licence get-status", array( $this, 'get_licence_status' ) );
			WP_CLI::add_command( "{$cli_base} licence set-key", array( $this, 'set_licence_key' ) );
			WP_CLI::add_command( "{$cli_base} licence get-key", array( $this, 'get_licence_key' ) );
			WP_CLI::add_command( "{$cli_base} licence activate", array( $this, 'activate' ) );
			WP_CLI::add_command( "{$cli_base} licence deactivate", array( $this, 'deactivate' ) );
			WP_CLI::add_command( "{$cli_base} product-information", array( $this, 'get_product_details' ) );
			WP_CLI::add_command( "{$cli_base} check-updates", array( $this, 'get_check_updates' ) );
		} catch ( Exception $e ) {
			$this->logger->error(
				'Failed to register WP CLI commands: ' . $e->getMessage(),
				array( 'exception' => $e )
			);
		}
	}

	/**
	 * Get the licence details
	 *
	 * [--format=<format>]
	 * The serialization format for the value.
	 * ---
	 * default: table
	 * options:
	 * - table
	 * - json
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *   # Get the licence status for the plugin.
	 *   $ wp plugin-slug licence get
	 *   +-------------+---------+---------------------------+---------------------------+
	 *   | licence_key | status  | expires                   | last_updated              |
	 *   +-------------+---------+---------------------------+---------------------------+
	 *   | {my-key}    | invalid | 2024-06-11T00:22:31+00:00 | 2024-06-01T00:12:29+00:00 |
	 *   +-------------+---------+---------------------------+---------------------------+
	 *
	 *   # Get the licence status for the plugin from the licence server.
	 *   $ wp plugin-slug licence get --refresh
	 *   +-------------+---------+---------------------------+---------------------------+
	 *   | licence_key | status  | expires                   | last_updated              |
	 *   +-------------+---------+---------------------------+---------------------------+
	 *   | {my-key}    | invalid | 2024-06-11T00:22:31+00:00 | 2024-06-11T00:22:31+00:00 |
	 *   +-------------+---------+---------------------------+---------------------------+
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::get_licence_details()
	 */
	public function get_licence( array $args, array $assoc_args ): void {

		try {
			$result = $this->api->get_licence_details(
				\WP_CLI\Utils\get_flag_value( $assoc_args, 'refresh', false )
			);
		} catch ( Licence_Key_Not_Set_Exception $e ) {
			WP_CLI::error( $e->getMessage() . ' Use `wp ' . $this->settings->get_cli_base() . ' licence set-key {my-key}`.' );
		} catch ( SLSWC_Exception_Abstract $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		$formatter = new Formatter( $assoc_args, array_keys( $result->__serialize() ) );
		$formatter->display_items( array( $result->__serialize() ) );
	}

	/**
	 * Get the licence status
	 *
	 * ## EXAMPLES
	 *
	 *   # Get the licence status for the plugin.
	 *   $ wp plugin-slug licence get-status
	 *   Success: active
	 *
	 * @see API_Interface::get_licence_details()
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 */
	public function get_licence_status( array $args, array $assoc_args ): void {

		try {
			$result = $this->api->get_licence_details(
				\WP_CLI\Utils\get_flag_value( $assoc_args, 'refresh', false )
			);
		} catch ( Licence_Key_Not_Set_Exception $e ) {
			WP_CLI::error( $e->getMessage() . ' Use `wp ' . $this->settings->get_cli_base() . ' licence set-key {my-key}`.' );
		} catch ( SLSWC_Exception_Abstract $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		WP_CLI::success( $result->get_status() );
	}

	/**
	 * Get the licence key
	 *
	 * ## EXAMPLES
	 *
	 *   # Get the licence key the plugin has been configured with.
	 *   $ wp plugin-slug licence get-key
	 *   Success: 876235557140adb9b8c47b28488cda8481d98495
	 *
	 * @see API_Interface::get_licence_details()
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 */
	public function get_licence_key( array $args, array $assoc_args ): void {

		$result = $this->api->get_licence_details( false );

		WP_CLI::success( $result->get_licence_key() ?? 'No licence key set' );
	}

	/**
	 * Set the licence key.
	 *
	 * Sets the licence key the plugin should use. Conditionally activates it.
	 *
	 * A licence key cannot be validated until it is activated. I.e. an invalid licence key may be accepted.
	 *
	 * TODO: A licence key of an invalid format will be rejected.
	 *
	 * ## OPTIONS
	 *
	 *  <licence_key>
	 *  : Alphanumeric licence key.
	 *
	 * ## EXAMPLES
	 *
	 *   # Set the licence key the plugin should use.
	 *   $ wp plugin-slug licence set-key 876235557140adb9b8c47b28488cda8481d98495
	 *   Success: active
	 *
	 *   # Set the licence key the plugin should use and activate it.
	 *   $ wp plugin-slug licence set-key 876235557140adb9b8c47b28488cda8481d98495 --activate
	 *   Success: active
	 *
	 *   # Set an invalid licence key
	 *   $ wp plugin-slug licence set-key a1s2invalidp0o9
	 *   TODO
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::activate_licence()
	 */
	public function set_licence_key( array $args, array $assoc_args ): void {

		try {
			$result = $this->api->set_license_key( $args[0] );

			if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'activate', false ) ) {
				$result = $this->api->activate_licence();
			}
		} catch ( SLSWC_Exception_Abstract $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		WP_CLI::success( "Licence key set to: {$result->get_licence_key()}" );
	}

	/**
	 * Activate the already configured licence.
	 *
	 * ## EXAMPLES
	 *
	 *   # Activate this domain to use the configured licence key for Zelle plugin updates.
	 *   $ wp zelle licence activate
	 *   TODO
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::activate_licence()
	 */
	public function activate( array $args, array $assoc_args ): void {

		try {
			$result = $this->api->activate_licence();
		} catch ( Licence_Key_Not_Set_Exception $e ) {
			WP_CLI::error( $e->getMessage() . ' Use `wp ' . $this->settings->get_cli_base() . ' licence set-key {my-key}`.' );
		} catch ( SLSWC_Exception_Abstract $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		// TODO:
		WP_CLI::success( $result->get_status() );
	}

	/**
	 * Deactivate the licence.
	 *
	 * Deactivates the configured licence key.
	 *
	 * ## EXAMPLES
	 *
	 *   # Deactivate the licence key the Zelle plugin is using.
	 *   $ wp zelle licence deactivate
	 *   TODO
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::deactivate_licence()
	 */
	public function deactivate( array $args, array $assoc_args ): void {

		try {
			$result = $this->api->deactivate_licence();
		} catch ( Licence_Key_Not_Set_Exception $e ) {
			WP_CLI::error( $e->getMessage() . ' Use `wp ' . $this->settings->get_cli_base() . ' licence set-key {my-key}`.' );
		} catch ( SLSWC_Exception_Abstract $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		// TODO:
		WP_CLI::success( $result->get_status() );
	}

	/**
	 * Refresh the product information from the plugin update server.
	 *
	 * ## EXAMPLES
	 *
	 *   # Refresh the product information from the plugin update server.
	 *   $ wp plugin-slug product-information --refresh
	 *   Success: {json} TODO: display as table
	 *
	 * @param string[]             $args The unlabelled command line arguments.
	 * @param array<string,string> $assoc_args The labelled command line arguments.
	 *
	 * @see API_Interface::get_product_information()
	 */
	public function get_product_details( array $args, array $assoc_args ): void {

		$result = $this->api->get_product_information(
			\WP_CLI\Utils\get_flag_value( $assoc_args, 'refresh', false )
		);

		WP_CLI::success( wp_json_encode( $result, JSON_PRETTY_PRINT ) ?: '' );
	}

	public function get_check_updates( array $args, array $assoc_args ): void {

		$result = $this->api->get_check_update( true );

		WP_CLI::success( wp_json_encode( $result, JSON_PRETTY_PRINT ) ?: '' );
	}
}
