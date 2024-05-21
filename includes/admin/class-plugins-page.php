<?php
/**
 * Add info on plugins.php, add link to display licence modal
 *
 * Add the "Licence: invalid" text in the Automatic Updates column on `plugins.php`.
 * TODO: display banner under plugin entry if licence is not active.
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Admin;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Settings;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;

class Plugins_Page {

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api
	 * @param Settings_Interface $settings
	 */
	public function __construct(
		protected API_Interface $api,
		protected Settings_Interface $settings,
	) {
	}

	/**
	 * Add the "Licence: invalid" text in the Automatic Updates column on `plugins.php`.
	 *
	 * TODO: remove "Enable auto-updates" when the licence is invalid.
	 *
	 * @hooked plugin_auto_update_setting_html
	 * @see \WP_Plugins_List_Table::single_row()
	 *
	 * @param string $html The existing HTML for the plugins.php Automatic Updates column.
	 * @param string $file Plugin or theme file.
	 */
	public function plugin_auto_update_setting_html( $html, $file ): string {

		if ( $this->settings->get_plugin_basename() !== $file ) {
			return $html;
		}

		$licence_link_url = admin_url(
			add_query_arg(
				array(
					'plugin'    => $this->settings->get_plugin_slug(),
					'tab'       => 'plugin-information',
					'section'   => 'licence',
					'TB_iframe' => 'true',
					'width'     => '772',
					'height'    => '730',
				),
				'plugin-install.php'
			)
		);

		$licence_status = $this->api->get_licence_details( false )->get_status();
		$expires        = $this->api->get_licence_details( false )->get_expires();

		if ( in_array( $licence_status, array( 'valid', 'expired' ), true )
				&& ! is_null( $expires ) ) {
			$licence_link_text = 'Licence active until ' . $expires->format( 'Y-m-d' );
		} else {
			$licence_link_text = "Licence: {$licence_status}";
		}

		$licence_link = sprintf(
			'<a href="%s" class="thickbox open-plugin-details-modal" style="%s">%s</a>',
			$licence_link_url,
			'display: inline-block;',
			esc_html( $licence_link_text )
		);

		// Remove the 'Enable auto-updates' link when the licence is invalid.
		if( 'invalid' === $licence_status ) {
			 $html = '';
		}

		return empty( trim( wp_strip_all_tags( $html ) ) )
			? $licence_link
			: "{$html} | {$licence_link}";
	}

	// TODO: display banner under plugin entry if licence is not active.
}
