<?php
/**
 * Template for the slider.
 *
 * @package    Just_Slider
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
global $jslider_slides;
global $jslider_id;
global $jslider_height;
?>

<div class="jslider-wrapper jslider-<?php echo esc_attr( $jslider_id );?>">
	<ul class="jslider-items">
		<?php foreach ( $jslider_slides as $index => $slide ) : ?>
			<li class="jslider-item jslider-item-<?php echo esc_attr( $index );?>">
				<?php if ( $slide['image'] ) :
					$attachement_data = wp_get_attachment_image_src( $slide['image'], 'full' );
				?>

				<div class="jslider-content"><?php echo urldecode( $slide['content'] );?></div>
				<img class="jslider-slide-image" alt="slider image" src="<?php echo esc_url( $attachement_data[0] ); ?>">
				<?php endif;?>
			</li>
		<?php endforeach;?>
	</ul>
</div>
