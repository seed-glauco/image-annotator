<?php

class WPIA_Front {

	public function __construct() {
		//Add the shortcode
		add_shortcode( 'wpia_image', array( $this, 'shortcode' ) );

		//Adds an empty JS object to the header to be used later on
		//add_action( 'wp_head', array( $this, 'add_header_variable' ) );
		//Adds the styles and scripts to run things and make them look good
		//add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts_styles' ) );
		//Adds the shortcode button in the TinyMCE editor
		add_action( 'init', array( $this, 'shortcode_button' ) );

		//Adds annotation JSON to the header so they can be loaded
		add_action( 'admin_print_scripts', array( $this, 'admin_scripts_styles' ) );
	}

	//This is a check for mobile that excludes iPads since the image still is relatively large on those
	protected function wpia_is_mobile() {
		static $is_mobile;

		if ( isset( $is_mobile ) )
			return $is_mobile;

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_mobile = false;
		} elseif (
				strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false ) {
			$is_mobile = true;
		} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false && strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) == false ) {
			$is_mobile = true;
		} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) !== false ) {
			$is_mobile = false;
		} else {
			$is_mobile = false;
		}

		return $is_mobile;
	}

	// Add shortcode and return output
	public function shortcode( $atts ) {

		$output = '';

		// Attributes
		$atts = shortcode_atts(
				array(
					'id' => '',
					'navigator' => '0',
					'placeholder' => ''
				),
				$atts,
				'wpia_image'
		);

		//Query args to get annotated image
		$args = array(
			'post_type' => 'annotation',
			'posts_per_page' => 1,
			'p' => $atts['id']
		);

		$annotation_query = new WP_Query( $args );

		if ( $annotation_query->have_posts() ) {
			while ( $annotation_query->have_posts() ) {
				$annotation_query->the_post();
				$image = get_post_meta( get_the_ID(), 'wpia_annotation_image', true );
				$data = get_post_meta( get_the_ID(), 'wpia_annotation_data', true );

				//$original_size = get_post_meta( get_the_ID(), 'wpia_annotation_canvas_size', true );
				//$annotation_data = json_decode( $data );
				//$annotation_text = array();

				ob_start();

				if ( isset( $atts['navigator'] ) && !empty( $atts['navigator'] ) && $atts['navigator'] == '1' && isset( $atts['placeholder'] ) && !empty( $atts['placeholder'] ) ):
					?>
					<style>
					<?= get_option( 'vanilla_tagger_navigation' ) ?>
					</style>	
					<script>
					<?= get_option( 'vanilla_tagger_webc' ) ?>
					<?= get_option( 'vanilla_tagger_navigation_webc' ) ?>
					</script>	
					<vanilla-tagger-navigation
						id="v-tagger"			
						src="<?= $image ?>"
						placeholder="<?= $atts['placeholder'] ?>"
						data-tags="<?= esc_attr( $data ) ?>"
						data-theme-text="<?= esc_attr( get_option( 'vanilla_tagger_theme' ) ) ?>"
						>
						Your browser doesn't currently support this component<br />
						<a href="https://browsehappy.com/" target="_blank">Please , update your browser</a>
					</vanilla-tagger-navigation>			
					<?php
				else:
					?>
					<script>
					<?= get_option( 'vanilla_tagger_webc' ) ?>
					</script>	
					<vanilla-tagger
						id="wpia-preview-image"			
						src="<?= $image ?>"
						placeholder="#wpia-toolbar"
						data-tags="<?= esc_attr( $data ) ?>"
						data-theme-text="<?= esc_attr( get_option( 'vanilla_tagger_theme' ) ) ?>"
						>
						Your browser doesn't currently support this component<br />
						<a href="https://browsehappy.com/" target="_blank">Please , update your browser</a>
					</vanilla-tagger>			
				<?php
				endif;
				$output = ob_get_clean();
			}
		}

		//wp_enqueue_script( 'wpia-front-script' );
		wp_reset_postdata();
		return $output;
	}

	public function shortcode_button() {
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_button' ) );
		}
	}

	public function register_button( $buttons ) {
		array_push( $buttons, "|", "annotate" );
		return $buttons;
	}

	public function add_plugin( $plugin_array ) {
		$plugin_array['annotate'] = plugins_url( '../front/js/tinymce.js', __FILE__ );
		return $plugin_array;
	}

	public function get_annotations() {
		$annotations_array = array();
		$args = array(
			'posts_per_page' => 500,
			'post_type' => 'annotation'
		);

		$annotations = get_posts( $args );

		if ( !empty( $annotations ) ) {
			foreach ( $annotations as $annotation ) {

				$annotations_array[] = array(
					'value' => $annotation->ID,
					'text' => $annotation->post_title
				);
			}
		}

		return $annotations_array;
	}

	public function admin_scripts_styles() {

		$annotations_array = $this->get_annotations();

		echo "<script type='text/javascript'>\n";
		echo 'var annotations = ' . wp_json_encode( $annotations_array ) . ';';
		echo "\n</script>";
	}

}

new WPIA_Front();
