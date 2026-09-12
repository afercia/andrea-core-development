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
		$this->init_plugin_classes();
	}
	/**
	 * Initializes the plugin by creating instances of the core classes.
	 *
	 * @since  1.0.1
	 * @access private
	 */
	private function init_plugin_classes() {
		new AFCD_Admin_Page();
		new AFCD_Debug_Spinner();
		new AFCD_Mailpit();
	}
}
