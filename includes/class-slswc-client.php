<?php
/**
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SLSWC_Client {

	protected static API_Interface $instance;

	public static function get_instance( ?Settings_Interface $settings = null, ?LoggerInterface $logger = null ): API_Interface {

		if ( ! isset( self::$instance ) && is_null( $settings ) ) {
			throw new \Exception( 'Settings must be provided on first call.' );
		}

		$logger = $logger ?? new NullLogger();

		if ( ! isset( self::$instance ) ) {
			self::$instance = new API(
				$settings,
				$logger
			);
			new Actions(
				self::$instance,
				$settings,
				$logger
			);
		}

		return self::$instance;
	}
}
