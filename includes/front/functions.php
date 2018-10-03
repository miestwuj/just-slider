<?php
/**
 * Public, global PHP functions.
 *
 * @package    Just_Slider
 */

/**
 * Display the slider.
 *
 * @param int $slider_id  The slug of the slider.
 */
function just_slider( $slider_id ) {
	$instance = new Just_Slider_Display( $slider_id );
}
