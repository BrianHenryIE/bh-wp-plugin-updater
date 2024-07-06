[![WordPress tested 6.5](https://img.shields.io/badge/WordPress-v6.5%20tested-0073aa.svg)](https://wordpress.org/) [![PHPCS WPCS](./.github/phpcs.svg)](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)  [![PHPStan ](.github/phpstan.svg)](https://github.com/szepeviktor/phpstan-wordpress) [![PHPUnit ](https://brianhenryie.github.io/bh-wp-plugin-updater/phpunit/coverage.svg)](https://brianhenryie.github.io/bh-wp-plugin-updater/phpunit/html) [![WP-CLI ](https://img.shields.io/badge/WP-CLI-3d681d.svg?logo=wordpress)](https://brianhenryie.github.io/bh-wp-plugin-updater/wp-cli) [![OpenAPI ](https://img.shields.io/badge/REST-OpenAPI-85ea2d.svg?logo=swagger)](https://brianhenryie.github.io/bh-wp-plugin-updater/openapi)

# BH WP Plugin Updater

This is a work-in-progress library for updating non-.org WordPress plugins.

If you're interested in using this, I'm happy to jump on a call. 

The general idea is to define the interfaces required by WordPress for updates and plugin update information.

A regular .org plugin uses the endpoints (incomplete):

* http://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=woocommerce
* https://github.com/WordPress/wordpress.org/blob/trunk/wordpress.org/public_html/wp-content/plugins/plugin-directory/api/routes/class-plugin.php
* https://api.wordpress.org/plugins/update-check/1.1/

This is early days and everything is open to input and change.

Currently working on compatability with:

* GitHub releases
* [licenseserver.io](https://licenseserver.io/)

## Installation

```bash
composer config allow-plugins.brianhenryie/composer-fallback-to-git true
composer require --dev brianhenryie/composer-fallback-to-git

composer require brianhenryie/bh-wp-plugin-updater
```

then in the plugin header

```php
<?php
/**
 * Plugin Name:   Example Plugin
 * Update URI:    github.com/brianhenryie/example-plugin
 */
```

or

```php
<?php
/**
 * Plugin Name:   Example Plugin
 * Update URI:    example.org/wp-json/slswc/v1
 */
```

That's it. 

Composer's autoloader will load bootstrap.php, and the plugin updater will be loaded on admin, cron, rest, and wp-cli requests.