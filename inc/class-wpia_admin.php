<?php
if ( !defined( 'WPINC' ) ):
	die;
endif;

class WPIA_Admin {
	/*
	  protected $vanilla_tagger_default_options = array(
	  'post_types' => array(),
	  );
	 */

	function __construct() {
		//Registers the post type
		//add_action( 'init', array( $this, 'register_post_type' ) );
		/*
		  add_action( 'init', array( $this, 'register_taxes_annotation_tag' ) );
		  add_action( 'restrict_manage_posts', array( $this,'filter_post_type_by_taxonomy') );
		  add_filter( 'parse_query', array( $this,'convert_id_to_term_in_query') );
		 */
		//Adds scripts to be used
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles_scripts' ) );

		//Adds current object (if one exists) to be edited to header
		add_action( 'admin_print_scripts', array( $this, 'add_current_json' ) );

		//Adds revision support for the post type's meta keys
		add_filter( 'wp_post_revision_meta_keys', array( $this, 'add_meta_keys_to_revision' ) );

		//Seed added:BEGIN
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_image_annotator_settings' ) );
		//Seed added:END
	}

	//Seed added:BEGIN

	/**
	 * Registers a new settings page under Settings.
	 */
	public function admin_menu() {
		add_options_page(
				__( 'Image annotator', 'textdomain' ),
				__( 'Image annotator', 'textdomain' ),
				'manage_options',
				'options_image_annotator',
				array(
					$this,
					'settings_page'
				)
		);
	}

