<?php

namespace BrianHenryIE\WP_SLSWC_Client\Exception;

use Throwable;

class Licence_Does_Not_Exist_Exception extends SLSWC_Exception_Abstract {

	const MESSAGE = 'Licence does not exist. Please check your licence key.';

	public function __construct( string $message = self::MESSAGE, int $code = 0, ?Throwable $previous = null ) {

		if ( self::MESSAGE === $message ) {
			$message = __( 'Licence does not exist. Please check your licence key.', 'bh-wp-slswc-client' );
		}

		parent::__construct( $message, $code, $previous );
	}
}
