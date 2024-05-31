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



if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	echo 'uhoh' . '<br/>' . PHP_EOL;

	$r = exec( '/usr/local/bin/composer install', $output, $result_code );

	echo 'Composer install result code: ' . $result_code . '<br/>' . PHP_EOL;
	echo 'Composer install output: ' . '<br/>' . PHP_EOL;
	echo implode( '<br/>' . PHP_EOL, $output ) . '<br/>' . PHP_EOL;

	echo 'Composer install done' . '<br/>' . PHP_EOL;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	echo './vendor/autoload.php not found' . '<br/>' . PHP_EOL;
}


// if ( file_exists( __DIR__ . '/src/bh-wp-plugin-meta-kit/bootstrap.php' ) ) {
// require_once __DIR__ . '/src/bh-wp-plugin-meta-kit/bootstrap.php';
// }
//
// if ( file_exists( require_once __DIR__ . '/src/bootstrap.php' ) ) {
// require_once __DIR__ . '/src/bootstrap.php';
// }
