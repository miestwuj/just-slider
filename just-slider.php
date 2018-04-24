<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/miestwuj
 * @since             1.0.0
 * @package           Just_Slider
 *
 * @wordpress-plugin
 * Plugin Name:       Just Slider
 * Plugin URI:        https://github.com/thepluginurl/
 * Description:       When you just want a slider and you want to avoid a revolution ;)
 * Version:           1.0.0
 * Author:            Mateusz Dorywalski
 * Author URI:        https://github.com/miestwuj
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       just-slider
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * CONSTANTS.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );
define( 'JUST_SLIDER_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'JUST_SLIDER_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * The core plugin class.
 */
class Just_Slider {

	/**
	 * The Just_Slider object instance
	 *
	 * @var Just_Slider
	 */
	private static $instance;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		if ( is_admin() ) {
			require_once JUST_SLIDER_PATH . 'includes/admin/ajax.php';
			require_once JUST_SLIDER_PATH . 'includes/admin/class-just-slider-admin-panel.php';
		} else {
			require_once JUST_SLIDER_PATH . 'includes/front/class-just-slider-display.php';
			require_once JUST_SLIDER_PATH . 'includes/front/functions.php';
			require_once JUST_SLIDER_PATH . 'includes/front/shortcodes.php';
		}
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Load textdomain
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'just-slider',
			false,
			dirname( JUST_SLIDER_PATH ) . 'languages/'
		);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		if ( is_admin() ) {
			new Just_Slider_Admin_Panel();
		} else {
			new Just_Slider_Display();
			// mtodo register shortcode here!
		}
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	static public function get_plugin_name() {
		return 'just-slider';
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	static public function get_version() {
		return PLUGIN_NAME_VERSION;
	}

	/**
	 * Return the only existing instance of Snax object
	 *
	 * @return Just_Slider
	 */
	static public function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Just_Slider();
		}
		return self::$instance;
	}

	/**
	 * Load a template part, child and parent theme compatible.
	 *
	 * @param string $slug The slug name for the generic template.
	 */
	static public function get_template_part( $slug ) {
		$slug = ltrim( $slug, '/' );

		if ( empty( $slug ) ) {
			return;
		}

		$files = array(
			trailingslashit( get_template_directory() ) . 'just-slider/' . $slug . '.php',
			trailingslashit( get_stylesheet_directory() ) . 'just-slider/' . $slug . '.php',
			JUST_SLIDER_PATH . 'templates/' . $slug . '.php',
		);

		foreach ( $files as $file ) {
			if ( empty( $file ) ) {
				continue;
			}
			if ( file_exists( $file ) ) {
				$located = $file;
				break;
			}
		}
		if ( strlen( $located ) ) {
			load_template( $located, false );
		}
	}

}

/**
 * Begins execution of the plugin.
 */
function run_just_slider() {

	$plugin = Just_Slider::get_instance();
	$plugin->run();

}
run_just_slider();
