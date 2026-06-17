<?php
/**
 * Plugin Name:       PowerPlus — All-in-One Powerful Toolkit
 * Plugin URI:        https://wordpress.org/plugins/powerplus-toolkit/
 * Description:       All-in-One Powerful Toolkit — custom login pages, page duplicator, security hardening, admin branding, and more.
 * Version:           3.10.3
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Saddam Hussain Safi
 * Author URI:        https://saddamhussain.com.np/
 * Text Domain:       powerplus-toolkit
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Elementor tested up to: 3.25
 * Elementor Pro tested up to: 3.25
 *
 * @package PKWT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PKWT_VERSION' ) ) {
	define( 'PKWT_VERSION', '3.10.3' );
}
if ( ! defined( 'PKWT_PLUGIN_FILE' ) ) {
	define( 'PKWT_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'PKWT_PLUGIN_DIR' ) ) {
	define( 'PKWT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'PKWT_PLUGIN_URL' ) ) {
	define( 'PKWT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'PKWT_PLUGIN_SLUG' ) ) {
	define( 'PKWT_PLUGIN_SLUG', 'powerplus-toolkit' );
}
if ( ! defined( 'PKWT_MIN_PHP' ) ) {
	define( 'PKWT_MIN_PHP', '8.0' );
}
if ( ! defined( 'PKWT_MIN_WP' ) ) {
	define( 'PKWT_MIN_WP', '6.0' );
}
if ( ! defined( 'PKWT_MIN_ELEMENTOR' ) ) {
	define( 'PKWT_MIN_ELEMENTOR', '3.5.0' );
}
if ( ! defined( 'PKWT_FILTER_PRIORITY' ) ) {
	define( 'PKWT_FILTER_PRIORITY', 20 );
}
if ( ! defined( 'PKWT_SETTINGS_VERSION' ) ) {
	define( 'PKWT_SETTINGS_VERSION', PKWT_VERSION );
}

spl_autoload_register(
	static function ( $class ) {
		if ( 0 !== strpos( $class, 'PKWT\\' ) ) {
			return;
		}

		// Strip namespace prefix and convert underscores to hyphens, then lowercase.
		$relative = strtolower( str_replace( array( 'PKWT\\', '_' ), array( '', '-' ), $class ) );
		$relative = str_replace( '\\', '/', $relative );

		// Map PKWT class file segments back to file naming conventions.
		// Class_PKWT_DPP_* classes map to class-dpp-* files.
		// Class_PKWT_* classes map to class-pkwt-* files.
		$filename = basename( $relative );
		$dirname  = dirname( $relative );

		if ( 0 === strpos( $filename, 'class-pkwt-dpp-' ) ) {
			// e.g. class-pkwt-dpp-ghost -> class-dpp-ghost
			$filename = str_replace( 'class-pkwt-dpp-', 'class-dpp-', $filename );
		}
		// All other class-pkwt-* files map directly (no renaming needed).

		$relative = ( '.' !== $dirname ? $dirname . '/' : '' ) . $filename;
		$file     = PKWT_PLUGIN_DIR . $relative . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

register_activation_hook( PKWT_PLUGIN_FILE, array( 'PKWT\\Includes\\Class_PKWT_Activator', 'activate' ) );
register_deactivation_hook( PKWT_PLUGIN_FILE, array( 'PKWT\\Includes\\Class_PKWT_Deactivator', 'deactivate' ) );

if ( file_exists( PKWT_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once PKWT_PLUGIN_DIR . 'vendor/autoload.php';
}

add_action(
	'plugins_loaded',
	static function () {
		if ( ! class_exists( 'PKWT\\Includes\\Class_PKWT_Plugin' ) ) {
			return;
		}
		PKWT\Includes\Class_PKWT_Plugin::instance()->boot();
	}
);

