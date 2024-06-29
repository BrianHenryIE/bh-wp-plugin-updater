


`chmod +x tests/e2e-pw/setup/initialize-external.sh`
`chmod +x tests/e2e-pw/setup/initialize-internal.sh`
`chmod +x tests/e2e-pw/setup/initialize-internal-tests.sh`
For the test plugin:
`composer install --no-dev`

`PHP_IDE_CONFIG="serverName=localhost" wp test-plugin logger delete-all; PHP_IDE_CONFIG="serverName=localhost" wp test-plugin licence activate`

```
wp transient delete update_plugins --network; XDEBUG_CONFIG="idekey=WP_CLI_XDEBUG remote_connect_back=1 log_level=0" XDEBUG_MODE=debug 
 PHP_IDE_CONFIG=serverName=localhost wp plugin list
```

```
cd test-plugin;
rm -rf vendor;
composer update --no-dev;
cd ..;
npx wp-env start;
```
```
echo "y" | npx wp-env destroy && npx wp-env start;
```


`wp test-plugin logger delete-all; wp test-plugin licence activate`

`composer show --self`
`composer show --direct`
`composer show --outdated`
`composer show --tree`

`curl -s http://localhost:8888/wp-json/ | jq '.namespaces | sort'`

### OpenAPI

`wp openapi-generator export-file test-plugin/v1 --destination=./openapi/test-plugin-openapi.json`
TODO: maybe open an issue at https://github.com/schneiderundschuetz/document-generator-for-openapi
jsonSchemaDialect: https://spec.openapis.org/oas/3.1/dialect/base
`cat test-plugin-openapi.json | jq '.jsonSchemaDialect="https://spec.openapis.org/oas/3.1/dialect/base"' | sponge test-plugin-openapi.json`


(Maybe) Delete `.servers` since the URL changes for every WordPress install:
`cat test-plugin-openapi.json | jq 'del(.servers)' | sponge test-plugin-openapi.json`

Remove the root `/` path since we are only concerned with the endpoints we have defined:
`cat test-plugin-openapi.json | jq 'del(.paths."/")' | sponge test-plugin-openapi.json`

Regenerate:
```
wp-env run cli wp openapi-generator export-file test-plugin/v1 --destination=./openapi/test-plugin-openapi.json --extract-common-types;
cat ./openapi/test-plugin-openapi.json | jq 'del(.servers) | del(.paths."/") | .jsonSchemaDialect = "https://spec.openapis.org/oas/3.1/dialect/base"' | sponge ./openapi/test-plugin-openapi.json
npm --prefix ./openapi install
```


## Contributing

Clone this repo, open PhpStorm, then run `composer install` to install the dependencies.

```
git clone https://github.com/brianhenryie/bh-wp-plugin-updater.git;
open -a PhpStorm ./;
composer install;
```

For integration and acceptance tests, a local webserver must be running with `localhost:8080/bh-wp-plugin-updater/` pointing at the root of the repo. MySQL must also be running locally – with two databases set up with:

```
mysql_username="root"
mysql_password="secret"

# export PATH=${PATH}:/usr/local/mysql/bin

# Make .env available 
# Bash:
export $(grep -v '^#' .env.testing | xargs)
# Zsh:
source .env.testing

# Create the database user:
# MySQL
mysql -u $mysql_username -p$mysql_password -e "CREATE USER '"$TEST_DB_USER"'@'%' IDENTIFIED WITH mysql_native_password BY '"$TEST_DB_PASSWORD"';";
# MariaDB
mysql -u $mysql_username -p$mysql_password -e "CREATE USER '"$TEST_DB_USER"'@'%' IDENTIFIED BY '"$TEST_DB_PASSWORD"';";

# Create the databases:
mysql -u $mysql_username -p$mysql_password -e "CREATE DATABASE "$TEST_SITE_DB_NAME"; USE "$TEST_SITE_DB_NAME"; GRANT ALL PRIVILEGES ON "$TEST_SITE_DB_NAME".* TO '"$TEST_DB_USER"'@'%';";
mysql -u $mysql_username -p$mysql_password -e "CREATE DATABASE "$TEST_DB_NAME"; USE "$TEST_DB_NAME"; GRANT ALL PRIVILEGES ON "$TEST_DB_NAME".* TO '"$TEST_DB_USER"'@'%';";

# Import the WordPress database:
mysql -u $mysql_username -p$mysql_password $TEST_SITE_DB_NAME < tests/_data/dump.sql
```

### WordPress Coding Standards

See documentation on [WordPress.org](https://make.wordpress.org/core/handbook/best-practices/coding-standards/) and [GitHub.com](https://github.com/WordPress/WordPress-Coding-Standards).

Correct errors where possible and list the remaining with:

```
vendor/bin/phpcbf; vendor/bin/phpcs
```

### Tests

Tests use the [Codeception](https://codeception.com/) add-on [WP-Browser](https://github.com/lucatume/wp-browser) and include vanilla PHPUnit tests with [WP_Mock](https://github.com/10up/wp_mock). 

Run tests with:

```
vendor/bin/codecept run unit;
vendor/bin/codecept run wpunit;
vendor/bin/codecept run integration;
vendor/bin/codecept run acceptance;
```

Show code coverage (unit+wpunit):

```
XDEBUG_MODE=coverage composer run-script coverage-tests 
```

```
vendor/bin/phpstan analyse --memory-limit 1G
```

To save changes made to the acceptance database:

```
export $(grep -v '^#' .env.testing | xargs)
mysqldump -u $TEST_SITE_DB_USER -p$TEST_SITE_DB_PASSWORD $TEST_SITE_DB_NAME > tests/_data/dump.sql
```

To clear Codeception cache after moving/removing test files:

```
vendor/bin/codecept clean
```

### More Information

See [github.com/BrianHenryIE/WordPress-Plugin-Boilerplate](https://github.com/BrianHenryIE/WordPress-Plugin-Boilerplate) for initial setup rationale. 

# Acknowledgements