<?php

/**
 * The debug spinner functionality.
 * @since      1.0.1
 * @package    Andrea_Core_Development
 * @subpackage Andrea_Core_Development/includes
 * @author     Andrea Fercia
 */
class AFCD_Debug_Spinner {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_filter( 'admin_body_class', array( $this, 'add_debug_spinner_class' ) );
	}

	/**
	 * Enqueues admin styles.
	 *
	 * @since 1.0.1
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style(
			'afcd-debug-spinner',
			plugin_dir_url( __FILE__ ) . '../assets/css/admin-debug-spinner.css',
			array(),
			'1.0.1',
			'all'
		);
	}

	/**
	 * Adds debug spinner class to admin body.
	 *
	 * @since 1.0.1
	 *
	 * @param string $classes Existing body classes.
	 * @return string Modified body classes.
	 */
	public function add_debug_spinner_class( $classes ) {
		// Make sure we're in admin area
		if ( ! is_admin() ) {
			return $classes;
		}

		$current_user_id = get_current_user_id();

		// Debug: Check if user ID is valid.
		if ( 0 === $current_user_id ) {
			return $classes;
		}

		// Get the setting - make sure we're getting the correct value.
		$debug_spinner_enabled = get_user_meta( $current_user_id, 'afcd_debug_spinner', true );

		// If setting is enabled (truthy), add the class.
		if ( $debug_spinner_enabled ) {
			$classes .= ' afcd-debug-spinners';
		}

		return $classes;
	}
}
