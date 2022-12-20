# WordPress Plugin Boilerplate – BrianHenryIE Fork

The popular [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/) with added Composer, namespaces, autoloading, Codeception testing, and GitHub Actions WordPress.org deployment.

## Overview

The WordPress Plugin Boilerplate is a well-documented starting point for WordPress plugins which encourages consistent conventions in plugin development. This fork adds:

* PHP namespacing
* PHP autoloading
* Acceptance, integration & unit tests
* WordPress Coding Standards (PHPCBF/PHPCS)
* Composer namespace prefixing
* Local copy of WordPress in development environment
* Symlinks to make a sensible directory layout
* Configuration of PhpStorm
* Base README.md with Contributing section for new repos
* GitHub Actions for WordPress.org deployment

The documentation is typically a little behind the code, but what's going on should be understandable to most developers.

The ultimate dream would be to have all WordPress plugins understandable by convention, unit tested, and in a repo thay can be contributed to with CI/CD to WordPress.org.

### Environment

This was written for a local environment with:

* MacOS Catalina (Bash)
* Built-in Apache serving to localhost:8080 from your projects directory (probably ~/Sites/)
* PHP 7.4 [setup guide](https://getgrav.org/blog/macos-catalina-apache-multiple-php-versions)
* Xdebug
* MySQL 8
* NPM

There are some differences between `sed` on MacOS and Linux. See [PR6](https://github.com/BrianHenryIE/WordPress-Plugin-Boilerplate/pull/6/files). 


## Setup a New Plugin

Open Terminal and set the variables, your local MySQL credentials, and your WordPress.org credentials for deployment:

```
your_name="BrianHenryIE"
your_email="BrianHenryIE@gmail.com"

plugin_name="Example Plugin"
plugin_package_name="BrianHenryIE\\\\\\Plugin_Name"

mysql_username="root"
mysql_password="secret"

WP_ORG_SVN_USERNAME="email@example.org"
WP_ORG_SVN_PASSWORD="wordpressorgpassword"
```

Run these commands to generate replacements (or define them yourself):

```
plugin_slug=$(echo $plugin_name | tr '[:upper:]' '[:lower:]' | sed 's/ /-/g'); echo $plugin_slug; # example-plugin
plugin_snake=$(echo $plugin_name | sed 's/ /_/g'); echo $plugin_snake; # Example_Plugin
plugin_snake_lower=$(echo $plugin_snake | tr '[:upper:]' '[:lower:]'); echo $plugin_snake_lower; # example_plugin
plugin_capitalized=$(echo $plugin_name | tr '[:lower:]' '[:upper:]' | sed 's/ /_/g'); echo $plugin_capitalized; # EXAMPLE_PLUGIN
php_package_name=$(echo "${plugin_package_name%%\\*}"/$plugin_slug | tr '[:upper:]' '[:lower:]'); echo $php_package_name # brianhenryie/example-plugin
test_site_db_name=${plugin_snake_lower:0:57}"_tests"; # example_plugin_tests
test_db_name=${plugin_snake_lower:0:51}"_integration"; # example_plugin_integration
plugin_db_username=${plugin_slug:0:31}; # 32 character max for username 
plugin_db_password=$plugin_slug;
```


And display them so you can change them if you need to:

```
echo plugin_slug=$plugin_slug \# Should only contain lowercase letters,numbers and hyphens;
echo plugin_snake=$plugin_snake;
echo plugin_snake_lower=$plugin_snake_lower;
echo plugin_package_name=$plugin_package_name;
echo plugin_capitalized=$plugin_capitalized;
echo test_site_db_name=$test_site_db_name;
echo test_db_name=$test_db_name;
echo plugin_db_username=$plugin_db_username;
echo plugin_db_password=$plugin_db_password;
```

Then this block of commands will take care of most of the downloading:

```
git clone https://github.com/BrianHenryIE/WordPress-Plugin-Boilerplate.git;
mv WordPress-Plugin-Boilerplate $plugin_slug;
cd $plugin_slug;


# Branches can be merged here.
# git merge origin/no-loader

open -a PhpStorm .;
```

Then the renaming/replacing:

```
find . -depth -name '*plugin-slug*' -execdir bash -c 'git mv "$1" "${1//plugin-slug/'$plugin_slug'}"' bash {} \;
find . -depth \( -name '*.php' -o -name '*.txt' -o -name '.env.testing' -o -name '*.md' \) -exec sed -i '' "s/plugin_title/$plugin_name/g" {} +
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.json' -o -name '*.xml' -o -name '.env.testing'  -o -name '*.yml' -o -name '.gitignore' -o -name '.htaccess' -o -name '*.md' \) -exec sed -i '' 's/plugin-slug/'$plugin_slug'/g' {} +
find . -depth \( -name '*.php' -o -name '*.testing' \) -exec sed -i '' 's/plugin_snake_lower/'$plugin_snake_lower'/g' {} +
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.json' -o -name '*.xml' \) -exec sed -i '' 's/Plugin_Snake/'$plugin_snake'/g' {} \;
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.xml' \) -exec sed -i '' 's/Plugin_Package_Name/'$plugin_package_name'/g' {} \;
find . -type f \( -name '*.json' \) -exec sed -i '' 's/Plugin_Package_Name/'$(echo $plugin_package_name | sed 's/\\/\\\\/g')'/g' {} \;
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.json' -o -name '*.xml' \) -exec sed -i '' 's#PHP_Package_Name#'$php_package_name'#g' {} \;
find . -depth -name '*.php' -exec sed -i '' 's/PLUGIN_NAME/'$plugin_capitalized'/g' {} +
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.json' \) -exec sed -i '' "s/Your Name/$your_name/g" {} +
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.json' -o -name '.env.testing' \) -exec sed -i '' "s/email@example.com/$your_email/g" {} +
find . -type f \( -name '.env.testing' \) -exec sed -i '' 's/plugin-db-username/'$plugin_db_username'/g' {} +
```

Create two local databases for tests:

```
# export PATH=${PATH}:/usr/local/mysql/bin

# mysql_username="root"
# mysql_password="secret"

# Make .env available to zsh or bash
[[ $0 == "-zsh" ]] && source .env.testing || export $(grep -v '^#' .env.testing | xargs);

# Create the database user: (different syntax for MariaDB and MySQL).
[[ $(mysqld --version) =~ .*MariaDB.* ]] && mysql -u $mysql_username -p$mysql_password -e "CREATE USER '"$TEST_DB_USER"'@'%' IDENTIFIED BY '"$TEST_DB_PASSWORD"';" || mysql -u $mysql_username -p$mysql_password -e "CREATE USER '"$TEST_DB_USER"'@'%' IDENTIFIED WITH mysql_native_password BY '"$TEST_DB_PASSWORD"';";

# Create the databases.
mysql -u $mysql_username -p$mysql_password -e "CREATE DATABASE "$TEST_SITE_DB_NAME"; USE "$TEST_SITE_DB_NAME"; GRANT ALL PRIVILEGES ON "$TEST_SITE_DB_NAME".* TO '"$TEST_DB_USER"'@'%';";
mysql -u $mysql_username -p$mysql_password -e "CREATE DATABASE "$TEST_DB_NAME"; USE "$TEST_DB_NAME"; GRANT ALL PRIVILEGES ON "$TEST_DB_NAME".* TO '"$TEST_DB_USER"'@'%';";
```

Install everything, setup WordPress, save a copy of the database:

```
composer update

# Make .env available to bash or zsh
[[ $0 == "-zsh" ]] && source .env.testing || export $(grep -v '^#' .env.testing | xargs);

vendor/bin/wp core install --url="localhost:8080/$PLUGIN_SLUG" --title="$PLUGIN_NAME" --admin_user=admin --admin_password=password --admin_email=admin@example.org;


vendor/bin/wp plugin activate $PLUGIN_SLUG;
vendor/bin/wp user create bob bob@example.org --user_pass=password

mysqldump -u $TEST_SITE_DB_USER -p$TEST_SITE_DB_PASSWORD  $TEST_SITE_DB_NAME > tests/_data/dump.sql;
```


Run the tests to confirm it's working (this also does code generation necessary for autocomplete):

```
cp .env.secret.dist .env.secret

vendor/bin/codecept clean
vendor/bin/codecept run acceptance;
```

If this is a WooCommerce plugin:

```
composer require wpackagist-plugin/woocommerce --dev --no-scripts;
# or if you need the WooCommerce test helpers:
# composer require woocommerce/woocommerce --dev --no-scripts;
# ... although this requires extra setup commands not included here.

composer require wpackagist-theme/storefront --dev --no-scripts;
composer require php-stubs/woocommerce-stubs --dev --no-scripts;

vendor/bin/wp plugin activate woocommerce;
vendor/bin/wp theme activate storefront;

vendor/bin/wp wc tool run install_pages --user=admin;

# Import sample products
vendor/bin/wp plugin install wordpress-importer --activate
vendor/bin/wp import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=create

# Create a customer
vendor/bin/wp wc customer create --email='woo@woo.local' --billing='{"first_name":"Bob","last_name":"Tester","company":"Woo", "address_1": "123 Main St.", "city":"New York", "state:": "NY", "country":"USA"}' --shipping='{"first_name":"Bob","last_name":"Tester","company":"Woo", "address_1": "123 Main St.", "city":"New York", "state:": "NY", "country":"USA"}' --password='hunter2' --username='mrbob' --first_name='Bob' --last_name='Tester' --user=admin;

# Create dump after changing site.
[[ $0 == "-zsh" ]] && source .env.testing || export $(grep -v '^#' .env.testing | xargs);

mysqldump -u $TEST_SITE_DB_USER -p$TEST_SITE_DB_PASSWORD  $TEST_SITE_DB_NAME > tests/_data/dump.sql;
```

Discard this boilerplate repo's .git and README and start fresh:

```
rm -rf .git;
rm README.md;
mv README-rename.md README.md;

git init;
git add README.md;
git commit -m "Initial commit";
```

Set up GitHub using [GitHub CLI](https://cli.github.com/) (`brew install gh`, `gh auth login`). You might want to set the repo private here too, by appending `--private`.

```
gh repo create $PLUGIN_SLUG --public --push --source . ;
```

Setup gh-pages branch (for code coverage html report)).

```
git checkout --orphan gh-pages;

touch index.html;
git add index.html;
git commit -m 'Set up gh-pages branch' index.html;

git push origin gh-pages;
git checkout master;
```

Start a dev branch.

```
git checkout -b dev
git add .
git commit -am "Dev"
```

Add WordPress.org username and password to GitHub Secrets for use in deployments:

```
gh secret set SVN_USERNAME -b"${WP_ORG_SVN_USERNAME}"
gh secret set SVN_PASSWORD -b"${WP_ORG_SVN_PASSWORD}"
```

The default admin login is `admin`/`password`:

```
open "http://localhost:8080/$PLUGIN_SLUG/wp-admin"
```
 
## Usage

### WordPress Coding Standards

To see [WordPress Coding Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards) errors using [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) run:

```
vendor/bin/phpcs
```

Use PHP Code Beautifier and Fixer to automatically correct them where possible:

```
vendor/bin/phpcbf
```

https://make.wordpress.org/core/2020/03/20/updating-the-coding-standards-for-modern-php/
https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/

### Testing

Tests are handled with [WP-Browser](https://github.com/lucatume/wp-browser),

```
vendor/bin/codecept run unit
vendor/bin/codecept run wpunit
vendor/bin/codecept run integration
vendor/bin/codecept run acceptance
```

When making changes to the local WordPress installation to prep for acceptance tests, it needs to be saved because it is restored each time:

```
# export PATH=${PATH}:/usr/local/mysql/bin

[[ $0 == "-zsh" ]] && source .env.testing || export $(grep -v '^#' .env.testing | xargs);

mysql -u $TEST_SITE_DB_USER -p$TEST_SITE_DB_PASSWORD $TEST_SITE_DB_NAME < tests/_data/dump.sql
```

If you need to manually restore it:

```
[[ $0 == "-zsh" ]] && source .env.testing || export $(grep -v '^#' .env.testing | xargs);

mysql -u $mysql_username -p$mysql_password $TEST_SITE_DB_NAME < tests/_data/dump.sql
```


### Deployment

To create a .zip archive for uploading to WordPress:

```
mv src $(basename "`pwd`"); zip -r $(basename "`pwd`").zip $(basename "`pwd`"); mv $(basename "`pwd`") src;
```

To configure automatic WordPress.org plugin repository deployment, add your WordPress.org username and password as Secrets `SVN_USERNAME` and `SVN_PASSWORD` in the GitHub repository's settings, then when a Release is created, the plugin will be updated on WordPress.org (from [zerowp.com](https://zerowp.com)'s [Use Github Actions to publish WordPress plugins on WP.org repository](https://zerowp.com/use-github-actions-to-publish-wordpress-plugins-on-wp-org-repository/)).

## Composer Notes

By convention, WordPress plugins and themes installed by composer get installed into the project's `/wp-content/plugins` and `/wp-content/themes` directory. In a typical PHP project, libraries required by the project during runtime are installed in the `vendor` directory. In the case of this project, libraries are downloaded to the project's `vendor` folder, then their files copied to `src/vendor` and their namespace changed.

https://stackoverflow.com/a/32407670/336146

### Mozart

[Mozart](https://github.com/coenjacobs/mozart) is included in composer.json to prefix libraries' namespaces to avoid clashes with other WordPress plugins. e.g. in this case, [wp-namespace-autoloader](https://github.com/pablo-sg-pacheco/wp-namespace-autoloader) appears in `src/vendor/` with the namespace `Plugin_Name\Pablo_Pacheco\WP_Namespace_Autoloader`.

To use e.g. a [.ics parser](https://github.com/u01jmg3/ics-parser) in your project:

```
composer require johngrogg/ics-parser --dev --no-scripts
vendor/bin/mozart compose
``` 

The Mozart configuration in composer.json:

```
 "extra": {
  "mozart": {
   "dep_namespace": "Plugin_Name\\",
   "dep_directory": "/src/vendor/",
   "classmap_directory": "/src/dependencies/",
   "classmap_prefix": "Plugin_Name_"
  }
 }
```

### Composer-Patches

[composer-patches](https://github.com/cweagans/composer-patches) is used to apply PRs to composer dependencies (e.g. while waiting for the repository owners to accept the required changes). Currently nothing is being patched, but for hte majoriity of this project's life some thing or another has needed patching.

When you make a PR against a library, or see an existing unmerged PR that you need, add `.patch` to the end of the PR's URL and add it to your project's composer.json like:

```
 "extra": {
  "patches": {
   "coenjacobs/mozart": {
    "Allow default packages": "https://github.com/coenjacobs/mozart/pull/49.patch"
   }
  }
 }
```

https://github.com/cweagans/composer-patches/issues/286

### WordPress Packagist

Plugins published on WordPress.org are made available through composer via [wpackagist.org](https://wpackagist.org/). Add to composer.json using:

```
 "repositories": [
  {
   "type":"composer",
   "url":"https://wpackagist.org"
  }
 ]
```

Then add the plugin or theme 

```
composer require wpackagist-theme/twentytwenty --dev --no-scripts
```

```
 "require-dev": {
  "wpackagist-plugin/bh-wp-autologin-urls":">=1.1",
  "wpackagist-theme/twentytwenty":"*"
 }
```

### Local Projects

To add a project in another local directory, ensure it has its own `composer.json` with `"name": "brianhenryie/local-lib"`

```
 "repositories": [
  {
   "type": "path",
   "url": "../phpunit-github-actions-printer"
  }
 ]
```

```
 "require": {
  "brianhenryie/local-lib":"*",
 }
```

https://getcomposer.org/doc/articles/repository-priorities.md

### GitHub repository containing composer.json

By including plugins direct from GitHub, you may get additional files such as unit tests and JavaScript sources.

```
 "repositories": [
 {
  "url": "https://github.com/johnbillion/user-switching",
  "type": "git"
 }
```

```
 "require-dev": {
  "johnbillion/user-switching": "dev-master"
 }
```

### GitHub Branch/Fork

When including a fork or branch, the repository may need to be changed, and the branch name should be prefixed with `dev-`.

```
 "repositories": [
 {
  "url": "https://github.com/BrianHenryIE/wp-namespace-autoloader",
  "type": "git"
 }
```

```
 "require": {
  "pablo-sg-pacheco/wp-namespace-autoloader": "dev-brianhenryie"
 }
```


### GitHub repository without composer.json

For GitHub repositories that are not set up for composer:

```
 "repositories": [
 {
  "type": "package",
  "package": {
   "name": "enhancedathlete/ea-wp-aws-sns-client-rest-endpoint",
   "version": "1.0",
   "source": {
    "url": "https://github.com/EnhancedAthlete/EA-WP-AWS-SNS-Client-REST-Endpoint",
    "type": "git",
    "reference": "master"
   }
  }
 }
```

```
 "require-dev": {
  "enhancedathlete/ea-wp-aws-sns-client-rest-endpoint":"*"
 }
```

### SatisPress

[SatisPress](https://github.com/cedaro/satispress) is a WordPress plugin that allows you to expose the plugins and themes installed on your WordPress site via a private Composer repository.

Once installed, plugins need to be whitelisted via checkboxes in the admin UI's plugins.php page, and credentials need to be defined in Settings/SatisPress.

```
 "repositories": [
 {
  "type": "composer",
  "url": "https://brianhenry.ie/satispress/"
 }
```

```
 "require-dev": {
  "satispress/my-plugin": "*
 }  
```

When running `composer update` you will be prompted (once) for the credentials you created on the site.

### Symlinks

If an included WordPress plugin or theme does not install to the project's `wp-content` folder, it can be symlinked with Composer [Symlink Handler](https://github.com/kporras07/composer-symlinks).

```
 "extra": {
  "symlinks": {
   "./vendor/enhancedathlete/ea-wp-aws-sns-client-rest-endpoint/trunk": "./wp-content/plugins/ea-wp-aws-sns-client-rest-endpoint"
  }
```

### More

https://github.com/wikimedia/composer-merge-plugin

https://github.com/wikimedia/composer-merge-plugin


Xdebug with WP ClI. Run once in your Terminal session:
```
export XDEBUG_CONFIG="idekey=PHPSTORM remote_connect_back=1"
```

Xdebug with Postman. Append to the URL:

```
&XDEBUG_SESSION_START=PHPSTORM
```

## Notes

#### WPPB-Lib

The WordPress Plugin Boilerplate as-is passes the plugin name and version to every class. This code and the loader code are better suited to a library than every plugin's own code, so a branch exists wppb-lib that can be merged. I haven't see where the reason to pass the name and version to every class is. Maybe it can be handled better in the root plugin file in namespaced methods?

### Minimum WordPress Version

The minimum WordPress version can be determined using [wpseek.com's Plugin Doctor](https://wpseek.com/pluginfilecheck/).

## TODO

* PHP Storm configuration
* PHP Unit for PRs via GitHub Actions
* Code coverage badge
* Downloads count badge
* JavaScript unit testing
* Local Git hooks for WPCS
* Disable commiting to master
* Update Git origin instruction
* Composer scripts

Tools to investigate:

* https://github.com/bruli/php-git-hooks
* https://github.com/olivertappin/phpcs-diff
* https://github.com/barryvdh/composer-cleanup-plugin
* https://github.com/aaemnnosttv/wp-sqlite-db
* https://github.com/Automattic/wordbless
* https://github.com/alstr/todo-to-issue-action

I have made some progress on a lot of these open an issue with changes you propose working on and I'll tidy up what I have and hopefully save you some time

## Acknowledgements

The contributors to [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/) and more.

WP-Browser, Mozart and SatisPress are three tools that really take this project to another level.
