=== Image Annotator ===

Contributors: moeloubani1

Tags: html5, canvas, annotation

Requires at least: 4.1

Tested up to: 4.6

Stable tag: 4.6

License: GPLv3


This is a plugin that uses the HTML5 canvas and FabricJS to allow you to add shapes and text on top of images and display those images. 

== Description ==

If you've ever wanted to draw on an image or add a caption or text to your image then this is the plugin for you. Best of all it allows you to add your caption, save it, display it, then go back and edit it in different layers and objects laid on top of your image.

It extracts the text from the annotations and displays them as selectable and stylable text just below.

You can hold shift as you draw rectangles to make them perfect squares, same with the ellipse tool and circles.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/image-annotator` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Add a new annotation by going to 'Annotations' and then 'Add New' in the left hand columns of your admin panel 
4. Click choose image, add your annotations, and hit publish/update to save them.
5. On a post or page you can use the TinyMCE shortcode button to add one of your saved annotations or use the [wpia_image id=id_here] shortcode.


== Frequently Asked Questions ==

= How can I change the font in the annotations? =

Changing font sizes and colors is coming (although you can edit them in the JS files now) but you can add a custom font family by defining a font in your CSS using @font-face with the name 'annotatorFont' and then files from whichever font you would like.

= When are colors coming? Other fonts? =

As soon as I have time I hope to expand this plugin to allow for stroke width (border width) changes, color changes, free drawing and more. Stay updated!

== Screenshots ==

1. This shows the editing process after one round of adding different annotations and shapes. You can see how things are labeled and the speech bubble is used.
2. This shows how easy it is to just move objects around, remove objects, move the speech bubble around and decide where it goes.
3. This shows you how to add an annotation to one of your posts or pages.
4. Here is an example of the annotation in action on a desktop sized screen. The text below is stylable with CSS (or you can hide it!)
5. This is an example of the annotation on a mobile device, note how the text has been replaced with a larger font and numbers that reference the text below so that it is still readable despite everything getting shrunk down.

== Changelog ==

= 1.0 =
* First release of plugin - many more changes to come!

Special thanks to: 

Réseau BIBLIO du Centre-du-Québec, de Lanaudière et de la Mauricie (http://www.mabibliotheque.ca/centre-du-quebec-de-lanaudiere-et-de-la-mauricie/fr/index.aspx) and Desaulniers Simard (https://desaulniers-simard.com).

Everyone involved with making and contributing to FabricJS.

Everyone on Stack Overflow and other websites that answers questions and keeps the community strong!
