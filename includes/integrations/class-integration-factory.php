<?php
/**
 * Creates Integration objects based on the settings.
 *
 * E.g. if the settings indicate the plugin uses SLSWC, this will return an SLSWC object.
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Integrations;

use BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\SLSWC;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Creates objects of type {@see Integration_Interface}, e.g. {@see SLSWC}.
 */
class Integration_Factory implements Integration_Factory_Interface {
	use LoggerAwareTrait;

	/**
	 * Constructor
	 *
	 * @param LoggerInterface $logger A PSR logger.
	 */
	public function __construct(
		LoggerInterface $logger
	) {
		$this->setLogger( $logger );
	}

	/**
	 * Determine which integration to use based on the settings.
	 *
	 * @param Settings_Interface $settings The plugin's licence server settings.
	 */
	public function get_integration( Settings_Interface $settings ): Integration_Interface {
		return new SLSWC( $settings, $this->logger );
	}
}