	/**
	 * Settings page display callback.
	 */
	function settings_page() {

		$vanilla_tagger_editor = file_get_contents( VANILLA_TAGGER_EDITOR_CSS_FILE );
		$vanilla_tagger_navigation = file_get_contents( VANILLA_TAGGER_NAVIGATION_CSS_FILE );
		$vanilla_tagger_theme = file_get_contents( VANILLA_TAGGER_THEME_CSS_FILE );
		$vanilla_tagger_editor_tagdata_tmpl = file_get_contents( VANILLA_TAGGER_EDITOR_TAGDATA_TMPL_JS_FILE );
		$vanilla_tagger_editor_webc = file_get_contents( VANILLA_TAGGER_EDITOR_WEBC_JS_FILE );
		$vanilla_tagger_navigation_webc = file_get_contents( VANILLA_TAGGER_NAVIGATION_WEBC_JS_FILE );
		$vanilla_tagger_webc = file_get_contents( VANILLA_TAGGER_WEBC_JS_FILE );

		if ( !empty( get_option( 'vanilla_tagger_editor' ) ) ):
			$vanilla_tagger_editor = get_option( 'vanilla_tagger_editor' );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_navigation' ) ) ):
			$vanilla_tagger_navigation = get_option( 'vanilla_tagger_navigation' );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_theme' ) ) ):
			$vanilla_tagger_theme = get_option( 'vanilla_tagger_theme' );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_editor_tagdata_tmpl' ) ) ):
			$vanilla_tagger_editor_tagdata_tmpl = get_option( 'vanilla_tagger_editor_tagdata_tmpl' );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_editor_webc' ) ) ):
			$vanilla_tagger_editor_webc = get_option( 'vanilla_tagger_editor_webc' );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_navigation_webc' ) ) ):
			$vanilla_tagger_navigation_webc = get_option( 'vanilla_tagger_navigation_webc' );
		endif;

		if ( !empty( get_option( 'vanilla_tagger_webc' ) ) ):
			$vanilla_tagger_webc = get_option( 'vanilla_tagger_webc' );
		endif;
		/*
		  if ( !empty( get_option( 'vanilla_tagger_options' ) ) ):
		  $vanilla_tagger_options = get_option( 'vanilla_tagger_options' );
		  endif; */
		?>
		<div class="wrap">
			<h1>Image annotator settings</h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'image-annotator-settings-group' ); ?>
				<?php do_settings_sections( 'image-annotator-settings-group' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Vanilla tagger editor (CSS)</th>

						<td><textarea type="text" name="vanilla_tagger_editor" style="width:100%;height:300px"><?= $vanilla_tagger_editor ?></textarea></td>
					</tr>
					<tr valign="top">
						<th scope="row">Vanilla tagger navigator (CSS)</th>
						<td><textarea type="text" name="vanilla_tagger_navigation" style="width:100%;height:300px"><?= $vanilla_tagger_navigation ?></textarea></td>
					</tr>
					<tr valign="top">
						<th scope="row">Vanilla tagger theme (CSS)</th>
						<td><textarea type="text" name="vanilla_tagger_theme" style="width:100%;height:300px"><?= $vanilla_tagger_theme ?></textarea></td>
					</tr>	
					<tr valign="top">
						<th scope="row">Vanilla tagger editor tagdata tmpl (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_editor_tagdata_tmpl" style="width:100%;height:300px"><?= $vanilla_tagger_editor_tagdata_tmpl ?></textarea></td>
					</tr>						

					<tr valign="top">
						<th scope="row">Vanilla tagger editor webc (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_editor_webc" style="width:100%;height:300px"><?= $vanilla_tagger_editor_webc ?></textarea></td>
					</tr>						
					<tr valign="top">
						<th scope="row">Vanilla tagger navigation webc (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_navigation_webc" style="width:100%;height:300px"><?= $vanilla_tagger_navigation_webc ?></textarea></td>
					</tr>						
					<tr valign="top">
						<th scope="row">Vanilla tagger webc (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_webc" style="width:100%;height:300px"><?= $vanilla_tagger_webc ?></textarea></td>
					</tr>						
					<tr valign="top">
						<th scope="row">Post types</th>
						<td>


							<?php
							//var_dump($vanilla_tagger_options);
							foreach ( get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ), 'names' ) as $pt ) {
								echo "<div><b>" . $pt . "</b></div><hr>";
								echo "<select name=\"vanilla-tagger-settings-pt-" . $pt . "\" id=\"vanilla-tagger-settings-pt-" . $pt . "\">";
								echo "<option value=\"yes\" " . (selected( get_option( "vanilla-tagger-settings-pt-" . $pt ), "yes" )) . ">yes</option>";
								echo "<option value=\"no\" " . (selected( get_option( "vanilla-tagger-settings-pt-" . $pt ), "no" )) . ">no</option>";
								echo "</select><br><br>";
							}
							?>						
						</td>
					</tr>						


				</table>

				<?php submit_button(); ?>

			</form>
		</div>
		<?php
	}

	function register_image_annotator_settings() { // whitelist options
		$vanilla_tagger_editor = file_get_contents( VANILLA_TAGGER_EDITOR_CSS_FILE );
		$vanilla_tagger_navigation = file_get_contents( VANILLA_TAGGER_NAVIGATION_CSS_FILE );
		$vanilla_tagger_theme = file_get_contents( VANILLA_TAGGER_THEME_CSS_FILE );
		$vanilla_tagger_editor_tagdata_tmpl = file_get_contents( VANILLA_TAGGER_EDITOR_TAGDATA_TMPL_JS_FILE );
		$vanilla_tagger_editor_webc = file_get_contents( VANILLA_TAGGER_EDITOR_WEBC_JS_FILE );
		$vanilla_tagger_navigation_webc = file_get_contents( VANILLA_TAGGER_NAVIGATION_WEBC_JS_FILE );
		$vanilla_tagger_webc = file_get_contents( VANILLA_TAGGER_WEBC_JS_FILE );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_editor', array( 'default' => $vanilla_tagger_editor ) );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_navigation', array( 'default' => $vanilla_tagger_navigation ) );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_theme', array( 'default' => $vanilla_tagger_theme ) );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_editor_tagdata_tmpl', array( 'default' => $vanilla_tagger_editor_tagdata_tmpl ) );

		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_editor_webc', array( 'default' => $vanilla_tagger_editor_webc ) );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_navigation_webc', array( 'default' => $vanilla_tagger_navigation_webc ) );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_webc', array( 'default' => $vanilla_tagger_webc ) );

