<?php
/**
 * Tests for I18n. Tests load_plugin_textdomain.
 *
 * @package PHP_Package_Name
 * @author  Your Name <email@example.com>
 */

namespace Plugin_Package_Name\WP_Includes;

/**
 * Class Plugin_Package_Name_Test
 *
 * @see I18n
 */
class I18n_Integration_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * AFAICT, this will fail until a translation has been added.
	 *
	 * @see load_plugin_textdomain()
	 * @see https://gist.github.com/GaryJones/c8259da3a4501fd0648f19beddce0249
	 */
	public function test_load_plugin_textdomain(): void {

		$this->markTestSkipped( 'Needs one translation before test might pass.' );

		global $plugin_root_dir;

		$this->assertTrue( file_exists( $plugin_root_dir . '/languages/' ), '/languages/ folder does not exist.' );

		// Seems to fail because there are no translations to load.
		$this->assertTrue( is_textdomain_loaded( 'plugin-slug' ), 'i18n text domain not loaded.' );

	}

}
