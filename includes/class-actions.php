<?php
/**
 * WordPress hooks and filters for licence management.
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\Admin\Admin_Assets;
use BrianHenryIE\WP_SLSWC_Client\Admin\Licence_Management_Tab;
use BrianHenryIE\WP_SLSWC_Client\Admin\Plugins_Page;
use BrianHenryIE\WP_SLSWC_Client\Admin\Plugins_Page_View_Details;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\CLI;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\Cron;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\Rest;
use BrianHenryIE\WP_SLSWC_Client\WP_Includes\WordPress_Updater;
use Exception;
use Psr\Log\LoggerInterface;
use WP_CLI;

/**
 * `add_action` and `add_filter` hooks.
 */
class Actions {
	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api The updater's main functions.
	 * @param Settings_Interface $settings The configuration for the updater component.
	 * @param LoggerInterface    $logger A PSR logger.
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
		protected LoggerInterface $logger,
	) {
		$this->add_plugins_page_modal_hooks();
		$this->add_rest_hooks();
		$this->add_assets_hooks();
		$this->add_cron_hooks();
		$this->add_wordpress_updater_hooks();
		$this->add_cli_hooks();
	}

	/**
	 * Add hooks to schedule and handle a daily update check.
	 */
	protected function add_cron_hooks(): void {
		$cron = new Cron(
			$this->api,
			$this->settings
		);

		add_action(
			"activate_{$this->settings->get_plugin_slug()}",
			array( $cron, 'register_cron_job' )
		);

		add_action(
			$cron->get_update_check_cron_job_name(),
			array( $cron, 'handle_update_check_cron_job' )
		);
	}

	/**
	 * Add hooks to add the product and licence information to the WordPress `get_plugins()` information array.
	 */
	protected function add_wordpress_updater_hooks(): void {

		$plugin_update = new WordPress_Updater(
			$this->api,
			$this->settings
		);

		add_filter(
			'pre_set_site_transient_update_plugins',
			array( $plugin_update, 'add_product_data_to_wordpress_plugin_information' ),
			10,
			2
		);
	}

	/**
	 * Add hooks to add the "View details" modal to `plugins.php`.
	 *
	 * TODO: check the use of `plugins_api` vs `plugins_api_result` filters, which is correct?
	 */
	protected function add_plugins_page_modal_hooks(): void {

		$view_details = new Plugins_Page_View_Details( $this->api, $this->settings );
		add_filter( 'plugins_api', array( $view_details, 'add_plugin_modal_data' ), 10, 3 );

		$licence_tab = new Licence_Management_Tab( $this->api, $this->settings );
		add_filter( 'plugins_api_result', array( $licence_tab, 'add_licence_tab' ), 10, 3 );

		$plugins_page = new Plugins_Page(
			$this->api,
			$this->settings
		);
		add_filter( 'plugin_auto_update_setting_html', array( $plugins_page, 'plugin_auto_update_setting_html' ), 10, 2 );

		add_action(
			"in_plugin_update_message-{$this->settings->get_plugin_basename()}",
			array( $plugins_page, 'append_licence_link_to_auto_update_unavailable_text' ),
			10,
			2
		);
	}

	/**
	 * Enqueue the JavaScript to handle the licence tab on the plugins.php page.
	 */
	protected function add_assets_hooks(): void {
		$assets = new Admin_Assets();

		add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_script' ) );
		add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_styles' ) );
	}

	/**
	 * Add hooks for handling AJAX requests from the plugins.php licence management tab.
	 */
	protected function add_rest_hooks(): void {
		$rest = new Rest( $this->api, $this->settings );

		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
	}

	/**
	 * Add hooks to register WP CLI commands.
	 */
	protected function add_cli_hooks(): void {

		if ( ! class_exists( WP_CLI::class ) ) {
			return;
		}

		$cli_base = $this->settings->get_cli_base();

		if ( is_null( $cli_base ) ) {
			return;
		}

		$cli = new CLI( $this->api );

		try {
			WP_CLI::add_command( "{$cli_base} licence get-status", array( $cli, 'get_licence_status' ) );
			WP_CLI::add_command( "{$cli_base} licence get-key", array( $cli, 'get_licence_key' ) );
			WP_CLI::add_command( "{$cli_base} licence set-key", array( $cli, 'set_licence_key' ) );
			WP_CLI::add_command( "{$cli_base} licence deactivate", array( $cli, 'deactivate' ) );
			WP_CLI::add_command( "{$cli_base} licence activate", array( $cli, 'activate' ) );
			WP_CLI::add_command( "{$cli_base} product-information update", array( $cli, 'get_product_details' ) );
		} catch ( Exception $e ) {
			$this->logger->error(
				'Failed to register WP CLI commands: ' . $e->getMessage(),
				array( 'exception' => $e )
			);
		}
	}
}
