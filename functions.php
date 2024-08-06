<?php
/**
 * Global functions for other plugins to use.
 *
 * @package Plugin_Package_Name
 */

if ( ! function_exists( 'plugin_snake_lower' ) ) {
	/**
	 * Get the plugin name.
	 *
	 * @return string The plugin name.
	 */
	function plugin_snake_lower(): string {
		return 'Plugin Name';
	}
}
