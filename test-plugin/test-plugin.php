<?php
/**
 * Plugin Name:   Test Plugin    Software License Server for WooCommerce
 * Description:   My test plugin description
 * Version:       1.1.1
 * Author:        BrianHenryIE
 * Author URI:    https://bhwp.ie
 * License Server: https://localhost:8889
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

require_once __DIR__ . '/vendor/autoload.php';

if ( file_exists( __DIR__ . '/src/bh-wp-plugin-meta-kit/bootstrap.php' ) ) {
	require_once __DIR__ . '/src/bh-wp-plugin-meta-kit/bootstrap.php';
}

if ( file_exists( require_once __DIR__ . '/src/bootstrap.php' ) ) {
	require_once __DIR__ . '/src/bootstrap.php';
}
