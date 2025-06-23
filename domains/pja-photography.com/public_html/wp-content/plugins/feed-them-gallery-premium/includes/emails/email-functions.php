<?php
/**
 * Email Functions
 *
 * @package     Feed Them Gallery Premium
 * @subpackage  Emails
 * @copyright   Copyright (c) 2020, SlickRemix
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Add premium email tags.
 *
 * @since	1.0
 * @param	array	$email_tags	Email tags
 * @return	array	Email tags
 */
function ftg_premium_setup_email_tags( $email_tags )	{
	$premium_tags = array(
		array(
			'tag'         => 'album_title',
			'description' => __( 'Title of the album', 'feed-them-gallery-premium' ),
			'function'    => 'ftg_cm_email_tag_album_title'
		),
		array(
			'tag'         => 'album_url',
			'description' => __( 'Adds a URL so clients can view their album directly on your website.', 'feed-them-gallery-premium' ),
			'function'    => 'ftg_cm_email_tag_album_url'
		),
		array(
			'tag'         => 'album_url_path',
			'description' => __( 'Adds a non-linked URL so clients can view their album directly on your website.', 'feed-them-gallery-premium' ),
			'function'    => 'ftg_cm_email_tag_album_url_path'
		)
	);

	return array_merge( $email_tags, $premium_tags );
} // ftg_premium_setup_email_tags
add_filter( 'ftg_cm_email_tags', 'ftg_premium_setup_email_tags' );

/**
 * Email template tag: album_title
 * The title of the Album.
 *
 * @since	1.0
 * @param	int		$item_id
 * @return	string	Album title
 */
function ftg_cm_email_tag_album_title( $item_id )	{
	return get_the_title( $item_id );
} // ftg_cm_email_tag_album_title

/**
 * Email template tag: album_url
 * Adds a URL so clients can view their album directly on your website
 *
 * @since	1.0
 * @param	int		$item_id
 * @return	string	Album URL
 */
function ftg_cm_email_tag_album_url( $item_id ) {
	$url = get_permalink( $item_id );
	$url = apply_filters( 'ftg_premium_tag_album_url', $url, $item_id );

	return '<a href="' . $url . '">' . $url . '</a>';
} // ftg_cm_email_tag_album_url

/**
 * Email template tag: album_url_path
 * Adds a non-linked URL so clients can view their album directly on your website
 *
 * @since	1.0
 * @param	int		$item_id
 * @return	string	Album URL path
 */
function ftg_cm_email_tag_album_url_path( $item_id ) {
	$url = get_permalink( $item_id );
	$url = apply_filters( 'ftg_premium_tag_album_url_path', $url, $item_id );

	return $url;
} // ftg_cm_email_tag_album_url_path

/**
 * Email the album invite to the client.
 *
 * @since	1.0
 * @param	int		$album_id     	Album ID
 * @param	int		$client_id      Client ID
 * @return	bool	True if the email was successfully sent
 */
function ftg_premium_send_album_invite_email( $album_id, $client_id ) {
    $user_info = get_userdata( $client_id );

    if ( empty( $user_info ) )  {
        return;
    }

    do_action( 'ftg_premium_before_send_album_invite_email', $album_id, $user_info );

    $to_email = $user_info->user_email;

    if ( is_email( $to_email ) ) {
		$email_tags = new FTG_CM_NAMESPACE\FTG_CM_Email_Template_Tags();

        $from_name    = ftg_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
        $from_name    = apply_filters( 'ftg_premium_album_invite_from_name', $from_name, $album_id, $client_id );

        $from_email   = ftg_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
        $from_email   = apply_filters( 'ftg_premium_album_invite_from_address', $from_email, $album_id, $client_id );

        $subject      = ftg_get_option( 'album_invite_subject', __( 'Your Album is Ready', 'feed-them-gallery-premium' ) );
        $subject      = apply_filters( 'ftg_premium_album_invite_subject', wp_strip_all_tags( $subject ), $album_id, $client_id );
        $subject      = $email_tags->ftg_cm_do_email_tags( $subject, $album_id, $client_id );

        $heading      = ftg_get_option( 'album_invite_heading', __( 'Your Album Details', 'feed-them-gallery-premium' ) );
        $heading      = apply_filters( 'ftg_premium_album_invite_heading', $heading, $album_id, $client_id );
        $heading      = $email_tags->ftg_cm_do_email_tags( $heading, $album_id. $client_id );

        $message      = $email_tags->ftg_cm_do_email_tags( FTG_CM_NAMESPACE\FTG_CM_Email_Template::ftg_cm_get_album_invite_email_body_content( $album_id, $client_id ), $album_id, $client_id );

		$emails = new FTG_CM_NAMESPACE\FTG_CM_Emails();

        $emails->__set( 'from_name', $from_name );
        $emails->__set( 'from_email', $from_email );
        $emails->__set( 'heading', $heading );

        $headers = apply_filters( 'ftg_premium_album_invite_headers', $emails->get_headers(), $album_id, $client_id );
        $emails->__set( 'headers', $headers );

        $sent = $emails->send( $to_email, $subject, $message );

        do_action( 'ftg_premium_send_album_invite_email', $album_id, $user_info, $sent );

        return $sent;
    }

	return false;
} // ftg_premium_send_album_invite_email

