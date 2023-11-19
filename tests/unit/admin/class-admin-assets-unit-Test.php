<?php
/**
 * Tests for Admin.
 *
 * @see Admin_Assets
 *
 * @package PHP_Package_Name
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace Plugin_Package_Name\Admin;

use Plugin_Package_Name\Settings;

/**
 * Class Admin_Test
 *
 * @coversDefaultClass \Plugin_Package_Name\Admin\Admin_Assets
 */
class Admin_Assets_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Verifies enqueue_styles() calls wp_enqueue_style() with appropriate parameters.
	 * Verifies the .css file exists.
	 *
	 * @covers ::enqueue_styles
	 * @see wp_enqueue_style()
	 */
	public function test_enqueue_styles(): void {

		global $plugin_root_dir, $plugin_slug;

		// Return any old url.
		\WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'return' => "https://example.org/wp-content/plugins/{$plugin_slug}/",
				'times'  => 1,
			)
		);

		$css_file = $plugin_root_dir . '/assets/plugin-slug-admin.css';
		$css_url  = "https://example.org/wp-content/plugins/{$plugin_slug}/assets/plugin-slug-admin.css";

		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times' => 1,
				'args'  => array( $plugin_slug, $css_url, array(), '1.0.0', 'all' ),
			)
		);

		$settings = $this->make(
			Settings::class,
			array(
				'get_plugin_version'  => '1.0.0',
				'get_plugin_basename' => 'plugin-slug/plugin-slug.php',
			)
		);

		$admin = new Admin_Assets( $settings );

		$admin->enqueue_styles();

		$this->assertFileExists( $css_file );
	}

	/**
	 * Verifies enqueue_scripts() calls wp_enqueue_script() with appropriate parameters.
	 * Verifies the .js file exists.
	 *
	 * @covers ::enqueue_scripts
	 * @see wp_enqueue_script()
	 */
	public function test_enqueue_scripts(): void {

		global $plugin_root_dir, $plugin_slug;

		// Return any old url.
		\WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'return' => "https://example.org/wp-content/plugins/{$plugin_slug}/",
			)
		);

		$handle    = $plugin_slug;
		$js_file   = $plugin_root_dir . '/assets/bh-wc-bitcoinpostage-shipping-method-admin.js';
		$js_url    = "https://example.org/wp-content/plugins/{$plugin_slug}/assets/bh-wc-bitcoinpostage-shipping-method-admin.js";
		$deps      = array( 'jquery' );
		$ver       = '1.0.0';
		$in_footer = true;

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'times' => 1,
				'args'  => array( $handle, $js_url, $deps, $ver, $in_footer ),
			)
		);

		\WP_Mock::userFunction(
			'admin_url',
			array(
				'times' => 1,
				'args'  => array( 'admin-ajax.php' ),
			)
		);

		\WP_Mock::userFunction(
			'wp_create_nonce',
			array(
				'times' => 1,
				'args'  => array( Admin_Assets::class ),
			)
		);

		\WP_Mock::userFunction(
			'wp_json_encode',
			array(
				'times' => 1,
				'args'  => array(
					\WP_Mock\Functions::type( 'array' ),
					\WP_Mock\Functions::type( 'int' )
				),
			)
		);

		\WP_Mock::userFunction(
			'wp_add_inline_script',
			array(
				'times' => 1,
				'args'  => array(
					'plugin-slug',
					\WP_Mock\Functions::type( 'string' ),
					'before'
				),
			)
		);

		$settings = $this->make(
			Settings::class,
			array(
				'get_plugin_version'  => '1.0.0',
				'get_plugin_basename' => 'bh-wc-bitcoinpostage-shipping-method/bh-wc-bitcoinpostage-shipping-method.php',
			)
		);

		$admin = new Admin_Assets( $settings );

		$admin->enqueue_scripts();

		$this->assertFileExists( $js_file );
	}
}
