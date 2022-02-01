<?php
/**
 * Plugin Name: Taro Events
 * Plugin URI: https://github.com/tarosky/taro-events
 * Description: Add events feature to your WordPress site.
 * Author: Tarosky INC.
 * Version: 1.0.3
 * Author URI: https://tarosky.co.jp/
 * License: GPL3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: taro-events
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();

/**
 * Init plugins.
 */
function taro_events_init() {
	// Register translations.
	load_plugin_textdomain( 'taro-events', false, basename( __DIR__ ) . '/languages' );
	// Load functions.
	require_once __DIR__ . '/includes/functions.php';
	// Require Bootstrap.
	$autoload = __DIR__ . '/vendor/autoload.php';
	if ( ! file_exists( $autoload ) ) {
		trigger_error( __( 'Autoloader is missing. Did you ran composer install?', 'taro-events' ), E_USER_WARNING );
	} else {
		require $autoload;
		\Tarosky\Events\Bootstrap::get_instance();
	}
}

/**
 * Get plugin base URL.
 *
 * @return string
 */
function taro_events_url() {
	return untrailingslashit( plugin_dir_url( __FILE__ ) );
}

/**
 * Get directory path.
 *
 * @return string
 */
function taro_events_dir() {
	return __DIR__;
}

/**
 * Get version.
 *
 * @return string
 */
function taro_events_version() {
	static $version = null;
	if ( is_null( $version ) ) {
		$data    = get_file_data( __FILE__, [
			'version' => 'Version',
		] );
		$version = $data['version'];
	}

	return $version;
}

/**
 * Get template dir
 *
 * @param string $file
 *
 * @return string
 */
function taro_events_template( $file ) {
	$template_path = trailingslashit( __DIR__ . '/templates' ) . ltrim( $file, '/' );
	if ( file_exists( $template_path ) ) {
		return $template_path;
	} else {
		return '';
	}
}

// Register hooks.
add_action( 'plugins_loaded', 'taro_events_init' );
