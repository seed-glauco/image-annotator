<?php

class WPIA_Front {

    public function __construct()
    {
        //Add the shortcode
        add_shortcode('wpia_image', array($this, 'shortcode') );

        //Adds an empty JS object to the header to be used later on
        add_action('wp_head', array($this, 'add_header_variable') );

        //Adds the styles and scripts to run things and make them look good
        add_action('wp_enqueue_scripts', array($this, 'add_scripts_styles'));

        //Adds the shortcode button in the TinyMCE editor
        add_action('init', array($this, 'shortcode_button'));

        //Adds annotation JSON to the header so they can be loaded
        add_action('admin_print_scripts', array($this, 'admin_scripts_styles'));
    }

    //This is a check for mobile that excludes iPads since the image still is relatively large on those
    protected function wpia_is_mobile() {
        static $is_mobile;

        if ( isset($is_mobile) )
            return $is_mobile;

        if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
            $is_mobile = false;
        } elseif (
            strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false ) {
            $is_mobile = true;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
            $is_mobile = true;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
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
            array (
                'id' => '',
            ),
            $atts,
            'wpia_image'
        );

        //Query args to get annotated image
        $args = array(
            'post_type'         => 'annotation',
            'posts_per_page'    => 1,
            'p'                 => $atts['id']
        );

        $annotation_query = new WP_Query($args);

        if($annotation_query->have_posts()) {
            while($annotation_query->have_posts()) {
                $annotation_query->the_post();
                $image = get_post_meta(get_the_ID(), 'wpia_annotation_image', true);
                $data = get_post_meta(get_the_ID(), 'wpia_annotation_data', true);
				/*
                $original_size = get_post_meta(get_the_ID(), 'wpia_annotation_canvas_size', true);
                $annotation_data = json_decode($data);
                $annotation_text = array();
                $is_mobile = $this->wpia_is_mobile();
                $counter = 0;

                foreach($annotation_data->objects as $object) {
                    if($object->type === 'text-standalone') {

                        $annotation_text[] = $object->text;
                        if($is_mobile) {
                            $counter++;
                            $object->text = strval($counter);
                            $object->fontSize = 48;
                        }
                    }
                }

                if($is_mobile) {
                    $data = json_encode($annotation_data);
                }
*/
                ob_start();
                ?>

<script>
<?=get_option('vanilla_tagger_webc')?>
</script>	
				<vanilla-tagger
					id="wpia-preview-image"			
					src="<?php echo $image; ?>"
					placeholder="#wpia-toolbar"
					data-tags="<?= esc_attr( $data ) ?>"
					data-theme-text="<?php /*= esc_attr( $data )*/ ?>"
					>
					Your browser doesn't currently support this component<br />
					<a href="https://browsehappy.com/" target="_blank"
					   >Please , update your browser</a
					>
				</vanilla-tagger>			
                <!--script>
                    wpiaGeneratedImage["<?php echo get_the_ID(); ?>"] = <?php echo $data; ?>;
                </script>
                <div class="annotated-image-container">
                    <div class="annotated-image-wrapper" data-wpia="<?php echo get_the_ID(); ?>" data-wpia-originalsize="<?php echo $original_size; ?>">
                        <img src="<?php echo $image; ?>" alt="<?php the_title(); ?>" class="annotated-image">
                        <canvas id="live-canvas-<?php echo get_the_ID(); ?>" class="live-canvas"></canvas>
                    </div>
                    <div class="annotation-text">
                        <ol>
                            <?php foreach($annotation_text as $text) : ?>
                                <li><?php echo $text; ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div-->



                <?php
                $output = ob_get_clean();
            }
        }

        wp_enqueue_script('wpia-front-script');
        wp_reset_postdata();
        return $output;

    }


    public function add_scripts_styles()
    {
        wp_register_script('wpia-front-fabric', plugins_url('../lib/fabricjs/js/fabric.js', __FILE__), array('jquery'));
        wp_register_script('wpia-front-imagesloaded', plugins_url('../lib/imagesLoaded/imagesloaded.pkgd.min.js', __FILE__), array('jquery'));
        wp_register_script('wpia-front-script', plugins_url('../front/js/script.js', __FILE__), array('jquery', 'wpia-front-fabric', 'wpia-front-imagesloaded'));
        wp_enqueue_style('wpia-front-style', plugins_url('../front/css/style.css', __FILE__));
		
		
		
		
		
		
		
    }

    public function add_header_variable()
    {
        echo '<script type="text/javascript"> var wpiaGeneratedImage = {}; </script>';
    }

    public function shortcode_button()
    {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', array($this, 'add_plugin'));
            add_filter('mce_buttons', array($this, 'register_button'));
        }
    }

    public function register_button( $buttons )
    {
        array_push( $buttons, "|", "annotate" );
        return $buttons;
    }

    public function add_plugin( $plugin_array )
    {
        $plugin_array['annotate'] = plugins_url('../front/js/tinymce.js', __FILE__);
        return $plugin_array;
    }

    public function get_annotations() {
        $annotations_array = array();
        $args = array(
            'posts_per_page' => 500,
            'post_type'      => 'annotation'
        );

        $annotations = get_posts($args);

        if(!empty($annotations)) {
            foreach($annotations as $annotation) {

                $annotations_array[] = array(
                    'value' => $annotation->ID,
                    'text' => $annotation->post_title
                );
            }
        }

        return $annotations_array;

    }

    public function admin_scripts_styles()
    {

        $annotations_array = $this->get_annotations();

        echo "<script type='text/javascript'>\n";
        echo 'var annotations = ' . wp_json_encode($annotations_array) . ';';
        echo "\n</script>";
    }

}

new WPIA_Front();