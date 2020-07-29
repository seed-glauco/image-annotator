<?php
if ( !defined( 'WPINC' ) ):
	die;
endif;

class WPIA_Front {

	public function __construct() {
		//Add the shortcode
		add_shortcode( 'wpia_image', array( $this, 'shortcode' ) );

		//Adds an empty JS object to the header to be used later on
		//add_action( 'wp_head', array( $this, 'add_header_variable' ) );
		//Adds the styles and scripts to run things and make them look good
		//add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts_styles' ) );
		//Adds the shortcode button in the TinyMCE editor
		//add_action( 'init', array( $this, 'shortcode_button' ) );
		//Adds annotation JSON to the header so they can be loaded
		add_action( 'admin_print_scripts', array( $this, 'admin_scripts_styles' ) );
		
		$vanilla_tagger_where_show = 'the_content';
		if ( !empty( get_option( 'vanilla_tagger_where_show' ) ) ):
			$vanilla_tagger_where_show = get_option( 'vanilla_tagger_where_show' );
		endif;
		
		if ( $vanilla_tagger_where_show == 'the_content' ):
			add_filter( 'the_content', array( $this, 'the_content' ) );
		elseif ( $vanilla_tagger_where_show == 'get_footer' ):
			add_action( 'get_footer', array( $this, 'get_footer' ) );
		endif;
	}

	function the_content( $content ) {

		if ( isset( $GLOBALS['post'] ) && get_option( "vanilla-tagger-settings-pt-" . $GLOBALS['post']->post_type ) == 'yes' && !is_admin() && is_singular() ) :

			$wpia_navigatorStatus = get_post_meta( $GLOBALS['post']->ID, 'wpia_navigatorStatus', true );
			$wpia_navigatorPosition = get_post_meta( $GLOBALS['post']->ID, 'wpia_navigatorPosition', true );
			$wpia_navigatorTitle = get_post_meta( $GLOBALS['post']->ID, 'wpia_navigatorTitle', true );
			$sc = '[wpia_image id="' . $GLOBALS['post']->ID . '" navigator="' . $wpia_navigatorStatus . '" placeholder="navigator-placeholder-' . $GLOBALS['post']->ID . '" title="' . esc_attr( $wpia_navigatorTitle ) . '" position="' . $wpia_navigatorPosition . '"]';

			$content = $content . do_shortcode( $sc );

		endif;
		return $content;
	}

	function get_footer() {
		if ( isset( $GLOBALS['post'] ) && get_option( "vanilla-tagger-settings-pt-" . $GLOBALS['post']->post_type ) == 'yes' && !is_admin() && is_singular() ) :

			$wpia_navigatorStatus = get_post_meta( $GLOBALS['post']->ID, 'wpia_navigatorStatus', true );
			$wpia_navigatorPosition = get_post_meta( $GLOBALS['post']->ID, 'wpia_navigatorPosition', true );
			$wpia_navigatorTitle = get_post_meta( $GLOBALS['post']->ID, 'wpia_navigatorTitle', true );
			$sc = '[wpia_image id="' . $GLOBALS['post']->ID . '" navigator="' . $wpia_navigatorStatus . '" placeholder="navigator-placeholder-' . $GLOBALS['post']->ID . '" title="' . esc_attr( $wpia_navigatorTitle ) . '" position="' . $wpia_navigatorPosition . '"]';

			echo do_shortcode( $sc );

		endif;
		//return $content;
	}
	
	// Add shortcode and return output
	public function shortcode( $atts ) {


		if ( !empty( get_option( 'vanilla_tagger_navigation' ) ) ):
			$vanilla_tagger_navigation = get_option( 'vanilla_tagger_navigation' );
		else:
			$vanilla_tagger_navigation = file_get_contents( VANILLA_TAGGER_NAVIGATION_CSS_FILE );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_theme' ) ) ):
			$vanilla_tagger_theme = get_option( 'vanilla_tagger_theme' );
		else:
			$vanilla_tagger_theme = file_get_contents( VANILLA_TAGGER_THEME_CSS_FILE );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_navigation_webc' ) ) ):
			$vanilla_tagger_navigation_webc = get_option( 'vanilla_tagger_navigation_webc' );
		else:
			$vanilla_tagger_navigation_webc = file_get_contents( VANILLA_TAGGER_NAVIGATION_WEBC_JS_FILE );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_webc' ) ) ):
			$vanilla_tagger_webc = get_option( 'vanilla_tagger_webc' );
		else:
			$vanilla_tagger_webc = file_get_contents( VANILLA_TAGGER_WEBC_JS_FILE );
		endif;


		$output = '';

		// Attributes
		$atts = shortcode_atts(
				array(
					'id' => '',
					'navigator' => '0',
					'title' => '',
					'position' => '',
					'placeholder' => ''
				),
				$atts,
				'wpia_image'
		);

		//Query args to get annotated image
		$args = array(
			'post_type' => 'any',
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
					<?= $vanilla_tagger_navigation ?>
					</style>	
					<script>
					<?= $vanilla_tagger_webc ?>
					<?= $vanilla_tagger_navigation_webc ?>
					</script>	
					<section class="vtagger-wrapper"><section class="vtagger-imgcontainer">
							<vanilla-tagger-navigation
								id="v-tagger"			
								src="<?= $image ?>"
								placeholder="#<?= $atts['placeholder'] ?>"
								data-title="<?= esc_attr( $atts['title'] ) ?>"						
								data-tags="<?= esc_attr( $data ) ?>"
								data-theme-text="<?= esc_attr( $vanilla_tagger_theme ) ?>"
								>
								Your browser doesn't currently support this component<br />
								<a href="https://browsehappy.com/" target="_blank">Please , update your browser</a>
							</vanilla-tagger-navigation>	
						</section>
						<aside class="vtagger-navcontainer <?= $atts['position'] ?>" id="<?= $atts['placeholder'] ?>"
							   ></aside>
					</section>
					<?php
				else:
					?>
					<script>
					<?= $vanilla_tagger_webc ?>
					</script>	
					<vanilla-tagger
						id="wpia-preview-image"			
						src="<?= $image ?>"
						data-tags="<?= esc_attr( $data ) ?>"
						data-theme-text="<?= esc_attr( $vanilla_tagger_theme ) ?>"
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
