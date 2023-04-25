<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    PHP_Package_Name
 */

namespace Plugin_Package_Name\WP_Includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class I18n {

	const TEXTDOMAIN = 'plugin-slug';

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @hooked init
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain(): void {

		load_plugin_textdomain(
			self::TEXTDOMAIN,
			false,
			plugin_basename( dirname( __FILE__, 3 ) ) . '/languages/'
		);

	}

}
