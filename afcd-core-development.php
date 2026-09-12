<?php
/**
 * Andrea WP Core Development
 *
 * Plugin Name: Andrea WP Core Development
 * Plugin URI:  https://github.com/afercia/andrea-core-development
 * Description: Adjusts a few things to provide a better WordPress Core development experience.
 * Version:     1.0.1
 * Author:      Andrea Fercia
 * Author URI:  https://profiles.wordpress.org/afercia
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: classic-editor
 * Requires at least: 7.0
 * Requires PHP: 8.0.0
*/

require_once __DIR__ . '/vendor/autoload.php';

// Removes the "update now" notice from the admin dashboard.
function afcd_remove_actions() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}

add_action( 'admin_init', 'afcd_remove_actions', PHP_INT_MAX );


/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Adds `dump()` and `dd()` function debugging helpers to the global namespace.
 */

use Symfony\Component\VarDumper\VarDumper;

if ( ! function_exists( 'dump' ) ) {
	/**
	 * @author Nicolas Grekas <p@tchwork.com>
	 */
	function dump( $some_var, ...$more_vars ) {
		VarDumper::dump( $some_var );

		foreach ( $more_vars as $v ) {
			VarDumper::dump( $v );
		}

		if ( 1 < func_num_args() ) {
			return func_get_args();
		}

		return $some_var;
	}
}

if ( ! function_exists( 'dd' ) ) {
	function dd( ...$vars ) {
		foreach ( $vars as $v ) {
			VarDumper::dump( $v );
		}

		exit( 1 );
	}
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Initialize plugin
new AFCD_Plugin();
