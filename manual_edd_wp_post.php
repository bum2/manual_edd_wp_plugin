<?php

function text_callback ( $args, $post_id ) {
	$value = get_post_meta( $post_id, $args['id'], true );
	if ( $value != "" ) {
		$value = get_post_meta( $post_id, $args['id'], true );
	}else{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$output = "<tr valign='top'> \n".
		" <th scope='row'> " . $args['name'] . " </th> \n" .
		" <td><input type='text' class='regular-text' id='" . $args['id'] . "'" .
		" name='" . $args['id'] . "' value='" .  $value   . "' />\n" .
		" <label for='" . $args['name'] . "'> " . $args['desc'] . "</label>" .
		"</td></tr>";

	return $output;
}

function rich_editor_callback ( $args, $post_id ) {
	$value = get_post_meta( $post_id, $args['id'], true );
	if ( $value != "" ) {
		$value = get_post_meta( $post_id, $args['id'], true );
	}else{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	$output = "<tr valign='top'> \n".
		" <th scope='row'> " . $args['name'] . " </th> \n" .
		" <td>";
		ob_start();
		wp_editor( stripslashes( $value ) , $args['id'], array( 'textarea_name' => $args['id'], 'tinymce' => false, 'quicktags' => false, 'media_buttons' => false  ) );
	$output .= ob_get_clean();

	$output .= " <label for='" . $args['name'] . "'> " . $args['desc'] . "</label>" .
		"</td></tr>\n";

	return $output;
}


/**
 * Updates when saving post
 *
 */
function manual_edd_wp_post_save( $post_id ) {

	if ( ! isset( $_POST['post_type']) || 'download' !== $_POST['post_type'] ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;

	$fields = manual_wp_edd_fields();
	
	foreach ($fields as $field) {
		update_post_meta( $post_id, $field['id'],  $_REQUEST[$field['id']] );	
	}
}
add_action( 'save_post', 'manual_edd_wp_post_save' );


/**
 * Display sidebar metabox in saving post
 *
 */
function manual_edd_wp_print_meta_box ( $post ) {

	if ( get_post_type( $post->ID ) != 'download' ) return;

	?>
	<div class="wrap">
		<div id="tab_container">	
			<table class="form-table">						
				<?php
					$fields = manual_wp_edd_fields();
					foreach ($fields as $field) {		
						if ( $field['type'] == 'text'){
							echo text_callback( $field, $post->ID );
						}elseif ( $field['type'] == 'rich_editor' ) {
							echo rich_editor_callback( $field, $post->ID ) ;
						}
					}
				?>
					 
			</table>				
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
}

function manual_edd_wp_show_post_fields ( $post) { 

	add_meta_box( $post->ID, __( "Manual gateway Campaign Settings", 'manual_edd_wp_plugin'), "manual_edd_wp_print_meta_box", 'download', 'normal', 'high');

}
add_action( 'submitpost_box', 'manual_edd_wp_show_post_fields' );

function manual_wp_edd_fields () {

	$manual_gateway_settings = array(
		array(
			'id' => 'manual_edd_wp_post_IBAN',
			'name' => __( 'Campaign IBAN', 'manual_edd_wp_plugin' ),
			'desc' => __( 'The IBAN account number of the campaign', 'manual_edd_wp_plugin' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id' => 'manual_edd_wp_post_BIC',
			'name' => __( 'Campaign BIC', 'manual_edd_wp_plugin' ),
			'desc' => __( 'The BIC or SWIFT code of the bank account', 'manual_edd_wp_plugin' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id' => 'manual_edd_wp_post_from_email',
			'name' => __( 'Email From', 'manual_edd_wp_plugin' ),
			'desc' => __( 'Reply-to this email address of your project', 'manual_edd_wp_plugin' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => get_bloginfo( 'admin_email' )
		),
		array(
			'id' => 'manual_edd_wp_post_subject_mail',
			'name' => __( 'Email Subject', 'manual_edd_wp_plugin' ),
			'desc' => __( 'The subject of the email to notify the backer', 'manual_edd_wp_plugin' )  . '<br/>' . edd_get_emails_tags_list(),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id' => 'manual_edd_wp_post_body_mail',
			'name' => __( 'Email Body', 'manual_edd_wp_plugin' ),
			'desc' => __('The text of the email to inform your contributors about other payment methods', 'manual_edd_wp_plugin') . '<br/>' . edd_get_emails_tags_list()  ,
			'type' => 'rich_editor',
		),

	);

	return $manual_gateway_settings;
}
