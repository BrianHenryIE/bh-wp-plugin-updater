<?php

namespace BrianHenryIE\WP_Plugin_Updater\Exception;

use Throwable;

class Max_Activations_Exception extends Plugin_Updater_Exception_Abstract {

	// TODO: This should not reference staging.
	const MESSAGE = 'You have reached the maximum number of allowed activations on staging domain for this license.';

	public function __construct( string $message = self::MESSAGE, int $code = 0, ?Throwable $previous = null ) {

		if ( self::MESSAGE === $message ) {
			$message = __( 'You have reached the maximum number of allowed activations on staging domain for this license.', 'bh-wp-plugin-updater' );
		}

		parent::__construct( $message, $code, $previous );
	}
}
