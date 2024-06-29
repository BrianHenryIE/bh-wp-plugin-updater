<?php

namespace BrianHenryIE\WP_Plugin_Updater\Exception;

use Throwable;

class Slug_Not_Found_On_Server_Exception extends Plugin_Updater_Exception_Abstract {

	const MESSAGE = 'The plugin slug was not found on the server.';

	public function __construct( string $message = self::MESSAGE, int $code = 0, ?Throwable $previous = null ) {

		if ( self::MESSAGE === $message ) {
			// NB: This needs to be exactly the same as ::MESSAGE for the translator hint to work.
			$message = __( 'The plugin slug was not found on the server.', 'bh-wp-plugin-updater' );
		}

		parent::__construct( $message, $code, $previous );
	}
}
