<?php

namespace BrianHenryIE\WP_Plugin_Updater\Integrations;

use BrianHenryIE\WP_Plugin_Updater\Licence;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update_Interface;

interface Integration_Interface {

	public function activate_licence( Licence $licence );

	public function deactivate_licence( Licence $licence );

	public function refresh_licence_details( Licence $licence ): Licence;

	public function get_remote_check_update( Licence $licence ): ?Plugin_Update_Interface;

	public function get_remote_product_information( Licence $licence ): ?Plugin_Info_Interface;
}
