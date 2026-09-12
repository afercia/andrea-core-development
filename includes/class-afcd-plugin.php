<?php
/**
 * The core Andrea Core Development plugin class.
 *
 * @since      1.0.1
 * @package    AFCD_Core_Development
 * @subpackage AFCD_Core_Development/includes
 * @author     Andrea Fercia
 */
class AFCD_Plugin {

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Loads the required dependencies for this plugin.
	 *
	 * @since 1.0.1
	 * @access private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-afcd-debug-spinner.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-afcd-admin-page.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-afcd-mailpit.php';
	}

	/**
	 * Registers all of the hooks related to the admin area.
	 *
	 * @since  1.0.1
	 * @access private
	 */
	private function define_admin_hooks() {
		new AFCD_Admin_Page();
		new AFCD_Debug_Spinner();
		new AFCD_Mailpit();
	}
}
