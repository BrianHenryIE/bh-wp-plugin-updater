<?php

namespace BrianHenryIE\WP_SLSWC_Client\Exception;

abstract class SLSWC_Exception extends \Exception {

	/**
	 * SLSWC_Exception constructor.
	 *
	 * @param string          $message
	 * @param int             $code
	 * @param \Throwable|null $previous
	 */
	public function __construct( $message = '', $code = 0, \Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}

	abstract function get_http_status_code(): int;
}
