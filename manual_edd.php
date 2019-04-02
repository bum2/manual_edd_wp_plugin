<?php
/*
Plugin Name: Easy Digital Downloads - Manual Purchase Gateway
Plugin URL: 
Description: A manual (transfers) gateway for Easy Digital Downloads
Version: 0.2
Author: Bumbum (forked from Hackafou)
Author URI: 
*/

//Language
load_plugin_textdomain( 'manual_edd_wp_plugin', false,  dirname(plugin_basename(__FILE__)) . '/languages/' );

//Load post fields management
require_once ( __DIR__ . '/manual_edd_wp_post.php');

//Registers the gateway
function manual_wp_edd_register_gateway( $gateways ) {
	$gateways['manual_gateway'] = array( 'admin_label' => 'Manual', 'checkout_label' => __( 'Donation via Transfer or cryptocurrency (0% fee)', 'manual_edd_wp_plugin' ) );
	return $gateways;
}
add_filter( 'edd_payment_gateways', 'manual_wp_edd_register_gateway' );

//Pre purchase form
function edd_manual_gateway_cc_form() {
	
	$output = '<div>';			

		global $edd_options;
		$output .= $edd_options['mgs_transfer_info'];
	
	$output .= "</div>";

	echo $output;
	
}
add_action('edd_manual_gateway_cc_form', 'edd_manual_gateway_cc_form');


// processes the payment
function manual_wp_edd_process_payment( $purchase_data ) {	
	global $edd_options;	
	// check for any stored errors
	$errors = edd_get_errors();
	if ( ! $errors ) {

		$purchase_summary = edd_get_purchase_summary( $purchase_data );

		$payment = array(
			'price'        => $purchase_data['price'],
			'date'         => $purchase_data['date'],
			'user_email'   => $purchase_data['user_email'],
			'purchase_key' => $purchase_data['purchase_key'],
			'currency'     => $edd_options['currency'],
			'downloads'    => $purchase_data['downloads'],
			'cart_details' => $purchase_data['cart_details'],
			'user_info'    => $purchase_data['user_info'],
			'status'       => 'pending'
		);

		// record the pending payment
		$payment = edd_insert_payment( $payment );

		// send email with payment info
		manual_email_purchase_order( $payment );

		edd_send_to_success_page();		

	} else {
		$fail = true; // errors were detected
	}

	if ( $fail !== false ) {
		// if errors are present, send the user back to the purchase page so they can be corrected
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
	}
}
add_action( 'edd_gateway_manual_gateway', 'manual_wp_edd_process_payment' );


