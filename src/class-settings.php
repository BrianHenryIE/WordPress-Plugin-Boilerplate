<?php
/**
 * A plain object abstracting settings.
 *
 * @package PHP_Package_Name
 */

namespace Plugin_Package_Name;

/**
 * Typed settings.
 */
class Settings implements Settings_Interface {

	public function get_plugin_name(): string {
		return 'plugin_title';
	}

	/**
	 * The current plugin version, as defined in the root plugin file, or a string hopefully in sync with the root file.
	 *
	 * @used-by Admin_Assets::enqueue_scripts()
	 * @used-by Admin_Assets::enqueue_styles()
	 * @used-by Frontend_Assets::enqueue_scripts()
	 * @used-by Frontend_Assets::enqueue_styles()
	 *
	 * @return string
	 */
	public function get_plugin_version(): string {
		return defined( 'PLUGIN_NAME_VERSION' )
			? PLUGIN_NAME_VERSION
			: '1.0.0';
	}

	/**
	 * The plugin basename, as defined in the root plugin file, or a string hopefully in sync with the true basename.
	 *
	 * @used-by Admin_Assets::enqueue_scripts()
	 * @used-by Admin_Assets::enqueue_styles()
	 * @used-by Frontend_Assets::enqueue_scripts()
	 * @used-by Frontend_Assets::enqueue_styles()
	 *
	 * @return string
	 */
	public function get_plugin_basename(): string {
		return defined( 'PLUGIN_NAME_BASENAME' )
			? PLUGIN_NAME_BASENAME
			: 'plugin-slug/plugin-slug.php';
	}

	public function get_plugin_slug(): string {
		return 'plugin-slug';
	}

	public function get_log_level(): string {
		return get_option( 'plugin_snake_lower_log_level', 'notice' );
	}
}
