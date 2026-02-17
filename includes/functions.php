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
	 * @param string $snake_string The string to modify.
	 */
	function str_underscore_to_dash( string $snake_string ): string {
		return str_replace( '_', '-', $snake_string );
	}
}

if ( ! function_exists( '\BrianHenryIE\WP_Plugin_Updater\str_dash_to_underscore' ) ) {
	/**
	 * Convert a string from kebab case to snake case.
	 *
	 * @param string $kebab_string The string to modify.
	 */
	function str_dash_to_underscore( string $kebab_string ): string {
		return str_replace( '-', '_', $kebab_string );
	}
}

if ( ! function_exists( '\BrianHenryIE\WP_Plugin_Updater\str_dash_to_next_capitalised_first_lower' ) ) {
	/**
	 * Convert a string from kebab case to camel case.
	 *
	 * E.g. `bh-wc-zelle-gateway-licence` -> `bhWcZelleGatewayLicence`.
	 *
	 * @param string $kebab_string The string to modify.
	 */
	function str_dash_to_next_capitalised_first_lower( string $kebab_string ): string {
		return lcfirst( str_replace( ' ', '', ucwords( str_replace( '-', ' ', $kebab_string ) ) ) );
	}
}

if ( ! function_exists( '\BrianHenryIE\WP_Plugin_Updater\bh_wp_is_rest_request' ) ) {
	/**
	 * Determine is the current request a WordPress REST API request.
	 *
	 * Nonce is disabled here because we are just reading a URL to determine where we are, we are not modifying any data.
	 *
	 * phpcs:disable WordPress.Security.NonceVerification.Recommended
	 */
	function bh_wp_is_rest_request(): bool {
		return isset( $_GET['rest_route'] )
			||
			(
				isset( $_SERVER['REQUEST_URI'] )
				&& is_string( $_SERVER['REQUEST_URI'] )
				&& str_contains( sanitize_url( wp_unslash( ( $_SERVER['REQUEST_URI'] ) ) ), '/wp-json/' )
			);
	}
}
