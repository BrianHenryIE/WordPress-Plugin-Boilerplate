<?php
/**
 * Loads all required classes
 *
 * Uses classmap, PSR4 & wp-namespace-autoloader.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           PHP_Package_Name
 *
 * @see https://github.com/pablo-sg-pacheco/wp-namespace-autoloader/
 */

namespace Plugin_Package_Name;

use Plugin_Package_Name\Alley_Interactive\Autoloader\Autoloader;

$class_map_files = array(
	__DIR__ . '/autoload-classmap.php',
);
foreach ( $class_map_files as $class_map_file ) {
	if ( file_exists( $class_map_file ) ) {

		$class_map = include $class_map_file;

		if ( is_array( $class_map ) ) {
			spl_autoload_register(
				function ( $classname ) use ( $class_map ) {

					if ( array_key_exists( $classname, $class_map ) && file_exists( $class_map[ $classname ] ) ) {
						require_once $class_map[ $classname ];
					}
				}
			);
		}
	}
}
unset( $class_map_files, $class_map_file, $class_map );

// Load strauss classes after autoload-classmap.php so classes can be substituted.
require_once __DIR__ . '/vendor-prefixed/autoload.php';

Autoloader::generate(
	'Plugin_Package_Name',
	__DIR__ . '/src',
)->register();
