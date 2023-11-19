<?php
/**
 * The wp-admin settings page.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    PHP_Package_Name
 */

namespace Plugin_Package_Name\Admin;

use Plugin_Package_Name\Settings_Interface;
use Psr\Log\LogLevel;

/**
 * The setting page of the plugin.
 */
class Settings_Page {

	/**
	 * The settings, to pass to the individual fields for populating.
	 *
	 * @var Settings_Interface $settings The previously saved settings for the plugin.
	 */
	protected Settings_Interface $settings;

	/**
	 * Settings_Page constructor.
	 *
	 * @param Settings_Interface $settings The previously saved settings for the plugin.
	 */
	public function __construct( Settings_Interface $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Add the Plugin_Name settings menu-item/page as a submenu-item of the Settings menu.
	 *
	 * /wp-admin/options-general.php?page=plugin-slug
	 *
	 * @hooked admin_menu
	 */
	public function add_settings_page(): void {

		add_options_page(
			$this->settings->get_plugin_name(),
			$this->settings->get_plugin_name(),
			'manage_options',
			$this->settings->get_plugin_slug(),
			array( $this, 'print_plugin_admin_page' )
		);
	}

	/**
	 * Registered above, called by WordPress to display the admin settings page.
	 *
	 * When the template is included, the variables in this function's scope will be available to it.
	 */
	public function print_plugin_admin_page(): void {

		$template = 'admin/plugin-slug-admin-display.php';

		$template_admin_settings_page = WP_PLUGIN_DIR . '/' . plugin_dir_path( $this->settings->get_plugin_basename() ) . 'templates/' . $template;

		// Check the child theme for template overrides.
		if ( file_exists( get_stylesheet_directory() . $template ) ) {
			$template_admin_settings_page = get_stylesheet_directory() . $template;
		} elseif ( file_exists( get_stylesheet_directory() . 'templates/' . $template ) ) {
			$template_admin_settings_page = get_stylesheet_directory() . 'templates/' . $template;
		}

		$plugin_slug = $this->settings->get_plugin_slug();
		$plugin_name = $this->settings->get_plugin_name();

		/**
		 * Allow overriding the admin settings template.
		 *
		 * @param string $template_admin_settings_page The full filepath to the template to be included.
		 * @param array<mixed> $template_args The variables that will be available to the template.
		 */
		$filtered_template_admin_settings_page = apply_filters( "{$this->settings->get_plugin_slug()}_admin_settings_page_template", $template_admin_settings_page, func_get_args() );

		if ( ! file_exists( $filtered_template_admin_settings_page ) ) {
			include $template_admin_settings_page;
		} else {
			include $filtered_template_admin_settings_page;
		}
	}

	/**
	 * Register the one settings section with WordPress.
	 *
	 * @hooked admin_init
	 */
	public function setup_sections(): void {

		$settings_page_slug_name = $this->settings->get_plugin_slug();

		add_settings_section(
			'default',
			'Settings',
			function(){},
			$settings_page_slug_name
		);
	}

	/**
	 *
	 * @hooked admin_init
	 *
	 * @see https://github.com/reside-eng/wordpress-custom-plugin/blob/master/admin/class-wordpress-custom-plugin-admin.ph\\\]\
	 */
	public function setup_fields(): void {

		$setting_id = 'plugin-snake-lower_log_level';

		$print_settings_field_args = array(
			'helper' => __( 'Set to Debug to diagnose problems, Info to see times this plugin is logging users in. Warning: debug logs will contain autologin codes.', 'plugin-slug' ),
			'supplemental' => __( 'default: Notice', 'plugin-slug' ),
		);

		add_settings_field(
			$setting_id,
			'Log level',
			array( $this, 'print_log_level_field' ),
			$this->settings->get_plugin_slug(),
			'default',
			$print_settings_field_args
		);
	}

	/**
	 * @param array{placeholder:string, helper:string, supplemental:string, default:string} $arguments The field data as registered with add_settings_field().
	 */
	public function print_log_level_field( array $args ): void {

		$setting_id = 'plugin-snake-lower_log_level';

		$log_levels = array(
			'none',
			LogLevel::ERROR,
			LogLevel::WARNING,
			LogLevel::NOTICE,
			LogLevel::INFO,
			LogLevel::DEBUG
		);

		printf(
			'<fieldset><label for="%1$s"><select id="%1$s" name="%1$s" />',
			esc_attr( $setting_id )
		);

		$logs_url = admin_url( 'admin.php?page=plugin-slug-logs' );

		$supplemental = sprintf(
			'<p class="description">%s – <a href="%s">%s</a></p>',
			esc_html(  __( 'default: Notice', 'plugin-slug' ) ),
			esc_url( $logs_url ),
			esc_html(  __( 'View Logs', 'plugin-slug' ) ),
		);

		$this->print_html_select(
			array(
				'options' => $log_levels,
				'selected' => $this->settings->get_log_level(),
				'selected_name' => ucfirst( $this->settings->get_log_level() ),
				'helper' => __( 'Set to Debug to diagnose problems, Info to see times this plugin is logging users in. Warning: debug logs will contain autologin codes.', 'plugin-slug' ),
				'supplemental' =>$supplemental,
			)
		);
	}

	public function print_html_select( array $args ): void {
		foreach ( $args['options'] as $option ) {

			echo '<option value="' . esc_attr( $option ) . '"' . ( $args['selected'] === $option ? ' selected' : '' ) . '>' . esc_html( ucfirst( $option ) ) . '</option>';
		}

		echo '</select>';

		printf(
			'%1$s</label></fieldset>',
			wp_kses( $args['helper'], array( 'i' => array() ) )
		);

		if( isset( $args['supplemental'] ) ) {
			echo wp_kses_post( $args['supplemental'] );
		}

	}

	/**
	 * TODO: move to wp-includes/class-settings.php
	 *
	 * @hooked admin_init
     */
	public function register_settings(): void {
		$setting_id = 'plugin-snake-lower_log_level';

		/**
		 * Data used to describe the setting when registered.
		 *
		 * @param array $args {
		 *  type:string,
		 *  description:string,
		 *  sanitize_callback:callable,
		 *  show_in_rest:bool|array,
		 *  default:mixed
		 *  }
		 */
		$register_setting_args_array = array(
			'type'              => 'string',
			'description'       => "The log level for {$this->settings->get_plugin_name()}.",
			'sanitize_callback' => array( $this, 'sanitize_log_level'),
			'show_in_rest'      => false,
			'default'           => 'notice',
		);

		register_setting(
			$this->settings->get_plugin_slug(), // group.
			$setting_id, // Individual option name, as retrievable by `get_option( 'my-option-name' )`.
			$register_setting_args_array
		);
	}

	public function sanitize_log_level( string $log_level ): string {
		return in_array(
			$log_level,
			array( 'none', 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency' ),
			true
		) ? $log_level : 'notice';
	}
}
