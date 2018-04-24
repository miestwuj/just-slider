<?php
/**
 * Front end display object.
 *
 * @package    Just_Slider
 */

/**
 * Frontend display.
 */
class Just_Slider_Display {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @param int $slider_id  The slug of the slider.
	 */
	public function __construct( $slider_id ) {
	}

	// mtodo - move this shit to embeded and output with the slider on the spot. make sure this is done only once (static func?).
	/**
	 * Enqueue the assets.
	 */
	public function get_assets() {
		wp_enqueue_style( 'just-slider', JUST_SLIDER_URL . 'assets/style.min.css', array(), Just_Slider::get_version(), 'screen' );
		if ( defined( 'JUST_SLIDER_DEV_MODE' ) && JUST_SLIDER_DEV_MODE ) {
			wp_enqueue_script( 'just-slider', JUST_SLIDER_URL . 'assets/src/main.js', array( 'jquery' ), false, true );
		} else {
			wp_enqueue_script( 'just-slider', JUST_SLIDER_URL . 'assets/js/main.min.js', array( 'jquery' ), false, true );
		}
	}

}
