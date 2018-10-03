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
		Just_Slider::get_instance()->get_template_part( 'slider' );
		$this->render_script( );
	}

	/**
	 * Render script.
	 */
	private function render_script() {
		?>
			<script>
				(function ($) {

				'use strict';

				$(document).ready(function(){
					$('.jslider-<?php echo esc_attr( $this->id );?> .jslider-items').slick({
						<?php if( 'yes' === $this->slider_data['autoplay'] ) :?>
						autoplay: true,
						autoplaySpeed: <?php echo esc_attr( $this->slider_data['time'] );?>,
						<?php endif;?>
						<?php if( 'fade' === $this->slider_data['transitionType'] ) :?>
						fade: true,
						<?php endif;?>
						  infinite: true,
						  speed: 500,
						  adaptiveHeight: true,
					});
				});

				})(jQuery);
			</script>
		<?php
	}

}
