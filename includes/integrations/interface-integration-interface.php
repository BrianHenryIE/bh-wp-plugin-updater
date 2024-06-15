<?php

namespace BrianHenryIE\WP_SLSWC_Client\Integrations;

use BrianHenryIE\WP_SLSWC_Client\Licence;
use BrianHenryIE\WP_SLSWC_Client\Model\Plugin_Update;
use BrianHenryIE\WP_SLSWC_Client\Integrations\SLSWC\Model\Product;

interface Integration_Interface {

	public function activate_licence( Licence $licence );

	public function deactivate_licence( Licence $licence );

	public function refresh_licence_details( Licence $licence ): Licence;

	public function get_remote_check_update( Licence $licence ): ?Plugin_Update;

	public function get_remote_product_information( Licence $licence ): ?Product;
}
