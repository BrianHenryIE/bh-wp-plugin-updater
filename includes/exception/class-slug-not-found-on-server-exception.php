<?php

namespace BrianHenryIE\WP_SLSWC_Client\Exception;

use Throwable;

class Slug_Not_Found_On_Server_Exception extends SLSWC_Exception_Abstract {

	const MESSAGE = 'The plugin slug was not found on the server.';

	public function __construct( string $message = self::MESSAGE, int $code = 0, ?Throwable $previous = null ) {

		if ( self::MESSAGE === $message ) {
			// NB: This needs to be exactly the same as ::MESSAGE for the translator hint to work.
			$message = __( 'The plugin slug was not found on the server.', 'bh-wp-slswc-client' );
		}

		parent::__construct( $message, $code, $previous );
	}
}
