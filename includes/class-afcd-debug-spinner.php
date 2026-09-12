<?php
/**
 * The Debug Spinners functionality.
 *
 * @since      1.0.1
 * @package    AFCD_Core_Development
 * @subpackage AFCD_Core_Development/includes
 * @author     Andrea Fercia
 */
class AFCD_Debug_Spinner implements AFCD_Integration_Interface {

	/**
	 * Registers the hooks for this integration.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_filter( 'admin_body_class', array( $this, 'add_debug_spinner_class' ) );

		// The Customizer does not use the admin_body_class filter.
		// Use an action instead.
		add_action( 'customize_controls_head', array( $this, 'add_debug_spinner_class_to_customizer' ) );
	}

	/**
	 * Enqueues the admin styles.
	 *
	 * @since 1.0.1
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style(
			'afcd-debug-spinner',
			plugin_dir_url( __FILE__ ) . '../assets/css/admin-debug-spinner.css',
			array(),
			'1.0.1'
		);
	}

	/**
	 * Adds the debug spinner class to the admin body.
	 *
	 * @since 1.0.1
	 *
	 * @param string $classes Existing body classes.
	 * @return string Modified body classes.
	 */
	public function add_debug_spinner_class( $classes ) {
		// Make sure we're in admin area.
		if ( ! is_admin() ) {
			return $classes;
		}

		$debug_spinner_enabled = $this->is_debug_spinner_enabled();

		if ( $debug_spinner_enabled ) {
			$classes .= ' afcd-debug-spinners';
		}

		return $classes;
	}

	/**
	 * Adds debug spinner class to the Customizer.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function add_debug_spinner_class_to_customizer() {
		$debug_spinner_enabled = $this->is_debug_spinner_enabled();

		if ( $debug_spinner_enabled ) {
			echo '<script>document.addEventListener( "DOMContentLoaded", function() { document.body.classList.add( "afcd-debug-spinners" ); } );</script>';
		}
	}

	/**
	 * Checks whether the Debug Spinners user setting is enabled for the current user.
	 *
	 * @since 1.0.1
	 *
	 * @return bool Whether the Debug Spinners setting is enabled.
	 */
	public function is_debug_spinner_enabled() {
		$current_user_id = get_current_user_id();

		if ( 0 === $current_user_id ) {
			return false;
		}

		$debug_spinner_enabled = false;

		global $hook_suffix;

		// If we are in the AFCD plugin's admin page and a form submission has
		// occurred, use the submitted value.
		if (
			'toplevel_page_afcd-core-development' === $hook_suffix && ! empty( $_POST )
		) {
			$debug_spinner_enabled =
				isset( $_POST['afcd_debug_spinner'] ) && 1 === (int) $_POST['afcd_debug_spinner'];
		} else {
			// On all other pages, retrieve the setting from the current user meta.
			$debug_spinner_enabled = get_user_meta( $current_user_id, 'afcd_debug_spinner', true );
		}

		return (bool) $debug_spinner_enabled;
	}
}
