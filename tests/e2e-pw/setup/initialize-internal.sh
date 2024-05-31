#!/bin/bash


rm /var/www/html/wp-content/plugins/test-plugin/vendor/brianhenryie/bh-wp-slswc-client;
ln -s /var/www/html/wp-content/plugins/bh-wp-slswc-client/ /var/www/html/wp-content/plugins/test-plugin/vendor/bri
anhenryie/bh-wp-slswc-client;



# Print the script name.
echo $(basename "$0")

echo "wp plugin activate --all"
wp plugin activate --all




echo "Set up pretty permalinks for REST API."
wp rewrite structure /%year%/%monthnum%/%postname%/ --hard;