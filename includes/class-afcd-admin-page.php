<?php
/**
 * The plugin settings page.
 *
 * @since      1.0.1
 * @package    AFCD_Core_Development
 * @subpackage AFCD_Core_Development/includes
 * @author     Andrea Fercia
 */
class AFCD_Admin_Page implements AFCD_Integration_Interface {

	/**
	 * Registers the hooks for this integration.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
	}

	public function create_admin_menu() {
		$admin_page_slug       = 'afcd-core-development';
		$admin_page_title      = __( 'Core Development', 'afcd-core-development' );
		$admin_page_menu_title = $admin_page_title;

		add_menu_page(
			$admin_page_title,
			$admin_page_menu_title,
			'manage_options',
			$admin_page_slug,
			array( $this, 'admin_menu_page' ),
			'dashicons-admin-tools',
			100
		);
	}

	public function admin_menu_page() {
		echo '<div id="afcd-admin-page" class="wrap">';
		echo '<h1>Core Development</h1>';

		// Handle form submission.
		if ( isset( $_POST['afcd_nonce'] ) && wp_verify_nonce( $_POST['afcd_nonce'], 'afcd_nonce' ) ) {
			$user_id = get_current_user_id();

			$debug_spinner = isset( $_POST['afcd_debug_spinner'] ) ? 1 : 0;

			// Save the setting.
			update_user_meta( $user_id, 'afcd_debug_spinner', $debug_spinner );

			echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Settings saved successfully.', 'afcd-core-development' ) . '</p></div>';

			// For immediate UI feedback, we use the submitted value.
			$debug_spinner_enabled = $debug_spinner;
		} else {
			// Get current user's setting.
			$current_user_id       = get_current_user_id();
			$debug_spinner_enabled = get_user_meta( $current_user_id, 'afcd_debug_spinner', true );
		}

		echo '<form method="post">';
		wp_nonce_field( 'afcd_nonce', 'afcd_nonce' );

		echo '<table class="form-table">';
		echo '<tr>';
		echo '<th scope="row">' . __( 'Debug Spinners', 'afcd-core-development' ) . '</th>';
		echo '<td>';
		echo '<input type="checkbox" id="afcd_debug_spinner" name="afcd_debug_spinner" value="1" ' . checked( 1, $debug_spinner_enabled, false ) . ' />';
		echo '<label for="afcd_debug_spinner">' . __( 'Enable Debug Spinners', 'afcd-core-development' ) . '</label>';
		echo '<p>' . __( 'Attempts to make all the loading spinners in the classic admin pages visible for debugging purposes.', 'afcd-core-development' ) . '</p>';
		echo '<span class="afcd-test-spinner-container">Test spinner: <span class="spinner afcd-test-spinner"></span></span>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		submit_button();
		echo '</form>';

		echo '</div>';
	}
}
