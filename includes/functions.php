<?php
/**
 * Some helpful functions for the plugin.
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

// PRIVATE – API WILL CHANGE WITHOUT NOTICE.

if ( ! function_exists( '\BrianHenryIE\WP_SLSWC_Client\str_underscore_to_dash' ) ) {
	function str_underscore_to_dash( string $string ): string {
		return str_replace( '_', '-', $string );
	}
}

if ( ! function_exists( '\BrianHenryIE\WP_SLSWC_Client\str_dash_to_underscore' ) ) {
	function str_dash_to_underscore( string $string ): string {
		return str_replace( '-', '_', $string );
	}
}
