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
		add_filter( 'manage_just_slider_posts_columns',         array( $this, 'admin_columns' ), 10, 1 );
		add_action( 'manage_just_slider_posts_custom_column',   array( $this, 'admin_columns_data' ), 10, 2 );
	}

	/**
	 * Register custom column headers
	 *
	 * @param array $columns    List of columns.
	 *
	 * @return mixed            Modified colum list.
	 */
	public function admin_columns( $columns ) {
		if ( ! isset( $columns['slider_shortcode'] ) ) {
			$columns['slider_shortcode'] = esc_html__( 'Shortcode', 'just-slider' );
		}
		if ( ! isset( $columns['slider_php'] ) ) {
			$columns['slider_php'] = esc_html__( 'PHP function', 'just-slider' );
		}
		return $columns;
	}

	/**
	 * Render custom column value
	 *
	 * @param string $column         Column name.
	 * @param int    $post_id        Post id.
	 */
	public function admin_columns_data( $column, $post_id ) {
		$screen = get_current_screen();
		if ( ! is_object( $screen ) ) {
			return;
		}

		if ( 'slider_shortcode' === $column && 'just_slider' === $screen->post_type ) {?>
			<input class="widefat" readonly type="text" value="<?php echo esc_html( "[just_slider id='" . $post_id . "']" );?>" onclick="this.focus(); this.select()"><?php
		}
		if ( 'slider_php' === $column && 'just_slider' === $screen->post_type ) {?>
			<input class="widefat" readonly type="text" value="<?php echo esc_html( "<?php just_slider( " . $post_id . " ); ?>" );?>" onclick="this.focus(); this.select()"><?php
		}
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
			__( 'Slider', 'just-slider' ),
			array( $this, 'render_just_slider_settings' ),
			$post_type,
			'normal'
		);
		add_meta_box(
			'just_slider_classes',
			__( 'Adddional classes', 'just-slider' ),
			array( $this, 'render_just_slider_classes' ),
			$post_type,
			'side'
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
			'template' =>	 $this->get_slide_markup( 'put your code here', '' ),
		);
		wp_localize_script( 'just-slider-admin','justsliderConfig', wp_json_encode( $config ) );
	}

	/**
	 * Get markup for single slide
	 *
	 * @param  string $content   Slide content.
	 * @return string
	 */
	private function get_slide_markup( $content, $image ) {
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
					<textarea class="jslider-slide-content"><?php echo urldecode( $content );?></textarea>
				</div>
				<button class="jslider-sliders-remove"><?php echo esc_html__( 'Delete', 'just-slider' );?></button>
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
			'time' => 3000,
			'height' => 600,
			'slides' => array(
				array(
					'content' => esc_html__( 'put your code here', 'just-slider' ),
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
					<a class=" button jslider-sliders-add"><?php echo esc_html__( 'Add new slide', 'just-slider' );?></a>
					<label>
						<?php echo esc_html__( 'Transition type', 'just-slider' );?>
						<select class="jslider-transition-type jslider-slider-parameter">
							<option value="slide" <?php selected( 'slide', $values['transitionType'], true);?>><?php echo esc_html__( 'Slide', 'just-slider' );?></option>
							<option value="fade" <?php selected( 'fade', $values['transitionType'], true);?>><?php echo esc_html__( 'Fade', 'just-slider' );?></option>
						</select>
					</label>
					<label>
						<?php echo esc_html__( 'Autoplay', 'just-slider' );?>
						<select class="jslider-autoplay jslider-slider-parameter">
							<option value="yes" <?php selected( 'yes', $values['autoplay'], true);?>><?php echo esc_html__( 'Yes', 'just-slider' );?></option>
							<option value="no" <?php selected( 'no', $values['autoplay'], true);?>><?php echo esc_html__( 'No', 'just-slider' );?></option>
						</select>
					</label>
					<label>
						<?php echo esc_html__( 'Transition time (ms)', 'just-slider' );?>
						<input type="number" class="jslider-transition-time jslider-slider-parameter" value="<?php echo esc_attr( $values['time'] );?>">
					</label>
					<label>
						<?php echo esc_html__( 'Height (px)', 'just-slider' );?>
						<input type="number" class="jslider-height jslider-slider-parameter" value="<?php echo esc_attr( $values['height'] );?>">
					</label>
				</div>
				<div class="jslider-slides-list">
					<?php
						foreach ( $values['slides'] as $slide ) {
							echo $this->get_slide_markup( $slide['content'], $slide['image'] );
						}
					?>
				</div>
			</div>
			<input id="just-slider-settings" name="just-slider-settings" type="hidden" value="">
			<?php wp_nonce_field( 'just-slider-save-nonce', 'just-slider-save-nonce' );?>
		<?php
	}

	/**
	 * Render the admin panel.
	 *
 	 * @param WP_Post $post         Post object.
	 */
	public function render_just_slider_classes( $post ) {
		?>
		<div class="just-slider-classes-desc">
			<p><?php echo esc_html__( 'Those classes will make objects appear after x miliseconds.', 'just-slider' );?></p>
			<p><?php echo esc_html__( 'All examples use 500ms but you can change this by setting the number in class name', 'just-slider' );?></p>
			<p><?php echo esc_html__( 'There are classes from 50 to 2500ms, every 50ms.', 'just-slider' );?></p>
		</div>
		<div class="just-slider-classes-class">
			<input class="just-slider-class" onclick="this.focus(); this.select()" type="test" value=".jslider-opacity-500ms">
			<div class="just-slider-class-desc">Fade in</div>
		</div>
		<div class="just-slider-classes-class">
			<input class="just-slider-class" onclick="this.focus(); this.select()" type="test" value=".jslider-from-top-500ms">
			<div class="just-slider-class-desc">Scroll in from the top</div>
		</div>
		<div class="just-slider-classes-class">
			<input class="just-slider-class" onclick="this.focus(); this.select()" type="test" value=".jslider-from-left--500ms">
			<div class="just-slider-class-desc">Scroll in from the left</div>
		</div>
		<div class="just-slider-classes-class">
			<input class="just-slider-class" onclick="this.focus(); this.select()" type="test" value=".jslider-scale-500ms">
			<div class="just-slider-class-desc">Scale from 0% to 100%</div>
		</div>
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
			wp_die( esc_html__( 'Nonce incorrect!', 'just-slider' ) );
		}
		$values = filter_input( INPUT_POST, 'just-slider-settings', FILTER_SANITIZE_STRING );
		$values = html_entity_decode( $values );
		update_post_meta( $post_id, '_just_slider_values', $values );

		return $post_id;
	}
}
