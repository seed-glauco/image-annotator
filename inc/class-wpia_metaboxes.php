<?php
if ( !defined( 'WPINC' ) ):
	die;
endif;

class WPIA_Metaboxes {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'wpia_register_edit_area' ) );
		add_action( 'save_post', array( $this, 'wpia_save_meta_box' ) );
	}

	function wpia_register_edit_area() {
		
		
								$pts=array();
								$pts[]='none';
									foreach ( get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ), 'names' ) as $pt ) {
	
								if(get_option( "vanilla-tagger-settings-pt-" . $pt )=='yes'):
									$pts[]=$pt;
								endif;
							}
		
		
		add_meta_box( 'wpia-meta',
				__( 'Immagine annotata', 'image-annotator' ),
				array( $this, 'wpia_display_callback' ),
				$pts
		);
	}

	function wpia_display_callback( $post ) {


		if ( !empty( get_option( 'vanilla_tagger_editor' ) ) ):
			$vanilla_tagger_editor = get_option( 'vanilla_tagger_editor' );
		else:
			$vanilla_tagger_editor = file_get_contents( VANILLA_TAGGER_EDITOR_CSS_FILE );
		endif;
		if ( !empty( get_option( 'vanilla_tagger_webc' ) ) ):
			$vanilla_tagger_webc = get_option( 'vanilla_tagger_webc' );
		else:
			$vanilla_tagger_webc = file_get_contents( VANILLA_TAGGER_WEBC_JS_FILE );
		endif;		
		if ( !empty( get_option( 'vanilla_tagger_editor_tagdata_tmpl' ) ) ):
			$vanilla_tagger_editor_tagdata_tmpl = get_option( 'vanilla_tagger_editor_tagdata_tmpl' );
		else:
			$vanilla_tagger_editor_tagdata_tmpl = file_get_contents( VANILLA_TAGGER_EDITOR_TAGDATA_TMPL_JS_FILE );
		endif;			
		if ( !empty( get_option( 'vanilla_tagger_editor_webc' ) ) ):
			$vanilla_tagger_editor_webc = get_option( 'vanilla_tagger_editor_webc' );
		else:
			$vanilla_tagger_editor_webc = file_get_contents( VANILLA_TAGGER_EDITOR_WEBC_JS_FILE );
		endif;				

		if ( !empty( get_option( 'vanilla_tagger_theme' ) ) ):
			$vanilla_tagger_theme = get_option( 'vanilla_tagger_theme' );
		else:
			$vanilla_tagger_theme = file_get_contents( VANILLA_TAGGER_THEME_CSS_FILE );
		endif;
		

		$image = get_post_meta( $post->ID, 'wpia_annotation_image', true );
		$data = get_post_meta( $post->ID, 'wpia_annotation_data', true );
		$original_size = get_post_meta( $post->ID, 'wpia_annotation_canvas_size', true );
		$wpia_navigatorStatus = get_post_meta( $post->ID, 'wpia_navigatorStatus', true );
		$wpia_navigatorPosition = get_post_meta( $post->ID, 'wpia_navigatorPosition', true );
		$wpia_navigatorTitle = get_post_meta( $post->ID, 'wpia_navigatorTitle', true );
		?>
		<div id="upload-area">
			<p>Immagine da annotare.</p>
			<div class="upload-fields">
				<input id="upload_image" type="text" size="36" name="upload_image" value="<?php echo $image; ?>" />
				<input id="upload_image_button" type="button" value="<?php _e( 'Scegli il file', 'image-annotator' ); ?>" />
			</div>
		</div>
		<div id="work-area">
			<div id="wpia-toolbar">
			</div>
			<div id="canvas-area" class="wpia-canvas-area">
				<style><?= $vanilla_tagger_editor ?></style>
				<script>
				<?=$vanilla_tagger_webc?>
				<?=$vanilla_tagger_editor_tagdata_tmpl?>
				<?=$vanilla_tagger_editor_webc?>
				</script>
				<vanilla-tagger-editor
					id="wpia-preview-image"			
					src="<?php echo $image; ?>"
					placeholder="#wpia-toolbar"
					data-tags="<?= esc_attr( $data ) ?>"
					data-theme-text="<?= esc_attr( $vanilla_tagger_theme ) ?>"
					>
					Your browser doesn't currently support this component<br />
					<a href="https://browsehappy.com/" target="_blank"
					   >Please , update your browser</a
					>
				</vanilla-tagger-editor>						              
			</div>
		</div>
<div>
	<table>
		<tr>
			<th>
				Navigatore:
			</th>
			<td>
				<select name="wpia_navigatorStatus" id="wpia_navigatorStatus">
					<option value="1" <?=(($wpia_navigatorStatus==='1')?('selected'):(''))?>>SI</option>
					<option value="0" <?=(($wpia_navigatorStatus==='0')?('selected'):(''))?>>NO</option>
				</select>
				
			</td>
			<th>
				Posizione:
			</th>
			<td>
				<select name="wpia_navigatorPosition" id="wpia_navigatorPosition">
					<option value="vt-inner" <?=(($wpia_navigatorPosition==='vt-inner')?('selected'):(''))?>>Interno</option>
					<option value="" <?=(($wpia_navigatorPosition==='')?('selected'):(''))?>>Esterno</option>
				</select>
				
			</td>	
			<th>
				Titolo:
			</th>
			<td>
				<input type="text" name="wpia_navigatorTitle" id="wpia_navigatorTitle" value="<?=esc_attr( $wpia_navigatorTitle )?>">
				
			</td>			
		</tr>
	</table>
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

		update_post_meta( $post_id, 'wpia_navigatorStatus', $_POST['wpia_navigatorStatus'] );
		update_post_meta( $post_id, 'wpia_navigatorPosition', $_POST['wpia_navigatorPosition'] );
		update_post_meta( $post_id, 'wpia_navigatorTitle', $_POST['wpia_navigatorTitle'] );
		
		
	}

}

new WPIA_Metaboxes();
