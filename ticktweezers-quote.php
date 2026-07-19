<?php
/**
 * Plugin Name:       TickTweezers Quote Configurator
 * Plugin URI:        https://ticktweezers.com
 * Description:       Custom 4-step AJAX quote/sample request configurator for TickTweezers products. Replaces the Elementor-based flow with a standalone, admin-configurable plugin. Use shortcode [ticktweezers_quote].
 * Version:           1.0.0
 * Author:            Anik
 * Text Domain:       ttq
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 5.8
 */

// Block direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TTQ_VERSION', '1.0.0' );
define( 'TTQ_FILE', __FILE__ );
define( 'TTQ_PATH', plugin_dir_path( __FILE__ ) );
define( 'TTQ_URL', plugin_dir_url( __FILE__ ) );
define( 'TTQ_UPLOAD_SUBDIR', 'ttq-logos' );

/**
 * Autoload plugin classes.
 */
spl_autoload_register(
	function ( $class ) {
		if ( strpos( $class, 'TTQ_' ) !== 0 ) {
			return;
		}
		$file_name = 'class-' . strtolower( str_replace( '_', '-', substr( $class, 4 ) ) ) . '.php';
		$path      = TTQ_PATH . 'includes/' . $file_name;
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);

/**
 * Activation: create upload dir + default options.
 */
function ttq_activate() {
	$upload_dir = wp_upload_dir();
	$target     = trailingslashit( $upload_dir['basedir'] ) . TTQ_UPLOAD_SUBDIR;
	if ( ! file_exists( $target ) ) {
		wp_mkdir_p( $target );
	}
	// Protect the folder: no directory listing / no PHP execution.
	if ( ! file_exists( $target . '/index.php' ) ) {
		file_put_contents( $target . '/index.php', "<?php\n// Silence is golden.\n" );
	}
	if ( ! file_exists( $target . '/.htaccess' ) ) {
		file_put_contents( $target . '/.htaccess', "php_flag engine off\nDeny from all\n" );
	}

	if ( false === get_option( 'ttq_settings' ) ) {
		add_option( 'ttq_settings', TTQ_Admin::default_settings() );
	}
}
register_activation_hook( __FILE__, 'ttq_activate' );

/**
 * Boot the plugin.
 */
function ttq_run() {
	TTQ_Plugin::instance();
}
add_action( 'plugins_loaded', 'ttq_run' );
