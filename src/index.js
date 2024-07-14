import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { getPluginSlug, getPluginLicenceDataVarName, getPluginLicenceData } from './Utils';

/**
 * Main entry point for the React application.
 * Determines the plugin slug and license data variable name, retrieves the plugin license data,
 * and renders the React application.
 */

// Determine the plugin slug from the current script's <script> tag `id` attribute.
const pluginSlug = getPluginSlug();

// Convert `plugin-slug-licence` to `pluginSlugLicence`.
const pluginLicenceDataVarName = getPluginLicenceDataVarName( pluginSlug );

// The data seeded in PHP using `wp_add_inline_script()`.
// ajaxUrl, nonce, licence_information.
const pluginLicenceData = getPluginLicenceData( pluginLicenceDataVarName );

const root = ReactDOM.createRoot( document.getElementById( 'section-licence' ) );
root.render(
	<React.StrictMode>
		<App licenceData={ pluginLicenceData } />
	</React.StrictMode>
);
