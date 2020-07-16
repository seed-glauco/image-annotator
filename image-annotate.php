<?php

/*
  Plugin Name: Image Annotator
  Description: Adds the ability to create annotated images and then edit the annotations later.
  Version: 1.4.6
  Author: Moe Loubani
  Author URI: http://www.moeloubani.com
  License: GPLv3
  GitHub Plugin URI: https://github.com/seed-glauco/image-annotator
 */

if ( !defined( 'WPINC' ) ):
	die;
endif;

add_filter( 'github_updater_override_dot_org', function() {
    return [ 
        'image-annotator/image-annotate.php', //plugin format
      ];
});

define( 'VANILLA_TAGGER_EDITOR_CSS_FILE', __DIR__ . '/lib/vanilla-tagger/plugins/editor/vanilla-tagger-editor.css' );
define( 'VANILLA_TAGGER_NAVIGATION_CSS_FILE', __DIR__ . '/lib/vanilla-tagger/plugins/navigation/vanilla-tagger-navigation.css' );
define( 'VANILLA_TAGGER_THEME_CSS_FILE', __DIR__ . '/lib/vanilla-tagger/vanilla-tagger.theme.css' );
define( 'VANILLA_TAGGER_EDITOR_TAGDATA_TMPL_JS_FILE', __DIR__ . '/lib/vanilla-tagger/plugins/editor/vanilla-tagger-editor.tagdata.tmpl.js' );
define( 'VANILLA_TAGGER_EDITOR_WEBC_JS_FILE', __DIR__ . '/lib/vanilla-tagger/plugins/editor/vanilla-tagger-editor.webc.js' );
define( 'VANILLA_TAGGER_NAVIGATION_WEBC_JS_FILE', __DIR__ . '/lib/vanilla-tagger/plugins/navigation/vanilla-tagger-navigation.webc.js' );
define( 'VANILLA_TAGGER_WEBC_JS_FILE', __DIR__ . '/lib/vanilla-tagger/vanilla-tagger.webc.js' );



//Add file for plugin setup (post type and script registration)
require('inc/class-wpia_admin.php');


//Adds image annotation area
require('inc/class-wpia_metaboxes.php');

//Adds front end functions like shortcodes and script registration
require('inc/class-wpia_front.php');