/**
 * Email the album updated to the client.
 *
 * @since	1.0
 * @param	int		$album_id     	Album ID
 * @param	int		$client_id      Client ID
 * @return	bool	True if the email was successfully sent
 */
function ftg_premium_send_album_updated_email( $album_id, $client_id ) {
    $user_info = get_userdata( $client_id );

    if ( empty( $user_info ) )  {
        return;
    }

    do_action( 'ftg_premium_before_send_album_updated_email', $album_id, $user_info );

    $to_email = $user_info->user_email;

    if ( is_email( $to_email ) )    {
		$email_tags = new FTG_CM_NAMESPACE\FTG_CM_Email_Template_Tags();

        $from_name    = ftg_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
        $from_name    = apply_filters( 'ftg_premium_album_updated_from_name', $from_name, $album_id, $client_id );

        $from_email   = ftg_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
        $from_email   = apply_filters( 'ftg_premium_album_updated_from_address', $from_email, $album_id, $client_id );

        $subject      = ftg_get_option( 'album_updated_subject', __( 'Your Album is Ready', 'feed-them-gallery-premium' ) );
        $subject      = apply_filters( 'ftg_premium_album_updated_subject', wp_strip_all_tags( $subject ), $album_id, $client_id );
        $subject      = $email_tags->ftg_cm_do_email_tags( $subject, $album_id, $client_id );

        $heading      = ftg_get_option( 'album_updated_heading', __( 'Your Album Details', 'feed-them-gallery-premium' ) );
        $heading      = apply_filters( 'ftg_premium_album_updated_heading', $heading, $album_id, $client_id );
        $heading      = $email_tags->ftg_cm_do_email_tags( $heading, $album_id. $client_id );

        $message      = $email_tags->ftg_cm_do_email_tags( FTG_CM_NAMESPACE\FTG_CM_Email_Template::ftg_cm_get_album_updated_email_body_content( $album_id, $client_id ), $album_id, $client_id );

		$emails = new FTG_CM_NAMESPACE\FTG_CM_Emails();

        $emails->__set( 'from_name', $from_name );
        $emails->__set( 'from_email', $from_email );
        $emails->__set( 'heading', $heading );

        $headers = apply_filters( 'ftg_premium_album_updated_headers', $emails->get_headers(), $album_id, $client_id );
        $emails->__set( 'headers', $headers );

        $sent = $emails->send( $to_email, $subject, $message );

        do_action( 'ftg_premium_send_album_updated_email', $album_id, $user_info, $sent );

        return $sent;
    }

	return false;
} // ftg_premium_send_album_updated_email

/**
 * Send the album invite email.
 *
 * @since	1.0
 * @return	void
 */
function ftg_premium_send_album_invite_email_action()	{
	if ( ! isset( $_GET['ftg_action'], $_GET['album_id'], $_GET['client_id'] ) || 'send_album_invite' != $_GET['ftg_action'] )	{
		return;
	}

	$album_id  = absint( $_GET['album_id'] );
	$client_id = absint( $_GET['client_id'] );

	if ( empty( $album_id ) || empty( $client_id ) )	{
		return;
	}

	if ( FTG_CM_NAMESPACE\FTG_CM_Email_Functions::ftg_cm_send_album_invite_email( $album_id, $client_id ) )	{
		$message = 'invite_album_sent';
	} else	{
		$message = 'invite_album_not_sent';
	}

	$redirect = remove_query_arg( array( 'ftg_action', 'album_id', 'ftg-message' ) );
	$redirect = add_query_arg( 'ftg-message', $message, $redirect ) . '#clients';

	wp_safe_redirect( $redirect );
	exit;
} // ftg_premium_send_album_invite_email_action
add_action( 'init', 'ftg_premium_send_album_invite_email_action' );

/**
 * Send the album updated email.
 *
 * @since	1.0
 * @return	void
 */
