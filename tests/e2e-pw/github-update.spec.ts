/**
 *
 * wp transient delete update_plugins --network
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';
import { execSync } from 'child_process';

function runCliCommandInWpEnv( cliCommand: string ): void {
	execSync( 'npx wp-env run cli ' + cliCommand, { stdio: 'pipe' } );
}

const pluginSlug = 'bh-wp-aws-ses-bounce-handler';

test.describe( 'GitHub Integration', () => {
	test( 'Update displays for GitHub release', async ( { admin, page } ) => {

		// Install and activate the plugin via WP CLI
		const installPluginWpCliCommand = `wp plugin install https://github.com/BrianHenryIE/bh-wp-aws-ses-bounce-handler/releases/download/1.6.0/bh-wp-aws-ses-bounce-handler.1.6.0.zip --activate --force`;
		runCliCommandInWpEnv( installPluginWpCliCommand );

		await admin.visitAdminPage( 'plugins.php' );

		// Verify bh-wp-aws-ses-bounce-handler 1.6.0 is installed
		const pluginRow = page.locator('#the-list').locator( `tr[data-slug="aws-ses-bounce-handler"]` );
		await expect( pluginRow ).toBeVisible();
		await expect( pluginRow ).toContainText( '1.6.0' );

		// Clear the transients so the updater will run soon
		const clearUpdateTransientsWpCliCommand = `wp transient delete update_plugins --network`;
		runCliCommandInWpEnv( clearUpdateTransientsWpCliCommand );
		runCliCommandInWpEnv( 'wp plugin list' );

		// Reload the page
		// await admin.visitAdminPage( 'plugins.php' );

		// Verify an update to bh-wp-aws-ses-bounce-handler is available
		const updateRow = page.locator( `tr.plugin-update-tr[data-slug="aws-ses-bounce-handler"]` );
		await expect( updateRow ).toBeVisible();
		await expect( updateRow ).toContainText( 'new version' );
	} );
} );
