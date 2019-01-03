<?php
/**
 * Front end display object.
 *
 * @package    Just_Slider
 */
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Frontend display.
 */
class Just_Slider_Display {

	/**
	 * The slider data.
	 *
	 * @var array
	 */
	private $slider_data;

	/**
	 * The slider id.
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @param int $slider_id  The id of the slider.
	 */
	public function __construct( $slider_id ) {
		$data = get_post_meta( $slider_id, '_just_slider_values', true );
		$data = json_decode( $data, true );
		if ( ! $data ) {
			return;
		}
		$this->slider_data = $data;
		$this->id = $slider_id;
		$this->render();
	}

	/**
	 * Render.
	 */
	private function render() {
		global $jslider_slides;
		global $jslider_id;
		global $jslider_height;
		$jslider_slides = $this->slider_data['slides'];
		$jslider_id = $this->id;
		$jslider_height = $this->slider_data['height'];
		if ( ! $jslider_slides || 0 === count( $jslider_slides ) ) {
			return;
		}
		ob_start();
		if ( 'crop' === $this->slider_data['scaling'] ) {
			Just_Slider::get_instance()->get_template_part( 'slider' );
		} else {
			Just_Slider::get_instance()->get_template_part( 'slider-proportional' );
		}
		$html = ob_get_clean();
		/**
		 * Filter slider HTML.
		 */
		echo apply_filters( 'just_slider_html', $html, $this->id );
		$this->render_script( );
	}

	/**
	 * Render script.
	 */
	private function render_script() {
		$params = array(
			'infinite' => true,
			'speed' => 500,
			'adaptiveHeight' => true,
		);
		if( 'yes' === $this->slider_data['autoplay'] ){
			$params['autoplay'] = true;
			$params['autoplaySpeed'] = $this->slider_data['time'];
		}
		if( 'fade' === $this->slider_data['transitionType'] ) {
			$params['fade'] = true;
		}
		/**
		 * Filter slick script params.
		 */
		$params = apply_filters( 'just_slider_script_parameters', $params, $this->id );
		ob_start();
		?>
			<script>
				(function ($) {

				'use strict';

				$(document).ready(function(){
					var atts = <?php echo json_encode( $params ); ?>;
					$('.jslider-<?php echo esc_attr( $this->id );?> .jslider-items').slick(atts);
				});

				})(jQuery);
			</script>
		<?php
		$html = ob_get_clean();
		echo apply_filters( 'just_slider_script', $html, $this->id );
	}

}
