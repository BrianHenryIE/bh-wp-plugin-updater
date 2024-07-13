import { useState, useEffect } from 'react';
import { Notice, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { getLicenseDetails, setLicenseKey, activateLicense, deactivateLicense } from './api/License';

/**
 * Display status notice.
 *
 * @param {Object} props Component properties.
 * @return {JSX.Element} Notice element.
 */
function StatusNotice( props ) {
    const licenceStatus = props.licenceData.licence_details.status;

    let htmlStatus;
    let message;

    switch( licenceStatus ) {
        case 'invalid':
            htmlStatus = 'error';
            message = __( 'The licence is currently invalid.', 'bh-wp-plugin-updater' );
            break;
        case 'active':
            htmlStatus = 'success';
            message = __( 'The licence is currently active.', 'bh-wp-plugin-updater' );
            break;
        default:
            htmlStatus = 'warning';
            message = __( 'The licence status is unknown.', 'bh-wp-plugin-updater' );
    }

    if( props.licenceData.licence_details.key === undefined ) {
        htmlStatus = 'notice';
        message = __( 'Please enter a licence key.', 'bh-wp-plugin-updater' );
    }

    return (
        <Notice status={htmlStatus} isDismissible={false}>
            {message}
        </Notice>
    );
}

/**
 * Button component for activating or deactivating license.
 *
 * @param {Object} props Component properties.
 * @return {JSX.Element} Button element.
 */
function MyButton( props ) {
    const text = props.hasActiveLicence ? __( 'Deactivate licence', 'bh-wp-plugin-updater' ) : __( 'Activate licence', 'bh-wp-plugin-updater' );
    return <Button onClick={props.update} className="button-primary">{text}</Button>;
}

/**
 * Main App component.
 *
 * @param {Object} props Component properties.
 * @return {JSX.Element} App element.
 */
function App( props ) {
    const [ pluginLicenceData, setPluginLicenceData ] = useState( props.licenceData );
    const [ licenceIsActive, setLicenceIsActive ] = useState( false );
    const [ licenceKey, setLicenceKey ] = useState( pluginLicenceData.licence_details.licence_key ?? '' );

    const enterKeyPrompt = __( 'Please enter a licence key.', 'bh-wp-plugin-updater' );

    useEffect(() => {
        // Fetch initial license details
        getLicenseDetails(props.nonce, true).then( (result) => {
            setPluginLicenceData(result);
            setLicenceIsActive(result.licence_details.status === 'active');
        });
    }, [props.nonce]);

    const deactivateLicence = () => {
        deactivateLicense(props.nonce).then( (result) => {
            setPluginLicenceData(result);
            setLicenceIsActive(false);
        });
    };

    const activateLicence = () => {
        activateLicense(props.nonce).then( (result) => {
            setPluginLicenceData(result);
            setLicenceIsActive(true);
        });
    };

    const changeHandler = ( event ) => {
        setLicenceKey( event.target.value );
        setLicenseKey(props.nonce, event.target.value, false).then( (result) => {
            setPluginLicenceData(result);
        });
    };

    return (
        <div className="licence">
            <StatusNotice licenceData={pluginLicenceData}/>
            <input
                value={licenceKey}
                onChange={changeHandler}
                placeholder={enterKeyPrompt}
                disabled={licenceIsActive}
            />
            <MyButton update={licenceIsActive ? deactivateLicence : activateLicence} hasActiveLicence={licenceIsActive}/>
        </div>
    );
}

export default App;
