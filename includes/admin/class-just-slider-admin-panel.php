<?php
/**
 * Admin panel.
 *
 * @package    Just_Slider
 */

/**
 * Admin panel.
 */
class Just_Slider_Admin_Panel {

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Setup the admin panel hooks.
	 */
	public function setup_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_menu',           array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Register menu in admin area
	 */
	public function add_admin_menu() {
		$capability = is_multisite() ? 'manage_network_options' : 'manage_options';
		add_options_page(
			__( 'Just Slider', 'just-slider' ),
			__( 'Just Slider', 'just-slider' ),
			$capability,
			'just_slider-general-settings',
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue the assets.
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'just-slider-admin', JUST_SLIDER_URL . 'assets/admin.min.css', array(), Just_Slider::get_version(), 'screen' );
		if ( defined( 'JUST_SLIDER_DEV_MODE' ) && JUST_SLIDER_DEV_MODE ) {
			wp_enqueue_script( 'just-slider-admin', JUST_SLIDER_URL . 'assets/src/admin.js', array( 'jquery' ), false, true );
		} else {
			wp_enqueue_script( 'just-slider-admin', JUST_SLIDER_URL . 'assets/js/admin.min.js', array( 'jquery' ), false, true );
		}
	}

	/**
	 * Render the admin panel.
	 */
	public function render() {

	}

}
