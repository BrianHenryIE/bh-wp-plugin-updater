<?php
/**
 * @package brianhenryie/bh-wp-plugin-updater
 */

use Alley_Interactive\Autoloader\Autoloader;

$GLOBALS['project_root_dir']   = $project_root_dir  = dirname( __DIR__, 1 );
$GLOBALS['plugin_root_dir']    = $plugin_root_dir   = $project_root_dir;
$GLOBALS['plugin_slug']        = $plugin_slug       = basename( $project_root_dir );
$GLOBALS['plugin_file_php']    = $plugin_file_php   = $plugin_slug . '.php';
$GLOBALS['plugin_path_php']    = $plugin_root_dir . '/' . $plugin_file_php;
$GLOBALS['plugin_basename']    = $plugin_slug . '/' . $plugin_file_php;
$GLOBALS['wordpress_root_dir'] = $project_root_dir . '/wordpress';

Autoloader::generate(
	'BrianHenryIE\\WP_Plugin_Updater',
	__DIR__ . '/unit',
)->register();

Autoloader::generate(
	'BrianHenryIE\\WP_Plugin_Updater',
	__DIR__ . '/wpunit',
)->register();

// If there is a secrets file, load it here.
$env_secret_fullpath = codecept_root_dir( '.env.secret' );
if ( file_exists( $env_secret_fullpath ) ) {
	$dotenv = Dotenv\Dotenv::createImmutable( codecept_root_dir(), '.env.secret' );
	$dotenv->load();
}

/**
 * Fix "sh: php: command not found" when running wpunit tests in PhpStorm.
 *
 * @see lucatume\WPBrowser\Module\WPLoader::includeCorePHPUniteSuiteBootstrapFile()
 */
$is_phpstorm = array_reduce( $GLOBALS['argv'], fn( bool $carry, string $arg ) => $carry || str_contains( $arg, 'PhpStorm' ), false );
if ( $is_phpstorm ) {
	define( 'WP_PHP_BINARY', PHP_BINARY );
}
