<?php

namespace BrianHenryIE\WP_SLSWC_Client\Exception;

use Throwable;

class Max_Activations_Exception extends SLSWC_Exception {

	// TODO: This should not reference staging.
	const MESSAGE = 'You have reached the maximum number of allowed activations on staging domain for this license.';

	public function __construct( string $message = self::MESSAGE, int $code = 0, ?Throwable $previous = null ) {

		if ( self::MESSAGE === $message ) {
			$message = __( 'You have reached the maximum number of allowed activations on staging domain for this license.', 'bh-wp-slswc-client' );
		}

		parent::__construct( $message, $code, $previous );
	}
}
