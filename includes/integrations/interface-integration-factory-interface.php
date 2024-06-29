<?php
/**
 * Interface for a factory to create Integration objects.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Integrations;

use BrianHenryIE\WP_Plugin_Updater\Settings_Interface;

interface Integration_Factory_Interface {

	/**
	 * Get an integration that provides licence management and updates.
	 *
	 * @param Settings_Interface $settings The plugin's licence server settings.
	 */
	public function get_integration( Settings_Interface $settings ): Integration_Interface;
}
