<?php
/**
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use BrianHenryIE\WP_SLSWC_Client\Server\Product;

interface API_Interface {

	public function get_licence_details( ?bool $refresh = null ): Licence;

	/**
	 * @param ?bool|null $refresh True: force refresh from API; false: do not refresh; null: use cached value or refresh if missing.
	 *
	 * @return ?object
	 * (object) array(
	 * 'software' => 1,
	 * 'software_type' => 'plugin',
	 * 'allow_staging' => 'yes',
	 * 'license_type' => 'annual',
	 * 'software_slug' => 'bh-wc-zelle-gateway',
	 * 'version' => '1.1.0',
	 * 'author' => 'BH',
	 * 'required_wp' => '6.0',
	 * 'compatible_to' => '6.4',
	 * 'updated' => '2023-11-15',
	 * 'activations' => '0',
	 * 'staging_activations' => '0',
	 * 'description' => 'bh-wc-zelle-gateway',
	 * 'change_log' => '1.1.0 zelle',
	 * 'installation' => '',
	 * 'documentation_link' => 'http://bhwp.ie',
	 * 'banner_low' => '',
	 * 'banner_high' => '',
	 * 'update_file_id' => 'f5a6b699-5628-487d-84dc-d87dcfc65552',
	 * 'update_file_url' => 'http://localhost:8080/bh-wp-autologin-urls/wp-content/uploads/woocommerce_uploads/2023/11/bh-wc-zelle-gateway.1.1.0-luxyoq.zip',
	 * 'update_file_name' => 'bh-wc-zelle-gateway.1.1.0-luxyoq.zip',
	 * 'update_file' =>
	 * (object) array(
	 * 'id' => 'f5a6b699-5628-487d-84dc-d87dcfc65552',
	 * 'file' => 'http://localhost:8080/bh-wp-autologin-urls/wp-content/uploads/woocommerce_uploads/2023/11/bh-wc-zelle-gateway.1.1.0-luxyoq.zip',
	 * 'name' => 'bh-wc-zelle-gateway.1.1.0-luxyoq.zip',
	 * ),
	 * 'thumbnail' => false,
	 * ),
	 */
	public function get_product_information( ?bool $refresh = null ): ?Product;

	public function deactivate_licence();

	public function activate_licence( string $licence_key ): Licence;

	public function is_update_available( ?bool $refresh = null ): bool;
}
