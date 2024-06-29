<?php
/**
 * WordPress hooks and filters for licence management.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\WP_Plugin_Updater\Admin\Admin_Assets;
use BrianHenryIE\WP_Plugin_Updater\Admin\Licence_Management_Tab;
use BrianHenryIE\WP_Plugin_Updater\Admin\Plugins_Page;
use BrianHenryIE\WP_Plugin_Updater\Admin\Plugins_Page_View_Details;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\CLI;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\Cron;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\Rest;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\WordPress_Updater;
use Psr\Log\LoggerInterface;

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

		// Is this enough? If it's deleted once, it will be deleted forever.
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

		$hostname = wp_parse_url( sanitize_url( $this->settings->get_licence_server_host() ), PHP_URL_HOST );

		$plugin_update = new WordPress_Updater(
			$this->api,
			$this->settings,
			$this->logger,
		);

		add_filter(
			'pre_set_site_transient_update_plugins',
			array( $plugin_update, 'detect_force_update' ),
			10,
			2
		);

		add_filter(
			"update_plugins_{$hostname}",
			array( $plugin_update, 'add_update_information' ),
			10,
			4
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

		// Only load the JS on the plugin information modal for this plugin.
		global $pagenow;
		if ( 'plugin-install.php' !== $pagenow
			|| ! isset( $_GET['plugin'] )
			|| sanitize_key( wp_unslash( $_GET['plugin'] ) !== $this->settings->get_plugin_slug() )
		) {
			return;
		}

		$assets = new Admin_Assets(
			$this->api,
			$this->settings,
		);

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

		$cli = new CLI( $this->api, $this->settings, $this->logger );

		add_action( 'cli_init', array( $cli, 'register_commands' ) );
	}
}
