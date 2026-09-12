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
	 * Loads the integrations.
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		$this->load_integrations();
	}

	/**
	 * Auto-discovers and initializes all integrations.
	 *
	 * Scans the includes directory for PHP files that define a class
	 * implementing AFCD_Integration_Interface, includes them, and calls
	 * register_hooks() on each.
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 */
	private function load_integrations() {
		$includes_dir = __DIR__;
		$files        = glob( $includes_dir . '/class-afcd-*.php' );

		if ( ! $files ) {
			return;
		}

		foreach ( $files as $file ) {
			// Skip the interface file itself.
			if ( false === strpos( $file, 'interface' ) ) {
				// phpcs:ignore WordPress.Files.IncludingFiles -- Auto-discovery of integration files.
				require_once $file;
			}
		}

		// Now that all classes are loaded, find and instantiate the integrations.
		$integrations = $this->discover_integrations();

		foreach ( $integrations as $class_name ) {
			$integration = new $class_name();
			$integration->register_hooks();
		}
	}

	/**
	 * Discovers all classes implementing AFCD_Integration_Interface.
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @return string[] Array of class names implementing the interface.
	 */
	private function discover_integrations() {
		$integrations = array();

		foreach ( get_declared_classes() as $class ) {
			if ( in_array( 'AFCD_Integration_Interface', class_implements( $class ), true ) ) {
				$integrations[] = $class;
			}
		}

		return $integrations;
	}
}
