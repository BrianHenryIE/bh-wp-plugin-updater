<?php
/**
 * Refresh licence and product information daily.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\WP_Includes;

use BrianHenryIE\WP_Plugin_Updater\API_Interface;
use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;

use function BrianHenryIE\WP_Plugin_Updater\str_dash_to_underscore;

/**
 * Manage actions related to wp-cron scheduled and background tasks.
 */
class Cron {

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api The main plugin functions, which the cron job will call.
	 * @param Settings_Interface $settings The plugin settings. The slug is used for the cron job name.
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
	) {
	}

	/**
	 * Get the name of the cron job that will be scheduled.
	 *
	 * The WordPress convention, when searching for {@see wp_schedule_event()}, is to use cron job names with underscores.
	 *
	 * {plugin_slug}_update_check
	 */
	public function get_update_check_cron_job_name(): string {
		return str_dash_to_underscore(
			sprintf(
				'%s_%s',
				$this->settings->get_plugin_slug(),
				'update_check'
			)
		);
	}

	/**
	 * Get the name of the cron job that will be scheduled for an immediate update check.
	 *
	 * I think if the repeating one's name is used it might remove the schedule.
	 *
	 * {plugin_slug}_update_check_immediate
	 */
	public function get_immediate_update_check_cron_job_name(): string {
		return "{$this->get_update_check_cron_job_name()}_immediate";
	}

	/**
	 * When the plugin is activated, schedule a daily update check.
	 *
	 * @see activate_plugin()
	 * @hooked activate_{plugin_slug}
	 */
	public function register_cron_job(): void {
		if ( wp_next_scheduled( $this->get_update_check_cron_job_name() ) ) {
			return;
		}

		wp_schedule_event(
			time(),
			'daily',
			$this->get_update_check_cron_job_name()
		);
	}

	/**
	 * Handle the cron job invocation – refresh the licence details and fetch the product information.
	 *
	 * @see get_update_check_cron_job_name()
	 * @hooked {plugin_slug}_update_check
	 */
	public function handle_update_check_cron_job(): void {
		$this->api->get_licence_details( true );
		$this->api->get_plugin_information( true );
		$this->api->get_check_update( true );
	}
}
