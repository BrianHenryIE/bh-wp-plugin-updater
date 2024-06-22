<?php
/**
 * Add info on plugins.php, add link to display licence modal
 *
 * Add the "Licence: invalid" text in the Automatic Updates column on `plugins.php`.
 * Add link to licence modal when newer plugin exists but update cannot be downloaded.
 *
 * @package brianhenryie/bh-wp-swlsc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client\Admin;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;

/**
 * Add manage licence links to the plugins list table.
 *
 * @see \WP_Plugins_List_Table
 */
class Plugins_Page {

	/**
	 * Constructor.
	 *
	 * @param API_Interface      $api Used to get the licence details.
	 * @param Settings_Interface $settings Used for the plugin basename and slug.
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
	public function plugin_auto_update_setting_html( string $html, string $file ): string {

		if ( $this->settings->get_plugin_basename() !== $file ) {
			return $html;
		}

		$licence_status = $this->api->get_licence_details( false )->get_status();
		$expires        = $this->api->get_licence_details( false )->get_expiry_date();

		if ( in_array( $licence_status, array( 'valid', 'expired' ), true )
				&& ! is_null( $expires ) ) {
			$licence_link_text = 'Licence active until ' . $expires->format( 'Y-m-d' );
		} else {
			$licence_link_text = "Licence: {$licence_status}";
		}

		$licence_link = sprintf(
			'<a href="%s" class="thickbox open-plugin-details-modal" style="%s">%s</a>',
			$this->get_licence_link_url(),
			'display: inline-block;',
			esc_html( $licence_link_text )
		);

		// Remove the 'Enable auto-updates' link when the licence is invalid.
		if ( 'invalid' === $licence_status ) {
			$html = '';
		}

		return empty( trim( wp_strip_all_tags( $html ) ) )
			? $licence_link
			: "{$html} | {$licence_link}";
	}

	/**
	 * Get the URL which works to display the plugin View Details modal at the licence tab.
	 *
	 * @see \WP_Plugins_List_Table::get_view_details_link()
	 */
	protected function get_licence_link_url(): string {
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

		return $licence_link_url;
	}

	/**
	 * Add link to open the plugin details modal to the licence tab after the text: "Automatic update is unavailable
	 * for this plugin."
	 *
	 * @see wp_plugin_update_row()
	 * @hooked in_plugin_update_message-{$plugin_basename}
	 *
	 * @param array     $plugin_data
	 * @param \stdClass $response An object of metadata about the available plugin update.
	 */
	public function append_licence_link_to_auto_update_unavailable_text( $plugin_data, $response ): void {

		// Only print this text when there is no download link.
		if ( ! empty( $response->package ) ) {
			return;
		}

		// E.g. your licence has expired.
		$licence_link_text = 'View licence details';

		$licence_link = sprintf(
			' <a href="%s" class="thickbox open-plugin-details-modal">%s</a>',
			$this->get_licence_link_url(),
			esc_html( $licence_link_text )
		);

		echo wp_kses(
			$licence_link,
			array(
				'a' => array(
					'href'  => array(),
					'class' => array(),
				),
			)
		);
	}
}
