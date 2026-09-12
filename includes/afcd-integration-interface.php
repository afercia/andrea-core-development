<?php
/**
 * The AFCD Integration Interface.
 *
 * All AFCD integrations must implement this interface.
 * This enables auto-discovery and auto-initialization of all integrations.
 *
 * @since      1.1.0
 * @package    AFCD_Core_Development
 * @subpackage AFCD_Core_Development/includes
 * @author     Andrea Fercia
 */

/**
 * Interface for AFCD integrations.
 */
interface AFCD_Integration_Interface {

	/**
	 * Registers the hooks for this integration.
	 *
	 * This is called during plugin initialization.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_hooks();
}
