
// if ( empty( $this->settings->get_license_server_url() ) ) {
// <div class="error notice"><p>Plugin update URL missing. Please reinstall plugin.</p></div>
// }
// * status banner
// * banner if we are on a staging site
// * licence key field
// * toggle:
//      * activate button
//      * deactivate licence button
// * licence expiry date
// * link to licence server my-account
// * link to create renewal order

// Error: licence server unknown.
// Error: licence server unreachable.
// Error: licence server unreachable since 2021-01-01.
// Licence is currently active, with a renewal date of 2021-12-31.
// Licence is active until 2021-12-31, with no automatic renewal.
// Licence is currently inactive, activate licence.
// Licence is currently inactive, with zero activations remaining.
// Licence key is invalid. Click here to purchase.
// Please enter a licence key.

// Error: own server unreachable


import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import { getPluginSlug, getPluginLicenceDataVarName, getPluginLicenceData } from '../Utils';


const pluginData = getPluginLicenceData( getPluginLicenceDataVarName( getPluginSlug() ) );

const apiBaseUrl = pluginData.restUrl;
console.log({pluginData});

/**
 * Fetch license details from the server.
 *
 * @param {string} nonce Nonce for the request.
 * @param {boolean} refresh Whether to refresh the license details.
 * @return {Promise} API response.
 */
export function getLicenseDetails( nonce, refresh = false ) {
    const queryParams = {
        _ajax_nonce: nonce,
        refresh: refresh,
    };

    return apiFetch( {
        url: addQueryArgs( `${apiBaseUrl}licence`, queryParams ),
        method: 'GET',
    } );
}

/**
 * Set license key.
 *
 * @param {string} nonce Nonce for the request.
 * @param {string} licenseKey License key.
 * @param {boolean} activate Whether to activate the license.
 * @return {Promise} API response.
 */
export function setLicenseKey( nonce, licenseKey, activate = false ) {
    const queryParams = {
        _ajax_nonce: nonce,
        licence_key: licenseKey,
        activate: activate,
    };

    return apiFetch( {
        url: addQueryArgs( `${apiBaseUrl}licence/set-key`, queryParams ),
        method: 'POST',
    } );
}

/**
 * Activate license.
 *
 * @param {string} nonce Nonce for the request.
 * @return {Promise} API response.
 */
export function activateLicense( nonce ) {
    const queryParams = {
        _ajax_nonce: nonce,
    };

    return apiFetch( {
        url: addQueryArgs( `${apiBaseUrl}licence/activate`, queryParams ),
        method: 'POST',
    } );
}

/**
 * Deactivate license.
 *
 * @param {string} nonce Nonce for the request.
 * @return {Promise} API response.
 */
export function deactivateLicense( nonce ) {
    const queryParams = {
        _ajax_nonce: nonce,
    };

    return apiFetch( {
        url: addQueryArgs( `${apiBaseUrl}licence/deactivate`, queryParams ),
        method: 'POST',
    } );
}
