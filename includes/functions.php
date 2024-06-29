<?php
/**
 * Some helpful functions for the plugin.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

// PRIVATE – API WILL CHANGE WITHOUT NOTICE.

if ( ! function_exists( '\BrianHenryIE\WP_Plugin_Updater\str_underscore_to_dash' ) ) {
	/**
	 * Convert a string from snake case to kebab case.
	 *
	 * @param string $string
	 */
	function str_underscore_to_dash( string $string ): string {
		return str_replace( '_', '-', $string );
	}
}

if ( ! function_exists( '\BrianHenryIE\WP_Plugin_Updater\str_dash_to_underscore' ) ) {
	/**
	 * Convert a string from kebab case to snake case.
	 *
	 * @param string $string
	 */
	function str_dash_to_underscore( string $string ): string {
		return str_replace( '-', '_', $string );
	}
}

if ( ! function_exists( '\BrianHenryIE\WP_Plugin_Updater\str_dash_to_next_capitalised_first_lower' ) ) {
	/**
	 * Convert a string from kebab case to camel case.
	 *
	 * E.g. `bh-wc-zelle-gateway-licence` -> `bhWcZelleGatewayLicence`.
	 *
	 * @param string $string
	 */
	function str_dash_to_next_capitalised_first_lower( string $string ): string {
		return lcfirst( str_replace( ' ', '', ucwords( str_replace( '-', ' ', $string ) ) ) );
	}
}
