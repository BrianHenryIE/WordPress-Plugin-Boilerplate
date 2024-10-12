<?php

namespace Plugin_Package_Name\WP_Includes;

class Admin_Ajax {

	/**
	 *
	 * Called when a POST to `wp-admin/admin-ajax.php` contains the `action` `plugin_snake_lower`.
	 *
	 * @hooked wp_ajax_plugin_snake_lower
	 */
	public function save_data(): void {

		if ( ! check_ajax_referer( self::class, false, false ) ) {
			wp_send_json_error( array( 'message' => 'Bad/no nonce.' ), 400 );
		}

		$success = true;

		$result = array();

		if ( $success ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}
}
