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
		add_action( 'add_meta_boxes',           array( $this, 'add_meta_box' ), 10, 2 );
		add_action( 'wp_ajax_just_slider_save', array( $this, 'save_ajax' ) );
		add_action( 'save_post',  array( $this, 'save_metabox' ) );
	}

	/**
	 * Register metabox
	 *
	 * @param string  $post_type    Post type.
	 * @param WP_Post $post         Post object.
	 */
	public function add_meta_box( $post_type, $post ) {
		if ( 'just_slider' !== $post_type ) {
			return;
		}
		add_meta_box(
			'just_slider_settings',
			__( 'Slider', 'bimber' ),
			array( $this, 'render_just_slider_settings' ),
			$post_type,
			'normal'
		);
	}

	/**
	 * Enqueue the assets.
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'just-slider-admin', JUST_SLIDER_URL . 'assets/admin.min.css', array(), Just_Slider::get_version(), 'screen' );
		wp_enqueue_media();
		if ( defined( 'JUST_SLIDER_DEV_MODE' ) && JUST_SLIDER_DEV_MODE ) {
			wp_enqueue_script( 'just-slider-admin', JUST_SLIDER_URL . 'assets/src/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), false, true );
		} else {
			wp_enqueue_script( 'just-slider-admin', JUST_SLIDER_URL . 'assets/js/admin.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), false, true );
		}
		$config = array(
			'template' =>	 $this->get_slide_markup( 'put your code here', 1000, '' ),
		);
		wp_localize_script( 'just-slider-admin','justsliderConfig', wp_json_encode( $config ) );
	}

	/**
	 * Get markup for single slide
	 *
	 * @param  string $content   Slide content.
	 * @param  int    $time		 Slide time.
	 * @return string
	 */
	private function get_slide_markup( $content, $time, $image ) {
		ob_start();?>
			<div class="jslider-slide">
				<div class="jslide-slide-draggable"></div>
				<div class="just-slider-image-upload">
					<a class="button button-secondary just-slider-add-image" href="#"><?php esc_html_e( 'Add image', 'just-slider' ); ?></a>

					<div class="just-slider-image">
						<?php if ( ! empty( $image ) ) :  ?>
							<?php echo wp_get_attachment_image( $image ); ?>
						<?php endif; ?>
					</div>
					<a class="button button-secondary just-slider-delete-image" href="#"><?php esc_html_e( 'Remove image', 'just-slider' ); ?></a>
					<input class="just-slider-image-id" type="hidden" value="<?php echo $image ; ?>" />
				</div>
				<div class="just-slider-textarea">
					<textarea class="jslider-slide-content"><?php echo ( $content );?></textarea>
				</div>
				<label>
					<?php echo esc_html__( 'Transition time (ms)' );?>
					<input type="number" class="jslider-transition-time" value="<?php echo esc_attr( $time );?>">
				</label>
				<button class="jslider-sliders-remove"><?php echo esc_html__( 'Delete' );?></button>
			</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Get defaults
	 *
	 * @return array
	 */
	private function get_default(){
		return array(
			'transitionType' => 'slide',
			'autoplay' => 'yes',
			'slides' => array(
				array(
					'content' => esc_html__( 'put your code here' ),
					'time' => 3000,
					'image' => '',
				),
			)
		);
	}

	private function get_helpers(){
		return '';
	}

	/**
	 * Render the admin panel.
	 *
 	 * @param WP_Post $post         Post object.
	 */
	public function render_just_slider_settings( $post ) {
		$values = get_post_meta( $post->ID, '_just_slider_values', true );
		if ( ! $values ) {
			$values = $this->get_default();
		} else {
			$values = json_decode( $values, true );
		}

		?>
			<div class="jslider-slides-list-wrapper">
				<div class="jslider-slides-list-header">
					<a class=" button jslider-sliders-add"><?php echo esc_html__( 'Add new slide' );?></a>
					<label>
						<?php echo esc_html__( 'Transition type' );?>
						<select class="jslider-transition-type">
							<option value="slide" <?php selected( 'slide', $values['transitionType'], true);?>><?php echo esc_html__( 'Slide' );?></option>
							<option value="fade" <?php selected( 'fade', $values['transitionType'], true);?>><?php echo esc_html__( 'Fade' );?></option>
						</select>
					</label>
					<label>
						<?php echo esc_html__( 'Autoplay' );?>
						<select class="jslider-autoplay">
							<option value="yes" <?php selected( 'yes', $values['autoplay'], true);?>><?php echo esc_html__( 'Yes' );?></option>
							<option value="no" <?php selected( 'no', $values['autoplay'], true);?>><?php echo esc_html__( 'No' );?></option>
						</select>
					</label>
				</div>
				<div class="jslider-slides-list">
					<?php
						foreach ( $values['slides'] as $slide ) {
							echo $this->get_slide_markup( $slide['content'], $slide['time'], $slide['image'] );
						}
					?>
				</div>
			</div>
			<input id="just-slider-settings" name="just-slider-settings" type="hidden" value="">
			<?php wp_nonce_field( 'just-slider-save-nonce', 'just-slider-save-nonce' );?>
		<?php
	}

	/**
	 * Save metabox data
	 *
	 * @param int $post_id      Post id.
	 *
	 * @return mixed
	 */
	public function save_metabox( $post_id ) {
		// Security.
		$nonce = filter_input( INPUT_POST, 'just-slider-save-nonce', FILTER_SANITIZE_STRING );
		if ( ! $nonce ) {
			return $post_id;
		}
		$post_type = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_STRING );
		$post_type_obj = get_post_type_object( $post_type );
		if ( ! current_user_can( $post_type_obj->cap->edit_post, $post_id ) ) {
			return $post_id;
		}
		if ( ! check_admin_referer( 'just-slider-save-nonce', 'just-slider-save-nonce' ) ) {
			wp_die( esc_html__( 'Nonce incorrect!', 'bimber' ) );
		}
		$values = filter_input( INPUT_POST, 'just-slider-settings', FILTER_SANITIZE_STRING );
		$values = html_entity_decode( $values );
		update_post_meta( $post_id, '_just_slider_values', $values );

		return $post_id;
	}
}
