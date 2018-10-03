<?php
/**
 * Shortcodes to inject the slider.
 *
 * @package    Just_Slider
 */

class Just_Slider_Shortcode {

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->setup_shortcode();
	}

	/**
	 * Setup the shortcode.
	 */
	public function setup_shortcode() {
        add_shortcode( 'just_slider', array( $this, 'render' ) );
	}

    /**
     * Render.
     *
     * @param  array $atts			Shortcode attributes.
     * @return string
     */
    public function render( $atts ) {
        $default_atts = array(
            'id' => false,
        );

        $atts = shortcode_atts( $default_atts, $atts, 'just_slider_shortcode' );

        ob_start();
        if ( $atts['id'] ) {
            just_slider( $atts['id'] );
        }

        $html = ob_get_clean();
        return $html;
    }
}
