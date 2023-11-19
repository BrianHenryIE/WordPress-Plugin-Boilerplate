<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           PHP_Package_Name
 *
 * @wordpress-plugin
 * Plugin Name:       plugin_title
 * Plugin URI:        http://github.com/username/plugin-slug/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Requires PHP:      7.4
 * Author:            Your Name
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-slug
 * Domain Path:       /languages
 *
 * GitHub Plugin URI: https://github.com/username/plugin-slug/
 * Release Asset:     true
 */

namespace Plugin_Package_Name;

use BrianHenryIE\WP_Logger\Logger;
use Plugin_Package_Name\WP_Includes\Activator;
use Plugin_Package_Name\WP_Includes\Deactivator;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	throw new \Exception( 'WordPress required but not loaded.' );
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );
define( 'PLUGIN_NAME_BASENAME', plugin_basename( __FILE__ ) );
define( 'PLUGIN_NAME_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_NAME_URL', trailingslashit( plugins_url( plugin_basename( __DIR__ ) ) ) );

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_plugin_snake_lower(): Plugin_Snake {

	$settings = new Settings();
	$logger = Logger::instance($settings);

	$plugin = new Plugin_Snake( $settings );

	return $plugin;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['plugin_snake_lower'] = instantiate_plugin_snake_lower();
