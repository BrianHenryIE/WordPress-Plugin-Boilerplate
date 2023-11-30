<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @see \Plugin_Package_Name\Admin\Settings_Page
 * @see \Plugin_Package_Name\Admin\Settings_Page::print_plugin_admin_page()
 * @see \Plugin_Package_Name\Admin\Admin_Ajax
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    PHP_Package_Name
 *
 * Define variables available from `::display_plugin_admin_page()`.
 * @var string $plugin_name The plugin name.
 * @var string $plugin_slug The plugin slug.
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap <?php echo esc_attr( $plugin_slug ); ?>">

	<h2><?php echo esc_html( $plugin_name ); ?></h2>

	<?php settings_errors(); ?>


	<form method="POST" action="options.php">
		<?php
		settings_fields( $plugin_slug );
		do_settings_sections( $plugin_slug );
		submit_button();
		?>
	</form>

</div>
