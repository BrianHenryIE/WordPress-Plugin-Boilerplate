<?php
/**
 * Tests for the root plugin file.
 *
 * @package PHP_Package_Name
 * @author  Your Name <email@example.com>
 */

namespace Plugin_Package_Name;

use BrianHenryIE\WP_Logger\Logger;
use Psr\Log\NullLogger;

/**
 * Class Plugin_WP_Mock_Test
 */
class Plugin_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp() : void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * Verifies the plugin initialization.
	 * Verifies the plugin does not output anything to screen.
	 */
	public function test_plugin_include(): void {

		// Prevents code-coverage counting, and removes the need to define the WordPress functions that are used in that class.
		\Patchwork\redefine(
			array( Plugin_Snake::class, '__construct' ),
			function() {}
		);

		\Patchwork\redefine(
			array( Logger::class, 'instance' ),
			function( $settings ) { return new NullLogger(); }
		);

		// Defined in `bootstrap.php`.
		global $plugin_root_dir, $plugin_slug, $plugin_basename;

		\WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_root_dir . '/',
			)
		);

		\WP_Mock::userFunction(
			'plugin_basename',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_basename,
			)
		);

		\WP_Mock::userFunction(
			'plugins_url',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => 'http://localhost:8080/' . $plugin_slug,
				'times'  => 1,
			)
		);

		\WP_Mock::userFunction(
			'trailingslashit',
			array(
				'args'       => array( \WP_Mock\Functions::type( 'string' ) ),
				'return_arg' => true,
				'times'      => 1,
			)
		);

		\WP_Mock::userFunction(
			'register_activation_hook',
			array(
				'args'  => array( \WP_Mock\Functions::type( 'string' ), \WP_Mock\Functions::type( 'array' ) ),
				'times' => 1,
			)
		);

		\WP_Mock::userFunction(
			'register_deactivation_hook',
			array(
				'args'  => array( \WP_Mock\Functions::type( 'string' ), \WP_Mock\Functions::type( 'array' ) ),
				'times' => 1,
			)
		);

		ob_start();

		include $plugin_root_dir . '/plugin-slug.php';

		$printed_output = ob_get_contents();

		ob_end_clean();

		$this->assertEmpty( $printed_output );

		$this->assertArrayHasKey( 'plugin_snake_lower', $GLOBALS );

		$this->assertInstanceOf( Plugin_Snake::class, $GLOBALS['plugin_snake_lower'] );

	}

}