// adds the settings to the Payment Gateways section
function manual_wp_edd_add_settings ( $settings ) {

	$manual_gateway_settings = array(
		array(
			'id' => 'manual_gateway_settings',
			'name' => '<strong>' . __( 'Manual Gateway Settings', 'manual_edd_wp_plugin' ) . '</strong>',
			'desc' => __( 'settings_tittle_desc', 'manual_edd_wp_plugin' ),
			'type' => 'header'
		),
		array(
			'id' => 'mgs_platform_IBAN',
			'name' => __( 'platform_iban', 'manual_edd_wp_plugin' ),
			'desc' => __( 'platform_iban_desc', 'manual_edd_wp_plugin' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id' => 'mgs_platform_BIN',
			'name' => __( 'platform_bin', 'manual_edd_wp_plugin' ),
			'desc' => __( 'platform_bin_desc', 'manual_edd_wp_plugin' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id' => 'mgs_one_or_multiple_IBAN',
			'name' => __( 'one_multiple_accounts', 'manual_edd_wp_plugin' ),
			'desc' => __( 'one_multiple_accounts_desc', 'manual_edd_wp_plugin' ),
			'type' => 'select',
			'options' => array(1 => 'ONE', 2 => 'MULTIPLE'),
			'std'  => 1
		),
		array(
			'id' => 'mgs_transfer_info',
			'name' => __( 'transfer_info', 'manual_edd_wp_plugin' ),
			'desc' => __( 'transfer_info_desc', 'manual_edd_wp_plugin' ),
			'type' => 'textarea'
		),
		array(
			'id' => 'mgs_from_email',
			'name' => __( 'from_email', 'manual_edd_wp_plugin' ),
			'desc' => __( 'from_email_desc', 'manual_edd_wp_plugin' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => get_bloginfo( 'admin_email' )
		),
		array(
			'id' => 'mgs_subject_mail',
			'name' => __( 'subject_mail', 'manual_edd_wp_plugin' ),
			'desc' => __( 'subject_mail_desc', 'manual_edd_wp_plugin' ),//  . '<br/>' . edd_get_emails_tags_list(),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id' => 'mgs_body_mail',
			'name' => __( 'body_mail', 'manual_edd_wp_plugin' ),
			'desc' => __('body_mail_desc', 'manual_edd_wp_plugin') . '<br/>' . edd_get_emails_tags_list()  ,
			'type' => 'textarea',
		),

	);

	return array_merge( $settings, $manual_gateway_settings );
}
add_filter( 'edd_settings_gateways', 'manual_wp_edd_add_settings' );


function edd_email_tag_IBAN( $payment_id ) {

	global $edd_options;
	if ( $edd_options['mgs_one_or_multiple_IBAN'] == 1 ) {
		$IBAN = $edd_options['mgs_platform_IBAN'];
	} else {
		$downloads = edd_get_payment_meta_cart_details( $payment_id );
		$post_id = $downloads[0]['id'];
		$IBAN = get_post_meta( $post_id, 'manual_edd_wp_post_IBAN', true );
	}
	return $IBAN;

}
function edd_email_tag_BIC( $payment_id ) {

	global $edd_options;
	if ( $edd_options['mgs_one_or_multiple_IBAN'] == 1 ) {
		$BIC = $edd_options['mgs_platform_BIC'];
	} else {
		$downloads = edd_get_payment_meta_cart_details( $payment_id );
		$post_id = $downloads[0]['id'];
		$BIC = get_post_meta( $post_id, 'manual_edd_wp_post_BIN', true );
	}
	return $BIC;

}

function manual_edd_setup_email_tags() {

	// Setup default tags array
	$email_tags = array(
		array(
			'tag'         => 'IBAN',
			'description' => __( 'preconfigured_IBAN', 'manual_edd_wp_plugin' ),
			'function'    => 'edd_email_tag_IBAN'
		),
		array(
			'tag'         => 'BIC',
			'description' => __( 'preconfigured_BIC', 'manual_edd_wp_plugin' ),
			'function'    => 'edd_email_tag_BIC'
		)
	);

	// Apply edd_email_tags filter
	$email_tags = apply_filters( 'edd_email_tags', $email_tags );

	// Add email tags
	foreach ( $email_tags as $email_tag ) {
		edd_add_email_tag( $email_tag['tag'], $email_tag['description'], $email_tag['function'] );
	}

}
add_action( 'edd_add_email_tags', 'manual_edd_setup_email_tags' );


////   E M A I L   T O   U S E R   ////

//Sent transfer intructions
function manual_email_purchase_order ( $payment_id, $admin_notice = true ) {

	global $edd_options;

	$payment_data = edd_get_payment_meta( $payment_id );
	$user_id      = edd_get_payment_user_id( $payment_id );
	$user_info    = maybe_unserialize( $payment_data['user_info'] );
	$to           = edd_get_payment_user_email( $payment_id );

	if ( isset( $user_id ) && $user_id > 0 ) {
		$user_data = get_userdata($user_id);
		$name = $user_data->display_name;
	} elseif ( isset( $user_info['first_name'] ) && isset( $user_info['last_name'] ) ) {
		$name = $user_info['first_name'] . ' ' . $user_info['last_name'];
	} else {
		$name = $email;
	}

	$message = '';//edd_get_email_body_header();

	
	if ( $edd_options['mgs_one_or_multiple_IBAN'] == 1 ) {
		$email = stripslashes( $edd_options['mgs_body_mail'] );
		$from_email = isset( $edd_options['mgs_from_email'] ) ? $edd_options['mgs_from_email'] : get_option('admin_email');
		$subject = wp_strip_all_tags( $edd_options['mgs_subject_mail'], true );	
	} else {
		$downloads = edd_get_payment_meta_cart_details( $payment_id );
		$post_id = $downloads[0]['id'];
		$email = stripslashes (get_post_meta( $post_id, 'manual_edd_wp_post_body_mail', true ));
		$from_email = get_post_meta( $post_id, 'manual_edd_wp_post_from_email', true );
		$subject = wp_strip_all_tags(get_post_meta( $post_id, 'manual_edd_wp_post_subject_mail', true ));
	}

        $from_admin = isset( $edd_options['mgs_from_email'] ) ? $edd_options['mgs_from_email'] : get_option('admin_email');
	
	$message .= edd_do_email_tags( $email, $payment_id );
	//$message .= edd_get_email_body_footer();

	$from_name = get_bloginfo('name');
	
	$subject = edd_do_email_tags( $subject, $payment_id );
	
	$bkp_headers = EDD()->emails->get_headers();

	$headers = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_admin>\r\n";
	$headers .= "Reply-To: ". $from_email . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	$headers = apply_filters( 'edd_receipt_headers', $headers, $payment_id, $payment_data );
	
	EDD()->emails->__set('headers', $headers);
	
	$attachments = apply_filters( 'edd_sale_notification_attachments', array(), $payment_id, $payment_data );

	if ( apply_filters( 'edd_email_purchase_receipt', true ) ) { //wp_mail( $to, $subject, $message, $headers ) ) {
		EDD()->emails->send( $to, $subject, $message, $attachments );
	}
	
	EDD()->emails->__set('headers', $bkp_headers);
	
	if ( $admin_notice && !edd_admin_notices_disabled( $payment_id ) ) {
		do_action( 'manual_admin_sale_notice', $payment_id, $payment_data );
	}			
}

////   E M A I L   T O   A D M I N S   ////

/**
 * Sends the Admin Sale Notification Email
 *
 * @since 1.4.2
 * @param int $payment_id Payment ID (default: 0)
 * @param array $payment_data Payment Meta and Data
 * @return void
 */
function manual_admin_email_notice( $payment_id = 0, $payment_data = array(), $testdata = false ) {
	global $edd_options;

	/* Send an email notification to the admin */
	$admin_email = manual_get_admin_notice_emails( $payment_id ); // bumbum
	$user_id     = edd_get_payment_user_id( $payment_id );
	$user_info   = maybe_unserialize( $payment_data['user_info'] );

	if ( isset( $user_id ) && $user_id > 0 ) {
		$user_data = get_userdata($user_id);
		$name = $user_data->display_name;
	} elseif ( isset( $user_info['first_name'] ) && isset( $user_info['last_name'] ) ) {
		$name = $user_info['first_name'] . ' ' . $user_info['last_name'];
	} else {
		$name = $user_info['email'];
	}

	$admin_message = '';//edd_get_email_body_header();
	
	if( $testdata !== false) $admin_message .= $testdata; // bumbum
	
	$admin_message .= edd_get_sale_notification_body_content( $payment_id, $payment_data );
	//$admin_message .= edd_get_email_body_footer();

	if( !empty( $edd_options['sale_notification_subject'] ) ) {
		$admin_subject = wp_strip_all_tags( $edd_options['sale_notification_subject'], true );
	} else {
		$admin_subject = sprintf( __( 'New coopfunding payment #%1$s', 'edd' ), $payment_id );
	}
	
	if( $testdata !== false ) $admin_subject = 'TEST payment {payment_id} of {price}';
	
	$admin_subject = edd_do_email_tags( $admin_subject, $payment_id );
	$admin_subject = apply_filters( 'edd_admin_sale_notification_subject', $admin_subject, $payment_id, $payment_data );

	$from_name  = isset( $edd_options['from_name'] )  ? $edd_options['from_name']  : get_bloginfo('name');
	$from_email = isset( $edd_options['from_email'] ) ? $edd_options['from_email'] : get_option('admin_email');

    $admin_attachments = apply_filters( 'edd_admin_sale_notification_attachments', array(), $payment_id, $payment_data );
    
    EDD()->emails->send( $admin_email, $admin_subject, $admin_message, $admin_attachments );
    
}
add_action( 'manual_admin_sale_notice', 'manual_admin_email_notice', 10, 2 );

/**
 * Retrieves the emails for which admin notifications are sent to (these can be
 * changed in the EDD Settings)
 *
 * @since 1.0
 * @global $edd_options Array of all the EDD Options
 * @return void
 */
function manual_get_admin_notice_emails( $payment_id = 0 ) {
	global $edd_options;

	$emails = isset( $edd_options['admin_notice_emails'] ) && strlen( trim( $edd_options['admin_notice_emails'] ) ) > 0 ? $edd_options['admin_notice_emails'] : get_bloginfo( 'admin_email' );

	$emails = array_map( 'trim', explode( "\n", $emails ) );

	return apply_filters( 'edd_admin_notice_emails', $emails, $payment_id );
}
