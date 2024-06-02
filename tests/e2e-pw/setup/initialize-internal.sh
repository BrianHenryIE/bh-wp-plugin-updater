#!/bin/bash

#PLUGIN_SLUG="bh-wc-checkout-rate-limiter";
PLUGIN_SLUG=$1;
# Print the script name.
echo "Running " $(basename "$0") " for " $PLUGIN_SLUG;


rm /var/www/html/wp-content/plugins/test-plugin/vendor/brianhenryie/bh-wp-slswc-client;
ln -s /var/www/html/wp-content/bh-wp-slswc-client/ /var/www/html/wp-content/plugins/test-plugin/vendor/brianhenryie/bh-wp-slswc-client;

echo "wp plugin activate --all"
wp plugin activate --all




echo "Set up pretty permalinks for REST API."
wp rewrite structure /%year%/%monthnum%/%postname%/ --hard;