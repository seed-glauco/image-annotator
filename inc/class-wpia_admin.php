<?php

class WPIA_Admin {

    function __construct()
    {
        //Registers the post type
        add_action('init', array($this, 'register_post_type'));

        //Adds scripts to be used
        add_action('admin_enqueue_scripts', array($this, 'admin_styles_scripts'));

        //Adds current object (if one exists) to be edited to header
        add_action('admin_print_scripts', array($this, 'add_current_json'));

        //Adds revision support for the post type's meta keys
        add_filter('wp_post_revision_meta_keys', array($this, 'add_meta_keys_to_revision'));
    }

    public function register_post_type() {

        $labels = array(
            'name'               => _x( 'Annotations', 'post type general name', 'wp_image_annotator' ),
            'singular_name'      => _x( 'Annotation', 'post type singular name', 'wp_image_annotator' ),
            'menu_name'          => _x( 'Annotations', 'admin menu', 'wp_image_annotator' ),
            'name_admin_bar'     => _x( 'Annotation', 'add new on admin bar', 'wp_image_annotator' ),
            'add_new'            => _x( 'Add New', 'annotation', 'wp_image_annotator' ),
            'add_new_item'       => __( 'Add New Annotation', 'wp_image_annotator' ),
            'new_item'           => __( 'New Annotation', 'wp_image_annotator' ),
            'edit_item'          => __( 'Edit Annotation', 'wp_image_annotator' ),
            'view_item'          => __( 'View Annotation', 'wp_image_annotator' ),
            'all_items'          => __( 'All Annotations', 'wp_image_annotator' ),
            'search_items'       => __( 'Search Annotations', 'wp_image_annotator' ),
            'parent_item_colon'  => __( 'Parent Annotations:', 'wp_image_annotator' ),
            'not_found'          => __( 'No annotations found.', 'wp_image_annotator' ),
            'not_found_in_trash' => __( 'No annotations found in Trash.', 'wp_image_annotator' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'wp_image_annotator' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'annotation' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'revisions' )
        );

        register_post_type( 'annotation', $args );
    }

    /**
     * Adds scripts and styles.
     *
     * @param WP_Post $hook Current page.
     */

    function admin_styles_scripts($hook) {

        if( $hook !== 'edit.php' && $hook !== 'post.php' && $hook !== 'post-new.php' )
            return;

        if(get_post_type() !== 'annotation')
            return;

        wp_enqueue_style('thickbox');
        wp_enqueue_style('wpia-admin-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
        wp_enqueue_style('wpia-admin-style', plugins_url('../admin/css/style.css', __FILE__));

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');

        wp_enqueue_script('wpia-admin-fabric', plugins_url('../lib/fabricjs/js/fabric.js', __FILE__), array('jquery'));
        wp_enqueue_script('wpia-admin-fabricex', plugins_url('../admin/js/fabric.canvasex.js', __FILE__), array('wpia-admin-fabric'));
        wp_enqueue_script('wpia-admin-imagesloaded', plugins_url('../lib/imagesLoaded/imagesloaded.pkgd.min.js', __FILE__), array('jquery'));
        wp_enqueue_script('wpia-admin-scripts', plugins_url('../admin/js/script.js', __FILE__), array('jquery','media-upload','thickbox'));


    }

    function add_current_json() {

        if( !(get_current_screen()->base === 'post') && !(get_current_screen()->post_type === 'annotation') )
            return;

        $id = get_the_ID();

        echo "<script type='text/javascript'>\n";
        echo 'var currentWIPAObject = [' . get_post_meta($id, "wpia_annotation_data", true) . ', ' . get_post_meta($id, "wpia_annotation_canvas_size", true) . '];';
        echo "\n</script>";
    }

    function add_meta_keys_to_revision( $keys ) {
        $keys[] = 'wpia_annotation_date';
        $keys[] = 'wpia_annotation_canvas_size';
        return $keys;
    }

}

new WPIA_Admin();