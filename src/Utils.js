/**
 * Utility functions for determining plugin slug and license data variable name.
 * These functions help extract and process the plugin slug from the current script tag,
 * convert the plugin slug to a license data variable name, and retrieve the license data
 * seeded by PHP using `wp_add_inline_script()`.
 */

/**
 * Gets the plugin slug from the current script's <script> tag `id` attribute.
 * Assumes the script handle used is `plugin-slug-licence`.
 *
 * @returns {string} The plugin slug.
 */
export function getPluginSlug() {
	const elements = document.querySelectorAll( 'script' );
	const currentScript = elements[ elements.length - 1 ];
	const match = currentScript.id.match( /(.*).{11}$/ );
	return match ? match[1] : '';
}

/**
 * Converts a plugin slug to a license data variable name.
 * E.g., converts `plugin-slug-licence` to `pluginSlugLicence`.
 *
 * @param {string} pluginSlug - The plugin slug.
 * @returns {string} The converted plugin license data variable name.
 */
export function getPluginLicenceDataVarName( pluginSlug ) {
	return ( pluginSlug + '-licence' )
		.toLowerCase()
		.replace( /([-_][a-z])/g, ( ltr ) => ltr.toUpperCase() )
		.replace( /[^a-zA-Z]/g, '' );
}

/**
 * Retrieves the plugin license data seeded by PHP using `wp_add_inline_script()`.
 *
 * @param {string} pluginLicenceDataVarName - The plugin license data variable name.
 * @returns {any} The plugin license data.
 */
export function getPluginLicenceData( pluginLicenceDataVarName ) {
	return eval( pluginLicenceDataVarName ); // eslint-disable-line no-eval
}
