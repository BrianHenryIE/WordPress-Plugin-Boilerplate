# WordPress Plugin Boilerplate – BrianHenryIE Fork

The popular [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/) with added Composer, namespaces, autoloading, PHP Unit, and WordPress.org deployment.

## Overview

The WordPress Plugin Boilerplate is a well-documented starting point for WordPress plugins which encourages consistent conventions in plugin development. This fork expands on that base using modern PHP practices and providing a more comprehensive development environment setup. An example plugin where the changes have been tested is [Autologin URLs](https://github.com/BrianHenryIE/BH-WP-Autologin-URLs).

## Installation

Open Terminal and set the following variables (note the `\` before spaces):

```
plugin_name=Example\ Plugin
plugin_slug=example-plugin
plugin_snake=example_plugin
plugin_package_name=Example_Plugin
plugin_package_capitalized=EXAMPLE_PLUGIN
```

Then this block of commands will take care of most of the downloading and renaming.

```
git clone https://github.com/BrianHenryIE/WordPress-Plugin-Boilerplate.git
mv WordPress-Plugin-Boilerplate $plugin_slug
cd $plugin_slug
find . -depth -name '*.txt' -exec sed -i "s/Plugin Name/$plugin_name/" {} +
find . -depth -name '*plugin-name*' -execdir bash -c 'git mv "$1" "${1//plugin-name/'$plugin_slug'}"' bash {} \;
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.json' -o -name '*.xml' -o -name ".gitignore" \) -exec sed -i "s/plugin-name/$plugin_slug/" {} +
find . -depth -name $plugin_slug'.php'  -exec sed -i "s/plugin_snake/$plugin_snake/g" {} +
find . -depth -name $plugin_slug'.php'  -exec sed -i "s/WordPress Plugin Boilerplate/$plugin_name/" {} +
find . -type f \( -name '*.php' -o -name '*.txt' -o -name '*.json' -o -name '*.xml' \) -exec sed -i "s/Plugin_Name/$plugin_package_name/g" {} \;
find . -depth -name '*.php' -exec sed -i "s/PLUGIN_NAME/$plugin_package_capitalized/" {} +
composer install
```

The [wordpress-develop](https://github.com/wordpress/wordpress-develop) tests are configured to require a local [MySQL database](https://dev.mysql.com/downloads/mysql/) (which gets wiped each time) and this plugin is set to require a database called `wordpress_tests` and a user named `wordpress-develop` with the password `wordpress-develop`. 

To setup the database, open MySQL shell:

```
mysql -u root -p
```

Create the database and user, granting the user full permissions:

```
CREATE DATABASE wordpress_tests;
CREATE USER 'wordpress-develop'@'%' IDENTIFIED WITH mysql_native_password BY 'wordpress-develop';
GRANT ALL PRIVILEGES ON wordpress_tests.* TO 'wordpress-develop'@'%';
```

```
quit
```

Run the tests to confirm it's working:

```
vendor/bin/phpcbf; 
vendor/bin/phpcs; 
phpunit -c tests/wordpress-develop/phpunit.xml --coverage-php tests/reports/wordpress-develop.cov --coverage-text; 
phpunit -c tests/wp-mock/phpunit.xml --coverage-php tests/reports/wp-mock.cov --coverage-text; 
vendor/bin/phpcov merge --clover tests/reports/clover.xml --html tests/reports/html tests/reports --text
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

To configure WPCS checking on GitHub PRs, [generate a Personal Access Token](https://github.com/settings/tokens) with the `public_repo` permission, and under your GitHub repository's Settings's Secrets add it as `GH_BOT_TOKEN`.

### PHPUnit

[WP_Mock](https://github.com/10up/wp_mock) tests can be run with:

```
phpunit -c tests/wp-mock/phpunit.xml
```

The wordpress-develop tests can be run with:

```
phpunit -c tests/wordpress-develop/phpunit.xml
```

### Code Coverage

Code coverage reporting requires [Xdebug](https://xdebug.org/) installed.

Adding `--coverage-text` to `phpunit` commands displays their individual coverage in the console. 

Adding `--coverage-php tests/reports/wordpress-develop.cov` to each allows their coverage stats to be merged using:

```
vendor/bin/phpcov merge --clover tests/reports/clover.xml --html tests/reports/html tests/reports
```

### Minimum PHP Version

Use [PHPCompatibilityWP](https://github.com/PHPCompatibility/PHPCompatibilityWP) to check the minimum PHP version required with: 

```
./vendor/bin/phpcs -p ./trunk --standard=PHPCompatibilityWP --runtime-set testVersion 5.7-
```

### Deployment

To create a .zip archive for uploading to WordPress:

```
mv src $(basename "`pwd`"); zip -r $(basename "`pwd`").zip $(basename "`pwd`"); mv $(basename "`pwd`") src;
```

To configure automatic WordPress.org plugin repository deployment, add your WordPress.org username and password as Secrets `SVN_USERNAME` and `SVN_PASSWORD` in the GitHub repository's settings, then when a Release is created, the plugin will be updated on WordPress.org (from [zerowp.com](https://zerowp.com)'s [Use Github Actions to publish WordPress plugins on WP.org repository](https://zerowp.com/use-github-actions-to-publish-wordpress-plugins-on-wp-org-repository/)).

## Composer Notes

By convention, WordPress plugins and themes installed by composer get installed into the project's `/wp-content/plugins` and `/wp-content/themes` directory. In a typical PHP project, libraries required by the project during runtime are installed in the `vendor` directory. In the case of this project, libraries are downloaded to the project's `vendor` folder, then their files copied to `src/vendor` and their namespace changed.

### Mozart

[Mozart](https://github.com/coenjacobs/mozart) is included in composer.json to prefix libraries' namespaces to avoid clashes with other WordPress plugins. e.g. in this case, [wp-namespace-autoloader](https://github.com/pablo-sg-pacheco/wp-namespace-autoloader) appears in `src/vendor/` with the namespace `Plugin_Name\Pablo_Pacheco\WP_Namespace_Autoloader`.

```
 "extra": {
  "mozart": {
   "dep_namespace": "Plugin_Name\\",
   "dep_directory": "/src/vendor/",
   "classmap_directory": "/classes/dependencies/",
   "classmap_prefix": "Plugin_Name_"
  }
 }
```

### Composer-Patches

[composer-patches](https://github.com/cweagans/composer-patches) is used to apply PRs to composer dependencies (e.g. while waiting for the repository owners to accept the required changes). In this case, Mozart is patched with a PR (which configures Mozart to process all libraries listed in composer.json `require` whereas without the patch, each needs to be specified).[*](https://mindsize.me/blog/development/how-to-backport-woocommerce-security-patches-using-git-and-composer/)

```
 "extra": {
  "patches": {
   "coenjacobs/mozart": {
    "Allow default packages": "https://github.com/coenjacobs/mozart/pull/34.patch"
   }
  }
 }
```

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
 "require-dev": {
  "wpackagist-plugin/bh-wp-autologin-urls":">=1.1",
  "wpackagist-theme/twentytwenty":"*"
 }
```

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

## Notes


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
* Composer command for PHPCS+PHP Unit
* [PHP 7](https://stitcher.io/blog/php-in-2020) site:github.com inurl:WordPress-Plugin-Boilerplate php 7.3


## Acknowledgements

The contributors to [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/) and more.
