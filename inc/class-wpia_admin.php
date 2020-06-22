<?php

class WPIA_Admin {

	function __construct() {
		//Registers the post type
		add_action( 'init', array( $this, 'register_post_type' ) );

		//Adds scripts to be used
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles_scripts' ) );

		//Adds current object (if one exists) to be edited to header
		add_action( 'admin_print_scripts', array( $this, 'add_current_json' ) );

		//Adds revision support for the post type's meta keys
		add_filter( 'wp_post_revision_meta_keys', array( $this, 'add_meta_keys_to_revision' ) );

		//Seed added:BEGIN
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_image_annotator_settings') );
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
		
		$vanilla_tagger_editor=file_get_contents( __DIR__.'/../css/vanilla-tagger-editor.css');
		$vanilla_tagger_navigation=file_get_contents( __DIR__.'/../css/vanilla-tagger-navigation.css');
		$vanilla_tagger_theme=file_get_contents( __DIR__.'/../css/vanilla-tagger.theme.css');
		$vanilla_tagger_editor_tagdata_tmpl=file_get_contents( __DIR__.'/../js/vanilla-tagger-editor.tagdata.tmpl.js');
		$vanilla_tagger_editor_webc=file_get_contents( __DIR__.'/../js/vanilla-tagger-editor.webc.js');
		$vanilla_tagger_navigation_webc=file_get_contents( __DIR__.'/../js/vanilla-tagger-navigation.webc.js');
		$vanilla_tagger_webc=file_get_contents( __DIR__.'/../js/vanilla-tagger.webc.js');
		
		?>
		<div class="wrap">
			<h1>Image annotator settings</h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'image-annotator-settings-group' ); ?>
				<?php do_settings_sections( 'image-annotator-settings-group' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Vanilla tagger editor (CSS)</th>
						
						<td><textarea type="text" name="vanilla_tagger_editor" style="width:100%;height:300px"><?php echo ( get_option( 'vanilla_tagger_editor',$vanilla_tagger_editor ) ); ?></textarea></td>
					</tr>
					<tr valign="top">
						<th scope="row">Vanilla tagger navigator (CSS)</th>
						<td><textarea type="text" name="vanilla_tagger_navigation" style="width:100%;height:300px"><?php echo ( get_option( 'vanilla_tagger_navigation',$vanilla_tagger_navigation ) ); ?></textarea></td>
					</tr>
					<tr valign="top">
						<th scope="row">Vanilla tagger theme (CSS)</th>
						<td><textarea type="text" name="vanilla_tagger_theme" style="width:100%;height:300px"><?php echo ( get_option( 'vanilla_tagger_theme',$vanilla_tagger_theme ) ); ?></textarea></td>
					</tr>	
					<tr valign="top">
						<th scope="row">Vanilla tagger editor tagdata tmpl (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_editor_tagdata_tmpl" style="width:100%;height:300px"><?php echo ( get_option( 'vanilla_tagger_editor_tagdata_tmpl',$vanilla_tagger_editor_tagdata_tmpl ) ); ?></textarea></td>
					</tr>						

					<tr valign="top">
						<th scope="row">Vanilla tagger editor webc (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_editor_webc" style="width:100%;height:300px"><?php echo ( get_option( 'vanilla_tagger_editor_webc',$vanilla_tagger_editor_webc ) ); ?></textarea></td>
					</tr>						
					<tr valign="top">
						<th scope="row">Vanilla tagger navigation webc (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_navigation_webc" style="width:100%;height:300px"><?php echo ( get_option( 'vanilla_tagger_navigation_webc',$vanilla_tagger_navigation_webc ) ); ?></textarea></td>
					</tr>						
					<tr valign="top">
						<th scope="row">Vanilla tagger webc (JS)</th>
						<td><textarea type="text" name="vanilla_tagger_webc" style="width:100%;height:300px"><?php echo ( get_option( 'vanilla_tagger_webc',$vanilla_tagger_webc ) ); ?></textarea></td>
					</tr>						

					
				</table>

				<?php submit_button(); ?>

			</form>
		</div>
		<?php
	}

	function register_image_annotator_settings() { // whitelist options
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_editor' );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_navigation' );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_theme' );
		register_setting( 'image-annotator-settings-group', 'vanilla_tagger_editor_tagdata_tmpl' );
	}

	//Seed added:END

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

	/**
	 * Adds scripts and styles.
	 *
	 * @param WP_Post $hook Current page.
	 */
	function admin_styles_scripts( $hook ) {

		if ( $hook !== 'edit.php' && $hook !== 'post.php' && $hook !== 'post-new.php' )
			return;

		if ( get_post_type() !== 'annotation' )
			return;

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'wpia-admin-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
		wp_enqueue_style( 'wpia-admin-style', plugins_url( '../admin/css/style.css', __FILE__ ) );

		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );

		wp_enqueue_script( 'wpia-admin-fabric', plugins_url( '../lib/fabricjs/js/fabric.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'wpia-admin-fabricex', plugins_url( '../admin/js/fabric.canvasex.js', __FILE__ ), array( 'wpia-admin-fabric' ) );
		wp_enqueue_script( 'wpia-admin-imagesloaded', plugins_url( '../lib/imagesLoaded/imagesloaded.pkgd.min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'wpia-admin-scripts', plugins_url( '../admin/js/script.js', __FILE__ ), array( 'jquery', 'media-upload', 'thickbox' ) );
	}

	function add_current_json() {

		if ( !(get_current_screen()->base === 'post') && !(get_current_screen()->post_type === 'annotation') )
			return;

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
