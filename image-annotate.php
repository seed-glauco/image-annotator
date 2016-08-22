<?php
/*
  Plugin Name: WP Image Annotate
  Description: Adds the ability to create annotated images and then edit the annotations later.
  Version: 1.0
  Author: Moe Loubani
  Author URI: http://www.moeloubani.com
  License: GPLv2
 */


//Add file for plugin setup (post type and script registration)
require('inc/class-wpia_admin.php');

//Adds image annotation area
require('inc/class-wpia_metaboxes.php');

//Adds front end functions like shortcodes and script registration
require('inc/class-wpia_front.php');