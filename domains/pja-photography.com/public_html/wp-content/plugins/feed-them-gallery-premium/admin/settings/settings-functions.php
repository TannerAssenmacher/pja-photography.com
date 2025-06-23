<?php
/**
 * Settings Page
 *
 * Class Feed Them Gallery Settings Page
 *
 * @class    Settings_Page
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */

use FTG_CM_NAMESPACE\FTG_CM_DB_Client_Meta;

/**
 * Adds the Premium section to the extensions tab in settings.
 *
 * @since   1.0
 * @param   array   $sections   Array of sections
 * @return  array   Array of sections
 */
function ftg_premium_add_premium_settings_sections( $sections ) {
    $sections['extensions']['premium'] = __( 'Premium', 'feed-them-gallery-premium' );

    return $sections;
} // ftg_premium_add_premium_settings_sections
add_filter( 'ftg_settings_sections', 'ftg_premium_add_premium_settings_sections' );

/**
 * Add the email setting sections used with Clients Manager.
 *
 * @since   1.1.5
 * @param   array   $sections   Setting sectionas
 * @return  array   Setting sectionas
 */
function ftg_premium_add_email_setting_sections( $sections )    {
    $sections['album_invite']  = __( 'Album Invite', 'feed-them-gallery-premium' );
    $sections['album_updated'] = __( 'Album Updated', 'feed-them-gallery-premium' );

    return $sections;
} // ftg_premium_add_email_setting_sections
add_filter( 'ftg_settings_sections_emails', 'ftg_premium_add_email_setting_sections' );

/**
 * Register plugin settings.
 *
 * @since   1.0
 * @param   array   $settings   Array of settings
 * @return  array   Array of settings
 */
function ftg_premium_register_settings( $settings ) {
    $settings['extensions']['premium'] = array(
        'woo_header' => array(
            'id'      => 'woo_header',
            'name'    => __( 'WooCommerce Settings', 'feed-them-gallery-premium' ),
            'type'    => 'header'
        ),
        'woo_enable_right_click' => array(
            'id'      => 'woo_enable_right_click',
            'name'    => __( 'Disable Right Click', 'feed-them-gallery-premium' ),
            'type'    => 'checkbox',
            'std'     => 0,
            'desc'    => __( 'If enabled, the right click option will be disabled on all pages of your website.', 'feed-them-gallery-premium' )
        ),
		'woo_product_short_description' => array(
			'id'      => 'woo_product_short_description',
			'name'    => __( 'Product Short Description', 'feed-them-gallery-premium' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'desc'    => __( 'If checked, this will place your image description in the Product Short Description area instead of the main descriptions area.', 'feed-them-gallery-premium' )
		),
        'woo_attch_prod_to_gallery_cat' => array(
            'id'      => 'woo_attch_prod_to_gallery_cat',
            'name'    => __( 'Product Creation', 'feed-them-gallery-premium' ),
            'type'    => 'checkbox',
            'std'     => 0,
            'desc'    => __( 'Do you wish to attach product to a category named after your gallery.', 'feed-them-gallery-premium' )
        ),
        'woo_add_to_cart' => array(
            'id'      => 'woo_add_to_cart',
            'name'    => __( 'Add to Cart Button Action', 'feed-them-gallery-premium' ),
            'type'    => 'radio',
            'options' => array(
                'prod_page'         => __( "Take Customers to product page. (Doesn't add product to cart)", 'feed-them-gallery-premium' ),
                'cart_checkout'     => __( 'Take user directly to checkout. Useful for variable products.', 'feed-them-gallery-premium' ),
                'add_cart'          => __( "Add product to cart. (Doesn't redirect to checkout.) This will not work if your product has required variations.", 'feed-them-gallery-premium' ),
                'add_cart_checkout' => __( 'Add product to cart and take user directly to checkout. This will not work if your product has required variations.', 'feed-them-gallery-premium' )
            ),
            'std'     => 'prod_page'
        )
    );

    return $settings;
} // ftg_premium_register_settings
add_filter( 'ftg_registered_settings', 'ftg_premium_register_settings' );

/**
 * Add email settings for Clients Manager.
 *
 * @since   1.1.5
 * @param   array   $settings   Array of settings
 * @return  array   Array of settings
 */
function ftg_premium_register_email_settings( $settings )   {

    $email_tags = new FTG_CM_NAMESPACE\FTG_CM_Email_Template_Tags();

    $settings['album_invite'] = array(
        'album_invite_subject' => array(
            'id'   => 'album_invite_subject',
            'name' => __( 'Email Subject', 'feed-them-gallery-premium' ),
            'desc' => __( 'Enter the subject line for the album invite email. Template tags accepted.', 'feed-them-gallery-premium' ),
            'type' => 'text',
            'std'  => __( 'Your Album is Ready', 'feed-them-gallery-premium' ),
            'size' => 'large'
        ),
        'album_invite_content' => array(
            'id'   => 'album_invite_content',
            'name' => __( 'Content', 'feed-them-gallery-premium' ),
            'desc' => __( 'Enter the text that is sent as an invite email to clients when their album is ready. HTML is accepted. Available template tags:', 'feed-them-gallery-premium' ) . '<br />' . $email_tags->ftg_cm_get_emails_tags_list(),
            'type' => 'rich_editor',
            'std'  => FTG_CM_NAMESPACE\FTG_CM_Email_Template::ftg_cm_get_album_invite_email_body_content()
        )
    );

    $settings['album_updated'] = array(
        'album_updated_subject' => array(
            'id'   => 'album_updated_subject',
            'name' => __( 'Email Subject', 'feed-them-gallery-premium' ),
            'desc' => __( 'Enter the subject line for the album updated email. Template tags accepted.', 'feed-them-gallery-premium' ),
            'type' => 'text',
            'std'  => __( 'Your Album is Ready', 'feed-them-gallery-premium' ),
            'size' => 'large'
        ),
        'album_updated_content' => array(
            'id'   => 'album_updated_content',
            'name' => __( 'Content', 'feed-them-gallery-premium' ),
            'desc' => __( 'Enter the text that is sent to clients when their album has been updated. HTML is accepted. Available template tags:', 'feed-them-gallery-premium' ) . '<br />' . $email_tags->ftg_cm_get_emails_tags_list(),
            'type' => 'rich_editor',
            'std'  => FTG_CM_NAMESPACE\FTG_CM_Email_Template::ftg_cm_get_album_updated_email_body_content()
        )
    );

    return $settings;
} // ftg_premium_register_email_settings
add_filter( 'ftg_cm_settings_emails', 'ftg_premium_register_email_settings' );
