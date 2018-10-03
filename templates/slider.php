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
<style>
	.jslider-<?php echo esc_attr( $jslider_id );?>,
	.jslider-<?php echo esc_attr( $jslider_id );?> .jslider-item{
		height:<?php echo esc_attr( $jslider_height );?>px;
	}
</style>
<div class="jslider-wrapper jslider-<?php echo esc_attr( $jslider_id );?>">
	<ul class="jslider-items">
		<?php foreach ( $jslider_slides as $index => $slide ) : ?>
			<li class="jslider-item jslider-item-<?php echo esc_attr( $index );?>">
				<?php if ( $slide['image'] ) :
					$attachement_data = wp_get_attachment_image_src( $slide['image'], 'full' );
				?>
				<style>
					.jslider-item-<?php echo esc_attr( $index );?>{
						background-image:url(<?php echo esc_url( $attachement_data[0] ); ?>);
					}
				</style>
				<div class="jslider-content"><?php echo $slide['content'];?></div>
				<?php endif;?>
			</li>
		<?php endforeach;?>
	</ul>
</div>
