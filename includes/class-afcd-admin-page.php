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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_page_styles' ) );
	}

	public function create_admin_menu() {
		$admin_page_slug       = 'afcd-core-development';
		$admin_page_title      = __( 'Core Dev Tools', 'afcd-core-development' );
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
		echo '<h1>Core Dev Tools</h1>';

		echo '<div class="afcd-measure">';

		echo '<p>' . __( 'This plugin provides various core development tools.', 'afcd-core-development' ) . '</p>';

		echo '<h2>dump() and dd()</h2>';

		echo '<p>' . __( 'By default, two helper functions from the Symfony VarDumper Component are available: <code>dump()</code> and <code>dd()</code>. They provide a highly legible alternative to PHP&#8216;s built-in <code>var_dump()</code> or <code>print_r()</code> functions.', 'afcd-core-development' ) . '</p>';

		echo '<ul class="ul-disc">';
		echo '<li><code>dump()</code>: ' . __( 'Outputs a variable in a readable format. If you dump a complex object or nested array, it gives you an expandable tree that you can click through to inspect properties.', 'afcd-core-development' ) . '</li>';
		echo '<li><code>dd()</code> (Dump and Die): ' . __( 'Identical in formatting to <code>dump()</code>, but it immediately terminates the script execution right after printing the data.', 'afcd-core-development' ) . '</li>';
		echo '</ul>';

		echo '</div>'; // Close afcd-measure div.

		echo '<p>' . __( 'Example:', 'afcd-core-development' ) . '</p>';

		global $wp_roles;
		dump( $wp_roles );

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

		echo '<h2 class="mt-2">' . __( 'Other tools', 'afcd-core-development' ) . '</h2>';

		echo '<form method="post">';
		wp_nonce_field( 'afcd_nonce', 'afcd_nonce' );

		echo '<table class="form-table">';
		echo '<tr>';
		echo '<th scope="row">' . __( 'Debug Spinners', 'afcd-core-development' ) . '</th>';
		echo '<td>';
		echo '<input type="checkbox" id="afcd_debug_spinner" name="afcd_debug_spinner" value="1" ' . checked( 1, $debug_spinner_enabled, false ) . ' />';
		echo '<label for="afcd_debug_spinner">' . __( 'Enable Debug Spinners', 'afcd-core-development' ) . '</label>';
		echo '<p>' . __( 'Attempts to make visible all the loading spinners in the classic admin pages for debugging purposes.', 'afcd-core-development' ) . '</p>';
		echo '<span class="afcd-test-spinner-container">Test spinner: <span class="spinner afcd-test-spinner"></span></span>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		submit_button();
		echo '</form>';

		echo '</div>'; // Close afcd-admin-page wrap.
	}

	/**
	 * Enqueues the admin styles.
	 *
	 * @since 1.0.1
	 */
	public function enqueue_admin_page_styles() {
		global $hook_suffix;
		if ( 'toplevel_page_afcd-core-development' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'afcd-admin',
			plugin_dir_url( __FILE__ ) . '../assets/css/admin-page.css',
			array(),
			'1.0.1'
		);
	}
}
