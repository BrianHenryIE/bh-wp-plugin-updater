<?php
/**
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update_Interface;

interface API_Interface {

	/**
	 * Product information can be retrieved without a licence key. E.g. to check for updates, even if the licence key is invalid.
	 *
	 * http://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=woocommerce
	 *
	 * @param ?bool|null $refresh True: force refresh from API; false: do not refresh; null: use cached value or refresh if missing.
	 */
	public function get_plugin_information( ?bool $refresh = null ): ?Plugin_Info_Interface;

	public function get_check_update( ?bool $refresh = null ): ?Plugin_Update_Interface;

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
}
