<?php
/**
 * Interface for a factory to create Integration objects.
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Integrations;

use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;

interface Integration_Factory_Interface {

	/**
	 * Get an integration that provides licence management and updates.
	 *
	 * @param Settings_Interface $integration_settings The plugin's licence server settings.
	 */
	public function get_integration( Settings_Interface $settings ): Integration_Interface;
}
