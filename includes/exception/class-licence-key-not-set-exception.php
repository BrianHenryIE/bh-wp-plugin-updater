<?php

namespace BrianHenryIE\WP_SLSWC_Client\Exception;

use Throwable;

class Licence_Key_Not_Set_Exception extends SLSWC_Exception {

	const MESSAGE = 'The licence key has not been set.';

	public function __construct( string $message = self::MESSAGE, int $code = 0, ?Throwable $previous = null ) {

		if ( self::MESSAGE === $message ) {
			$message = __( 'The licence key has not been set.', 'bh-wp-slswc-client' );
		}

		parent::__construct( $message, $code, $previous );
	}
}
