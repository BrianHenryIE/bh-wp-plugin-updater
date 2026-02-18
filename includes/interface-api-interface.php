<?php
/**
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;

interface API_Interface {

	/**
	 * Product information can be retrieved without a licence key. E.g. to check for updates, even if the licence key is invalid.
	 *
	 * @see http://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=woocommerce
	 *
	 * @param ?bool $refresh True: force refresh from API; false: do not refresh; null: use cached value or refresh if missing.
	 */
	public function get_plugin_information( ?bool $refresh = null ): ?Plugin_Info;

	public function get_check_update( ?bool $refresh = null ): ?Plugin_Update;

	public function set_license_key( string $license_key ): Licence;

	public function get_licence_details( ?bool $refresh = null ): Licence;

	/**
	 * Deactivate the licence. Does not forget the licence key.
	 */
	public function deactivate_licence(): Licence;

	/**
	 * Attempt to activate an already set licence key.
	 */
	public function activate_licence(): Licence;

	/**
	 * Is there a newer version of the plugin released?
	 *
	 * This does not indicate that the licence is valid.
	 */
	public function is_update_available( ?bool $refresh = null ): bool;

	/**
	 * Trigger a plugin update check in the background.
	 */
	public function schedule_immediate_background_update(): void;
}
