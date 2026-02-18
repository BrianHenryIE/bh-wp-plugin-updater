<?php
/**
 * Adds a button in the admin menu bar to delete the `update_plugins` transient.
 */

namespace BrianHenryIE\WP_Plugin_Updater\Development_Plugin\UI;

class WP_Admin_Bar {

	public function __construct() {
	}

	public function register_hooks() {
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_node' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * @hooked admin_bar_menu
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar
	 */
	public function add_admin_bar_node( \WP_Admin_Bar $wp_admin_bar ): void {
		$wp_admin_bar->add_node(
			array(
				'id'    => 'bh-wp-plugin-updater-clear-update-transient',
				'title' => 'Clear Update Cache',
				'href'  => '#',
				'meta'  => array(
					'title' => 'Delete update_plugins network transient',
				),
			)
		);
	}

	/**
	 * @hooked admin_enqueue_scripts
	 */
	public function enqueue_scripts(): void {
		$nonce    = wp_create_nonce( 'wp_rest' );
		$rest_url = rest_url( 'bh-wp-plugin-updater/v1/transients/network/update_plugins' );

		$js = <<<JS
(function() {
	document.addEventListener('DOMContentLoaded', function() {
		var node = document.getElementById('wp-admin-bar-bh-wp-plugin-updater-clear-update-transient');
		if (!node) { return; }

		var link = node.querySelector('a');
		if (!link) { return; }

		link.addEventListener('click', function(e) {
			e.preventDefault();

			fetch(
				'{$rest_url}',
				{
					method: 'DELETE',
					headers: {
						'X-WP-Nonce': '{$nonce}',
						'Content-Type': 'application/json',
					},
				}
			).then(function(response) {
				return response.json();
			}).then(function(data) {
				var indicator = document.createElement('span');
				indicator.textContent = ' ✓';
				indicator.style.cssText = 'color:#46b450;font-weight:bold;transition:opacity 1s ease;';
				link.appendChild(indicator);
				setTimeout(function() {
					indicator.style.opacity = '0';
					setTimeout(function() {
						if (indicator.parentNode) {
							indicator.parentNode.removeChild(indicator);
						}
					}, 1000);
				}, 2000);
			}).catch(function(err) {
				console.error('Failed to clear update_plugins transient', err);
			});
		});
	});
})();
JS;

		wp_add_inline_script( 'wp-api-fetch', $js );
	}
}
