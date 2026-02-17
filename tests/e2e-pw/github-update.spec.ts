/**
 * wp plugin install https://github.com/BrianHenryIE/bh-wp-autologin-urls/releases/download/v2.4.2/bh-wp-autologin-urls.2.4.2.zip
 * wp plugin install https://github.com/BrianHenryIE/bh-wp-aws-ses-bounce-handler/releases/download/1.6.0/bh-wp-aws-ses-bounce-handler.1.6.0.zip
 * wp transient delete update_plugins --network
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Plugin activation', () => {
	test( 'example plugin is listed on the plugins page', async ( { admin, page } ) => {
		await admin.visitAdminPage( 'plugins.php' );

		const pluginRow = page.locator( 'tr[data-slug="development-plugin"]' );
		await expect( pluginRow ).toBeVisible();

		const deactivateLink = pluginRow.locator( 'a:has-text("Deactivate")' );
		await expect( deactivateLink ).toBeVisible();
	} );
} );
