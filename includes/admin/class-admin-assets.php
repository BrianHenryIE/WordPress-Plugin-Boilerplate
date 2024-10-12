<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    PHP_Package_Name
 */

namespace Plugin_Package_Name\Admin;

use Plugin_Package_Name\Settings;

/**
 * The admin-specific JS and CSS of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Admin_Assets {

	/**
	 * The plugin settings.
	 *
	 * @uses Settings::get_plugin_version() for caching.
	 * @uses Settings::get_plugin_basename() for determining the plugin URL.
	 *
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * Constructor
	 *
	 * @param Settings $settings The plugin settings.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @hooked admin_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$version = $this->settings->get_plugin_version();

		$plugin_dir = plugin_dir_url( $this->settings->get_plugin_basename() );

		wp_enqueue_style( 'plugin-slug', $plugin_dir . 'assets/plugin-slug-admin.css', array(), $version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @hooked admin_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {

		// TODO: Conditionally load this, only on our own settings page.

		/**
		 * Load the file at assets/plugin-slug-admin.js on every admin page.
		 */
		wp_enqueue_script(
			$this->settings->get_plugin_slug(),
			plugin_dir_url( $this->settings->get_plugin_basename() ) . 'assets/plugin-slug-admin.js',
			array( 'jquery' ),
			$this->settings->get_plugin_version(),
			true
		);

		$script_data      = array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( self::class ),
			'isWpDebug' => defined( 'WP_DEBUG' ) ? constant( 'WP_DEBUG' ) : false,
		);
		$script_data_json = wp_json_encode( $script_data, JSON_PRETTY_PRINT );

		$script = <<<EOD
var plugin_snake_lower_script_data = $script_data_json;
EOD;

		wp_add_inline_script(
			$this->settings->get_plugin_slug(),
			$script,
			'before'
		);
	}
}
