[![WordPress tested 6.5](https://img.shields.io/badge/WordPress-v6.5%20tested-0073aa.svg)](https://wordpress.org/) [![PHPCS WPCS](./.github/phpcs.svg)](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)  [![PHPStan ](.github/phpstan.svg)](https://github.com/szepeviktor/phpstan-wordpress) [![PHPUnit ](https://brianhenryie.github.io/bh-wp-plugin-updater/phpunit/coverage.svg)](https://brianhenryie.github.io/bh-wp-plugin-updater/phpunit/html) [![WP-CLI ](https://img.shields.io/badge/WP-CLI-3d681d.svg?logo=wordpress)](https://brianhenryie.github.io/bh-wp-plugin-updater/wp-cli) [![OpenAPI ](https://img.shields.io/badge/REST-OpenAPI-85ea2d.svg?logo=swagger)](https://brianhenryie.github.io/bh-wp-plugin-updater/openapi)

# BH WP Plugin Updater

This is a work-in-progress library for updating non-.org WordPress plugins.

Goals:

* (near) zero-config – require the library and set the update URL
* no blocking requests – all HTTP requests on cron except when the updates transient is deleted
* work as a secondary updater – where a plugin already has an updater, it should be possible to install a second plugin with this for beta updates
* agnostic/extensible to multiple backends 
* confidence: WPCS, PhpStan, PHPUnit, Playwright, WP-CLI, OpenAPI
* learn a little React

Basically, I have a bunch of plugins that I should sell, but I didn't like any licence system I saw. 

If you're interested in using this, I'm happy to jump on a call. 

The general idea is to define the interfaces required by WordPress for updates and plugin update information. Heavily document where each value in the arrays are being used; add strict typing and make it easy for other developers to navigaate the code, maybe even contribute.
 
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

Composer's autoloader will load bootstrap.php, and the plugin updater will be loaded on admin, cron, rest, and wp-cli requests. Obviously, [prefix your namespaces](https://github.com/BrianHenryIE/strauss).

The UI then is seamless with the WordPress plugin UI:

<img width="938" alt="Screenshot 2024-06-10 at 6 56 08 PM" src="https://github.com/BrianHenryIE/bh-wp-plugin-updater/assets/4720401/9ffba71d-1fbf-4155-afa9-f1a45326d25a">

<img width="1096" alt="Screenshot 2024-06-10 at 6 56 45 PM" src="https://github.com/BrianHenryIE/bh-wp-plugin-updater/assets/4720401/ff099bc5-4149-43c9-b449-7e27697ea2b6">

Again, early days, but you get the idea.

I'm still struggling with nomenclature that fits both paid and free updates.