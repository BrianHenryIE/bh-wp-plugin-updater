[![WordPress tested 6.5](https://img.shields.io/badge/WordPress-v6.5%20tested-0073aa.svg)](https://wordpress.org/) [![PHPCS WPCS](https://img.shields.io/badge/PHPCS-WordPress%20Coding%20Standards-8892BF.svg)](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)  [![PHPStan ](.github/phpstan.svg)](https://github.com/szepeviktor/phpstan-wordpress) [![PHPUnit ](https://brianhenryie.github.io/bh-wp-plugin-updater/phpunit/coverage.svg)](https://brianhenryie.github.io/bh-wp-plugin-updater/phpunit/html)

# BH WP Plugin Updater

This is a work-in-progress library for updating non-.org WordPress plugins.

The general idea is to define the interfaces required by WordPress for updates and plugin update information.

A regular .org plugin uses the endpoints (incomplete):

* http://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=woocommerce
* https://github.com/WordPress/wordpress.org/blob/trunk/wordpress.org/public_html/wp-content/plugins/plugin-directory/api/routes/class-plugin.php
* https://api.wordpress.org/plugins/update-check/1.1/

This is early days and everything is open to input and change.