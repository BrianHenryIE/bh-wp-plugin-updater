#!/bin/bash

PLUGIN_SLUG=$1;
# Print the script name.
echo "Running " $(basename "$0") " for " $PLUGIN_SLUG;


#rm /var/www/html/wp-content/plugins/test-plugin/vendor/brianhenryie/bh-wp-slswc-client;
#ln -s /var/www/html/wp-content/bh-wp-slswc-client/ /var/www/html/wp-content/plugins/test-plugin/vendor/brianhenryie/bh-wp-slswc-client;


echo "wp plugin activate --all"
wp plugin activate --all


rm /usr/local/bin/wp;
#  sudo rm /usr/local/bin/wp;
alias wp="/var/www/html/wp-content/plugins/test-plugin/vendor/bin/wp";

echo "Set up pretty permalinks for REST API."
wp rewrite structure /%year%/%monthnum%/%postname%/ --hard;