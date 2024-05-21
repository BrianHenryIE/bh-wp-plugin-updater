<?php
/**
 * Some helpful functions for the plugin.
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

// PRIVATE – API WILL CHANGE WITHOUT NOTICE.

function str_underscore_to_dash( string $string ): string {
	return str_replace( '_', '-', $string );
}