function ftg_premium_send_album_updated_email_action()	{
	if ( ! isset( $_GET['ftg_action'], $_GET['album_id'], $_GET['client_id'] ) || 'send_album_updated' != $_GET['ftg_action'] )	{
		return;
	}

	$album_id  = absint( $_GET['album_id'] );
	$client_id = absint( $_GET['client_id'] );

	if ( empty( $album_id ) || empty( $client_id ) )	{
		return;
	}

	if ( FTG_CM_NAMESPACE\FTG_CM_Email_Functions::ftg_cm_send_album_updated_email( $album_id, $client_id ) )	{
		$message = 'updated_album_sent';
	} else	{
		$message = 'updated_album_not_sent';
	}

	$redirect = remove_query_arg( array( 'ftg_action', 'album_id', 'ftg-message' ) );
	$redirect = add_query_arg( 'ftg-message', $message, $redirect ) . '#clients';

	wp_safe_redirect( $redirect );
	exit;
} // ftg_premium_send_album_updated_email_action
add_action( 'init', 'ftg_premium_send_album_updated_email_action' );

/**
 * Album Invite Email Template Body.
 *
 * This is the default content sent to the client when their album is ready.
 *
 * @since	1.0
 * @param	int 	$album_id		Album ID
 * @param	int		$user_id		User ID
 * @return	string	$email_body		Body of the email
 */
function ftg_premium_get_album_invite_email_body_content( $album_id = 0, $user_id = 0 ) 	{
	global $ftg_emails;

	$invite_email_body  = __( 'Hey', 'feed-them-gallery-premium' ) . " {name},\n\n";
	$invite_email_body .= __( 'Great news! Your album is ready for you to view.', 'feed-them-gallery-premium' ) . "\n\n";
	$invite_email_body .= __( "You can access your album using the details below. You'll need to set a password first...", 'feed-them-gallery-premium' ) . "\n\n";
	$invite_email_body .= __( 'Album URL:', 'feed-them-gallery-premium' ) . " {album_url}\n\n";
	$invite_email_body .= __( 'Username:', 'feed-them-gallery-premium' ) . " {user_email}\n\n";
	$invite_email_body .= __( 'Password:', 'feed-them-gallery-premium' ) . " {password_reset}\n\n";
	$invite_email_body .= __( 'Regards', 'feed-them-gallery-premium' ) . "\n\n";
	$invite_email_body .= '{sitename}' . "\n\n";

	$email = ftg_get_option( 'album_invite_content', false );
	$email = $email ? stripslashes( $email ) : $invite_email_body;

	$email_body = apply_filters( 'ftg_album_invite_email_content_email_template_wpautop', true ) ? wpautop( $email ) : $email;

	//$email_body = apply_filters( 'ftg_album_invite_email_content_' . $ftg_emails->get_template(), $email_body, $album_id, $user_id );

	return apply_filters( 'ftg_album_invite_email_content', $email_body, $album_id, $user_id );
} // ftg_premium_get_album_invite_email_body_content

/**
 * Album Updated Email Template Body.
 *
 * This is the default content sent to the client when their album is updated.
 *
 * @since	1.0
 * @param	int 	$album_id		Album ID
 * @param	int		$user_id		User ID
 * @return	string	$email_body		Body of the email
 */
function ftg_premium_get_gallery_updated_email_body_content( $album_id = 0, $user_id = 0 ) 	{
	global $ftg_emails;

	$updated_email_body  = __( 'Hey', 'feed-them-gallery-premium' ) . " {name},\n\n";
	$updated_email_body .= __( 'Your album has been updated and is now ready for you to review.', 'feed-them-gallery-premium' ) . "\n\n";
	$updated_email_body .= __( '<a href="{album_url_path}">Click here</a> to access your album.', 'feed-them-gallery-premium' ) . "\n\n";
	$updated_email_body .= __( 'As a reminder, your username is <strong>{user_email}</strong>.', 'feed-them-gallery-premium' ) . " \n\n";
	$updated_email_body .= __( 'Regards', 'feed-them-gallery-premium' ) . "\n\n";
	$updated_email_body .= '{sitename}' . "\n\n";

	$email = ftg_get_option( 'album_updated_content', false );
	$email = $email ? stripslashes( $email ) : $updated_email_body;

	$email_body = apply_filters( 'ftg_album_updated_email_content_email_template_wpautop', true ) ? wpautop( $email ) : $email;

	//$email_body = apply_filters( 'ftg_album_updated_email_content_' . $ftg_emails->get_template(), $email_body, $album_id, $user_id );

	return apply_filters( 'ftg_album_updated_email_content', $email_body, $album_id, $user_id );
} // ftg_premium_get_gallery_updated_email_body_content
