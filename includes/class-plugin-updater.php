<?php
/**
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\WP_Plugin_Updater\Helpers\JsonMapper\JsonMapper_Helper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Plugin_Updater {

	protected static API_Interface $instance;

	public static function get_instance( ?Settings_Interface $settings = null, ?LoggerInterface $logger = null ): API_Interface {

		if ( isset( self::$instance ) ) {
			return self::$instance;
		}

		if ( ! isset( self::$instance ) && is_null( $settings ) ) {
			throw new \Exception( 'Settings must be provided on first call.' );
		}

		$logger ??= new NullLogger();

		if ( ! isset( self::$instance ) ) {
			self::$instance = new API(
				settings: $settings,
				json_mapper: ( new JsonMapper_Helper() )->build(),
				logger: $logger
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