		foreach ( get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ), 'names' ) as $pt ) {
			register_setting( 'image-annotator-settings-group', 'vanilla-tagger-settings-pt-' . $pt, array( 'default' => 'no' ) );
		}
		//register_setting( 'image-annotator-settings-group', 'vanilla_tagger_options', array( 'default' => $this->vanilla_tagger_default_options ) );
	}

	//Seed added:END
	/*
	  public function register_post_type() {

	  $labels = array(
	  'name' => _x( 'Annotations', 'post type general name', 'wp_image_annotator' ),
	  'singular_name' => _x( 'Annotation', 'post type singular name', 'wp_image_annotator' ),
	  'menu_name' => _x( 'Annotations', 'admin menu', 'wp_image_annotator' ),
	  'name_admin_bar' => _x( 'Annotation', 'add new on admin bar', 'wp_image_annotator' ),
	  'add_new' => _x( 'Add New', 'annotation', 'wp_image_annotator' ),
	  'add_new_item' => __( 'Add New Annotation', 'wp_image_annotator' ),
	  'new_item' => __( 'New Annotation', 'wp_image_annotator' ),
	  'edit_item' => __( 'Edit Annotation', 'wp_image_annotator' ),
	  'view_item' => __( 'View Annotation', 'wp_image_annotator' ),
	  'all_items' => __( 'All Annotations', 'wp_image_annotator' ),
	  'search_items' => __( 'Search Annotations', 'wp_image_annotator' ),
	  'parent_item_colon' => __( 'Parent Annotations:', 'wp_image_annotator' ),
	  'not_found' => __( 'No annotations found.', 'wp_image_annotator' ),
	  'not_found_in_trash' => __( 'No annotations found in Trash.', 'wp_image_annotator' )
	  );

	  $args = array(
	  'labels' => $labels,
	  'description' => __( 'Description.', 'wp_image_annotator' ),
	  'public' => true,
	  'publicly_queryable' => true,
	  'show_ui' => true,
	  'show_in_menu' => true,
	  'query_var' => true,
	  'rewrite' => array( 'slug' => 'annotation' ),
	  'capability_type' => 'post',
	  'has_archive' => true,
	  'hierarchical' => false,
	  'menu_position' => null,
	  'supports' => array( 'title', 'revisions' )
	  );

	  register_post_type( 'annotation', $args );
	  }
	 */
	/*
	  function register_taxes_annotation_tag() {

	  $labels = [
	  "name" => __( "Annotation tags", "twentytwenty" ),
	  "singular_name" => __( "Annotation tag", "twentytwenty" ),
	  ];

	  $args = [
	  "label" => __( "Annotation tags", "twentytwenty" ),
	  "labels" => $labels,
	  "public" => false,
	  "publicly_queryable" => false,
	  "hierarchical" => false,
	  "show_ui" => true,
	  "show_in_menu" => true,
	  "show_in_nav_menus" => true,
	  "query_var" => true,
	  "rewrite" => [ 'slug' => 'annotation_tag', 'with_front' => true, ],
	  "show_admin_column" => true,
	  "show_in_rest" => true,
	  "rest_base" => "annotation_tag",
	  "rest_controller_class" => "WP_REST_Terms_Controller",
	  "show_in_quick_edit" => true,
	  ];
	  register_taxonomy( "annotation_tag", [ "annotation" ], $args );
	  }

	  function filter_post_type_by_taxonomy() {
	  global $typenow;
	  $post_type = 'annotation'; // change to your post type
	  $taxonomy = 'annotation_tag'; // change to your taxonomy
	  if ( $typenow == $post_type ) {
	  $selected = isset( $_GET[$taxonomy] ) ? $_GET[$taxonomy] : '';
	  $info_taxonomy = get_taxonomy( $taxonomy );
	  wp_dropdown_categories( array(
	  'show_option_all' => sprintf( __( 'Show all %s', 'textdomain' ), $info_taxonomy->label ),
	  'taxonomy' => $taxonomy,
	  'name' => $taxonomy,
	  'orderby' => 'name',
	  'selected' => $selected,
	  'show_count' => true,
	  'hide_empty' => true,
	  ) );
	  };
	  }

	  function convert_id_to_term_in_query( $query ) {
	  global $pagenow;
	  $post_type = 'annotation'; // change to your post type
	  $taxonomy = 'annotation_tag'; // change to your taxonomy
	  $q_vars = &$query->query_vars;
	  if ( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == $post_type && isset( $q_vars[$taxonomy] ) && is_numeric( $q_vars[$taxonomy] ) && $q_vars[$taxonomy] != 0 ) {
	  $term = get_term_by( 'id', $q_vars[$taxonomy], $taxonomy );
	  $q_vars[$taxonomy] = $term->slug;
	  }
	  }
	 */

	/**
	 * Adds scripts and styles.
	 *
	 * @param WP_Post $hook Current page.
	 */
	function admin_styles_scripts( $hook ) {

		if ( $hook !== 'edit.php' && $hook !== 'post.php' && $hook !== 'post-new.php' )
			return;

//		if ( get_post_type() !== 'annotation' ):
		if ( get_option( "vanilla-tagger-settings-pt-" . get_post_type() ) != 'yes' ):
			return;
		endif;





		wp_enqueue_style( 'thickbox' );

		//wp_enqueue_style( 'wpia-vtagger-editor-style', plugins_url( '../lib/vanilla-tagger/plugins/editor/vanilla-tagger-editor.css', __FILE__ ) );

		wp_enqueue_style( 'wpia-admin-style', plugins_url( '../admin/css/style.css', __FILE__ ) );

		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		/*
		  wp_enqueue_script( 'wpia-vtagger-js', plugins_url( '../lib/vanilla-tagger/vanilla-tagger.webc.js', __FILE__ ) );

		  wp_enqueue_script( 'wpia-vtagger-editor-tmpl-js', plugins_url( '../lib/vanilla-tagger/plugins/editor/vanilla-tagger-editor.tagdata.tmpl.js', __FILE__ ), array( 'wpia-vtagger-js' ) );


		  wp_enqueue_script( 'wpia-vtagger-editor-js', plugins_url( '../lib/vanilla-tagger/plugins/editor/vanilla-tagger-editor.webc.js', __FILE__ ), array( 'wpia-vtagger-js', 'wpia-vtagger-editor-tmpl-js' ) );
		 */
		wp_enqueue_script( 'wpia-admin-scripts', plugins_url( '../admin/js/script.js', __FILE__ ), array( 'media-upload', 'thickbox' ) );
	}

	function add_current_json() {

		//if ( !(get_current_screen()->base === 'post') && !(get_current_screen()->post_type === 'annotation') ):


		if ( !(get_current_screen()->base === 'post') && get_option( "vanilla-tagger-settings-pt-" . get_current_screen()->post_type ) == 'yes' ):

			return;
		endif;

		$id = get_the_ID();

		echo "<script type='text/javascript'>\n";
		echo 'var currentWIPAObject = [' . get_post_meta( $id, "wpia_annotation_data", true ) . ', ' . get_post_meta( $id, "wpia_annotation_canvas_size", true ) . '];';
		echo "\n</script>";
	}

	function add_meta_keys_to_revision( $keys ) {
		$keys[] = 'wpia_annotation_date';
		$keys[] = 'wpia_annotation_canvas_size';
		return $keys;
	}

}

new WPIA_Admin();
