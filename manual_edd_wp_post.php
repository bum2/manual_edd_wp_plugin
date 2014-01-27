<?php

/**
 * Updates bank_data when saving post
 *
 */
function manual_edd_wp_bank_account_save( $post_id ) {
	if ( ! isset( $_POST['post_type']) || 'download' !== $_POST['post_type'] ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;

	if ( isset( $_REQUEST['manual_edd_wp_postIBAN'] ) ) {
		update_post_meta( $post_id, 'manual_edd_wp_postIBAN', strip_tags( stripslashes( $_REQUEST['manual_edd_wp_postIBAN'] ) ) );
	}
	if ( isset( $_REQUEST['manual_edd_wp_postBIC'] ) ) {
		update_post_meta( $post_id, 'manual_edd_wp_postBIC', strip_tags( stripslashes( $_REQUEST['manual_edd_wp_postBIC'] ) ) );
	}
}
add_action( 'save_post', 'manual_edd_wp_bank_account_save' );


/**
 * Dispaly bank_data sidebar metabox in saving post
 *
 */
function manual_edd_wp_print_meta_box ( $post ) {


	if ( get_post_type( $post->ID ) != 'download' ) return;

	$IBAN = get_post_meta( $post->ID, 'manual_edd_wp_postIBAN', true );
	$BIN =  get_post_meta( $post->ID, 'manual_edd_wp_postBIC', true );

	?>
	<fieldset class="inline-edit-col-left">
		<div id="manual_edd_wp_bank_account" class="inline-edit-col">			
			<label>
				<span class="title"><?php _e( 'post_IBAN', 'manual_edd_wp_plugin' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="manual_edd_wp_postIBAN" class="text" value ="<?php echo $IBAN ?>"/>
				</span>
			</label>
			<br class="clear" />
			<label>
				<span class="title"><?php _e( 'post_BIC', 'manual_edd_wp_plugin' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="manual_edd_wp_postBIC" class="text" value ="<?php echo $BIN ?>"/>
				</span>
			</label>
			<br class="clear" />
		</div>
	</fieldset>
	<?php

}

function manual_edd_wp_show_post_fields ( $post) { 

	add_meta_box( $post->ID, __( "bank_account_tittle", 'manual_edd_wp_plugin'), "manual_edd_wp_print_meta_box", 'download', 'side', 'high');

}

add_action( 'submitpost_box', 'manual_edd_wp_show_post_fields' );
