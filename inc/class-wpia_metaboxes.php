<?php

class WPIA_Metaboxes {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'wpia_register_edit_area' ) );
		add_action( 'save_post', array( $this, 'wpia_save_meta_box' ) );
	}

	function wpia_register_edit_area() {
		add_meta_box( 'wpia-meta',
				__( 'Image Annotation', 'image-annotator' ),
				array( $this, 'wpia_display_callback' ),
				'annotation'
		);
	}

	function wpia_display_callback( $post ) {
		$image = get_post_meta( $post->ID, 'wpia_annotation_image', true );
		$data = get_post_meta( $post->ID, 'wpia_annotation_data', true );
		$original_size = get_post_meta( $post->ID, 'wpia_annotation_canvas_size', true );
		?>
		<div id="upload-area">
			<p>Image to annotate.</p>
			<div class="upload-fields">
				<input id="upload_image" type="text" size="36" name="upload_image" value="<?php echo $image; ?>" />
				<input id="upload_image_button" type="button" value="<?php _e( 'Choose image', 'image-annotator' ); ?>" />
			</div>
		</div>
		<div id="work-area">
			<div id="wpia-toolbar">
			</div>
			<div id="canvas-area" class="wpia-canvas-area">
				<vanilla-tagger-editor
					id="wpia-preview-image"			
					src="<?php echo $image; ?>"
					placeholder="#wpia-toolbar"
					data-tags="<?= esc_attr( $data ) ?>"
					>
					Your browser doesn't currently support this component<br />
					<a href="https://browsehappy.com/" target="_blank"
					   >Please , update your browser</a
					>
				</vanilla-tagger-editor>						              
			</div>
		</div>
		<div id="raw-code">
			<p>Raw JSON for annotations</p>
			<textarea type="text" name="image_annotation" id="image_annotation_json"><?php echo $data; ?></textarea>
		</div>
		<input type="hidden" value="<?php echo $original_size; ?>" name="original_size" id="wpia-original-size">
		<?php
		wp_nonce_field( 'wpia_nonce_verify', 'wpia_nonce' );
	}

	/**
	 * Save meta box content.
	 *
	 * @param int $post_id Post ID
	 */
	function wpia_save_meta_box( $post_id ) {
		// Add nonce for security and authentication.
		$nonce_name = isset( $_POST['wpia_nonce'] ) ? $_POST['wpia_nonce'] : '';
		$nonce_action = 'wpia_nonce_verify';

		// Check if nonce is set.
		if ( !isset( $nonce_name ) ) {
			return;
		}

		// Check if nonce is valid.
		if ( !wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, 'wpia_annotation_image', $_POST['upload_image'] );
		update_post_meta( $post_id, 'wpia_annotation_data', $_POST['image_annotation'] );
		update_post_meta( $post_id, 'wpia_annotation_canvas_size', $_POST['original_size'] );
	}

}

new WPIA_Metaboxes();
