<?php
/**
 * Template Settings Options Class
 *
 * This class has the options for building and saving on the Custom Meta Boxes
 *
 * @class    Template_Settings_Options
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */
namespace feed_them_gallery;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Gallery_Options
 */
class Template_Settings_Options {

	/**
	 * Holds all the options.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $all_options;

	/**
	 * Template_Settings_Options constructor.
	 */
	public function __construct() {
	}

	/**
	 * All Template Settings Options
	 *
	 * Function to return all Template Settings options
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_all_options() {
		$instance = new self();

		$instance->layout_options();
		$instance->color_options();
		$instance->watermark_options();
		$instance->woocommerce_options();
		$instance->pagination_options();
		$instance->tags_options();

		return $instance->all_options;
	}

	/**
	 * Layout Options
	 *
	 * Options for the Layout Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function layout_options() {
		$this->all_options['layout'] = array(
			'section_attr_key'   => 'facebook_',
			'section_title'      => esc_html__( 'Layout Options', 'feed-them-gallery-premium' ),
			'section_wrap_class' => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'  => 'fb-page-shortcode-form',
			'form_wrap_id'       => 'fts-fb-page-form',
			// Token Check // We'll use these option for premium messages in the future.
			'premium_msg_boxes'  => array(
				'album_videos' => array(
					'req_plugin' => 'fts_premium',
					'msg'        => '',
				),
				'reviews'      => array(
					'req_plugin' => 'facebook_reviews',
					'msg'        => '',
				),
			),

			'main_options'       => array(

				// Gallery Type.
				array(
					'input_wrap_class' => 'ft-wp-gallery-type',
					'option_type'      => 'select',
					'label'            => trim(
						sprintf(
							esc_html__( 'Choose the gallery type%1$s View all Gallery %2$sDemos%3$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'<a href="' . esc_url( 'https://feedthemgallery.com/gallery-demo-one/' ) . '" target="_blank">',
							'</a></small>'
						)
					),
					'type'             => 'text',
					'id'               => 'ft_gallery_type',
					'name'             => 'ft_gallery_type',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => esc_html__( 'Responsive Image Gallery ', 'feed-them-gallery-premium' ),
							'value' => 'gallery',
						),
						array(
							'label' => esc_html__( 'Image Post in Grid (Masonry)', 'feed-them-gallery-premium' ),
							'value' => 'post-in-grid',
						),
					// array(
					// 'label' => esc_html__( 'Image Post', 'feed-them-gallery-premium' ),
					// 'value' => 'post',
					// ),
					// array(
					// 'label' => esc_html__( 'Image Post in Grid (Masonry)', 'feed-them-gallery-premium' ),
					// 'value' => 'post-in-grid',
					// ),.
					),
				),
				// Show Photo Caption.
				array(
					'input_wrap_class' => 'fb-page-description-option-hide',
					'option_type'      => 'select',
					'label'            => esc_html__( 'Show Photo Caption', 'feed-them-gallery-premium' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_photo_caption',
					'name'             => 'ft_gallery_photo_caption',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => esc_html__( 'Title and Description', 'feed-them-gallery-premium' ),
							'value' => 'title_description',
						),
						array(
							'label' => esc_html__( 'Title', 'feed-them-gallery-premium' ),
							'value' => 'title',
						),
						array(
							'label' => esc_html__( 'Description', 'feed-them-gallery-premium' ),
							'value' => 'description',
						),
						array(
							'label' => esc_html__( 'None', 'feed-them-gallery-premium' ),
							'value' => 'none',
						),
					),
				),

                // Photo Caption Placement.
                array(
                    'input_wrap_class' => 'ftg-page-title-description-placement-option-hide',
                    'option_type'      => 'select',
                    'label'            => esc_html__( 'Photo Caption Placement', 'feed-them-gallery-premium' ),
                    'type'             => 'text',
                    'id'               => 'ft_gallery_photo_caption_placement',
                    'name'             => 'ft_gallery_photo_caption_placement',
                    'default_value'    => '',
                    'options'          => array(
                        array(
                            'label' => esc_html__( 'Caption Above Photo', 'feed-them-gallery-premium' ),
                            'value' => 'show_top',
                        ),
                        array(
                            'label' => esc_html__( 'Caption Below Photo', 'feed-them-gallery-premium' ),
                            'value' => 'show_bottom',
                        ),
                    ),
                ),
				// ******************************************
				// Facebook Grid Options
				// ******************************************
				// Facebook Page Display Posts in Grid
				// array(
				// 'grouped_options_title' => __('Grid', 'feed-them-gallery-premium'),
				// 'input_wrap_class' => 'fb-posts-in-grid-option-wrap',
				// 'option_type' => 'select',
				// 'label' => __('Display Posts in Grid', 'feed-them-gallery-premium'),
				// 'type' => 'text',
				// 'id' => 'ft_gallery_grid_option',
				// 'name' => 'ft_gallery_grid_option',
				// 'default_value' => 'no',
				// 'options' => array(
				// array(
				// 'label' => __('No', 'feed-them-gallery-premium'),
				// 'value' => 'no',
				// ),
				// array(
				// 'label' => __('Yes', 'feed-them-gallery-premium'),
				// 'value' => 'yes',
				// ),
				// ),
				// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
				// 'sub_options' => array(
				// 'sub_options_wrap_class' => 'main-grid-options-wrap',
				// ),
				// ),
				array(
					'input_wrap_class'   => 'fb-page-columns-option-hide',
					'option_type'        => 'select',
					'label'              => esc_html__( 'Number of Columns', 'feed-them-gallery-premium' ),
					'type'               => 'text',
					'instructional-text' => sprintf(
						esc_html__( '%1$sNOTE:%2$s Using the Columns option will make this gallery fully responsive and it will adapt in size to your containers width. Choose the Number of Columns and Space between each image below.', 'feed-them-gallery-premium' ),
						'<strong>',
						'</strong>'
					),
					'id'                 => 'ft_gallery_columns',
					'name'               => 'ft_gallery_columns',
					'default_value'      => '4',
					'options'            => array(
						array(
							'label' => esc_html__( '1', 'feed-them-gallery-premium' ),
							'value' => '1',
						),
						array(
							'label' => esc_html__( '2', 'feed-them-gallery-premium' ),
							'value' => '2',
						),
						array(
							'label' => esc_html__( '3', 'feed-them-gallery-premium' ),
							'value' => '3',
						),
						array(
							'label' => esc_html__( '4', 'feed-them-gallery-premium' ),
							'value' => '4',
						),
						array(
							'label' => esc_html__( '5', 'feed-them-gallery-premium' ),
							'value' => '5',
						),
						array(
							'label' => esc_html__( '6', 'feed-them-gallery-premium' ),
							'value' => '6',
						),
						array(
							'label' => esc_html__( '7', 'feed-them-gallery-premium' ),
							'value' => '7',
						),
						array(
							'label' => esc_html__( '8', 'feed-them-gallery-premium' ),
							'value' => '8',
						),
					),
				),
				array(
					'input_wrap_class'   => 'ftg-masonry-columns-option-hide',
					'option_type'        => 'select',
					'label'              => esc_html__( 'Number of Columns', 'feed-them-gallery-premium' ),
					'type'               => 'text',
					'instructional-text' => sprintf(
						esc_html__( '%1$sNOTE:%2$s Using the Columns option will make this gallery fully responsive and it will adapt in size to your containers width. Choose the Number of Columns and Space between each image below.', 'feed-them-gallery-premium' ),
						'<strong>',
						'</strong>'
					),
					'id'                 => 'ft_gallery_columns_masonry2',
					'name'               => 'ft_gallery_columns_masonry2',
					'default_value'      => '3',
					'options'            => array(
						array(
							'label' => esc_html__( '2', 'feed-them-gallery-premium' ),
							'value' => '2',
						),
						array(
							'label' => esc_html__( '3', 'feed-them-gallery-premium' ),
							'value' => '3',
						),
						array(
							'label' => esc_html__( '4', 'feed-them-gallery-premium' ),
							'value' => '4',
						),
						array(
							'label' => esc_html__( '5', 'feed-them-gallery-premium' ),
							'value' => '5',
						),
					),
				),
				array(
					'input_wrap_class' => 'ftg-masonry-columns-option-hide',
					'option_type'      => 'select',
					'label'            => esc_html__( 'Space between Images', 'feed-them-gallery-premium' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_columns_masonry_margin',
					'name'             => 'ft_gallery_columns_masonry_margin',
					'default_value'    => '5',
					'options'          => array(
						array(
							'label' => esc_html__( '1px', 'feed-them-gallery-premium' ),
							'value' => '1',
						),
						array(
							'label' => esc_html__( '2px', 'feed-them-gallery-premium' ),
							'value' => '2',
						),
						array(
							'label' => esc_html__( '3px', 'feed-them-gallery-premium' ),
							'value' => '3',
						),
						array(
							'label' => esc_html__( '4px', 'feed-them-gallery-premium' ),
							'value' => '4',
						),
						array(
							'label' => esc_html__( '5px', 'feed-them-gallery-premium' ),
							'value' => '5',
						),
						array(
							'label' => esc_html__( '10px', 'feed-them-gallery-premium' ),
							'value' => '10',
						),
						array(
							'label' => esc_html__( '15px', 'feed-them-gallery-premium' ),
							'value' => '15',
						),
						array(
							'label' => esc_html__( '20px', 'feed-them-gallery-premium' ),
							'value' => '20',
						),
					),
				),
				array(
					'input_wrap_class' => 'fb-page-columns-option-hide',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							esc_html__( 'Force Columns%1$s Yes, will force image columns. No, will allow the images to be resposive for smaller devices%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_force_columns',
					'name'             => 'ft_gallery_force_columns',
					'default_value'    => '',
					'options'          => array(
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),

					),
				),
				// Grid Column Width
				// array(
				// 'input_wrap_class' => 'fb-page-grid-option-hide fb-page-columns-option-hide ftg-hide-for-columns',
				// 'option_type' => 'input',
				// 'label' => __('Grid Column Width', 'feed-them-gallery-premium'),
				// 'type' => 'text',
				// 'id' => 'ft_gallery_grid_column_width',
				// 'name' => 'ft_gallery_grid_column_width',
				// 'instructional-text' =>
				// sprintf(__('%1$sNOTE:%2$s Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery-premium'),
				// '<strong>',
				// '</strong>'
				// ),
				// 'placeholder' => '310px ' . __('for example', 'feed-them-gallery-premium'),
				// 'default_value' => '310px',
				// 'value' => '',
					   // This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
					   // 'sub_options' => array(
					   // 'sub_options_wrap_class' => 'fts-facebook-grid-options-wrap',
					   // ),
				// ),
				   // Grid Spaces Between Posts.
				   array(
					   'input_wrap_class' => 'fb-page-grid-option-hide fb-page-grid-option-border-bottom',
					   'option_type'      => 'input',
					   'label'            => esc_html__( 'Space between Images', 'feed-them-gallery-premium' ),
					   'type'             => 'text',
					   'id'               => 'ft_gallery_grid_space_between_posts',
					   'name'             => 'ft_gallery_grid_space_between_posts',
					   'placeholder'      => '1px ' . esc_html__( 'for example', 'feed-them-gallery-premium' ),
					   'default_value'    => '1px',
					   // 'sub_options_end' => 2,
				   ),
				// Show Name.
				array(
					'input_wrap_class' => 'ft-gallery-user-name',
					'option_type'      => 'input',
					'label'            =>
						sprintf(
							esc_html__( 'User Name%1$s Company or user who took this photo%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_username',
					'name'             => 'ft_gallery_username',
					'placeholder'      => '',
					'default_value'    => '',
				),
				// Show Name Link.
				array(
					'option_type'   => 'input',
					'label'         =>
						sprintf(
							esc_html__( 'User Custom Link%1$s Custom about page or social media page link%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_user_link',
					'name'          => 'ft_gallery_user_link',
					'placeholder'   => '',
					'default_value' => '',
				),
				// Show Share.
				array(
					'input_wrap_class' => 'ft-gallery-share',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							esc_html__( 'Show Share Options%1$s Appears in the bottom left corner and in popup%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_wp_share',
					'name'             => 'ft_gallery_wp_share',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
					),
				),
				// Show Date.
				array(
					'input_wrap_class' => 'ft-gallery-date',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							esc_html__( 'Show Date%1$s Date image was uploaded%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_wp_date',
					'name'             => 'ft_gallery_wp_date',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
					),
				),
				// Show Icon.
				array(
					'input_wrap_class' => 'ft-gallery-icon',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							esc_html__( 'Show Wordpress Icon%1$s Appears in the top left corner%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_wp_icon',
					'name'             => 'ft_gallery_wp_icon',
					'default_value'    => 'no',
					'options'          => array(
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
					),
				),

				// Words per photo caption
				// array(
				// 'option_type' => 'input',
				// 'label' => __('# of words per photo caption', 'feed-them-gallery-premium') . '<br/><small>' . __('Typing 0 removes the photo caption', 'feed-them-gallery-premium') . '</small>',
				// 'type' => 'hidden',
				// 'id' => 'ft_gallery_word_count_option',
				// 'name' => 'ft_gallery_word_count_option',
				// 'placeholder' => '',
				// 'default_value' => '',
				// ),
				// Image Sizes on page.
				array(
					'input_wrap_class'   => 'ft-images-sizes-page',
					'option_type'        => 'ft-images-sizes-page',
					'instructional-text' =>
						sprintf(
							esc_html__( '%1$sNOTE:%2$s If for some reason the image size you choose does not appear on the front end you may need to regenerate your images. This free plugin called %3$sRegenerate Thumbnails%4$s does an amazing job of that.', 'feed-them-gallery-premium' ),
							'<strong>',
							'</strong>',
							'<a href="' . esc_url( 'plugin-install.php?s=regenerate+thumbnails&tab=search&type=term' ) . '" target="_blank">',
							'</a>'
						),
					'label'              => esc_html__( 'Image Size on Page', 'feed-them-gallery-premium' ),
					'class'              => 'ft-gallery-images-sizes-page',
					'type'               => 'select',
					'id'                 => 'ft_gallery_images_sizes_page',
					'name'               => 'ft_gallery_images_sizes_page',
					'default_value'      => 'medium',
					'placeholder'        => '',
					'autocomplete'       => 'off',
				),

				// Max-width for Images & Videos.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Max-width for Images', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_max_image_vid_width',
					'name'          => 'ft_gallery_max_image_vid_width',
					'placeholder'   => '500px',
					'default_value' => '',
				),
				// Gallery Width.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Gallery Max-width', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_width',
					'name'          => 'ft_gallery_width',
					'placeholder'   => '500px',
					'default_value' => '',
				),
				// Gallery Height for scrolling feeds using Post format only, this does not work for grid or gallery options except gallery squared because it does not use masonry. For all others it will be hidden.
				array(
					'input_wrap_class' => 'ft-gallery-height',
					'option_type'      => 'input',
					'label'            =>
						sprintf(
							esc_html__( 'Gallery Height%1$s Set the height to have a scrolling feed. Only works for Responsive Image Gallery and the Image Post option.%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_height',
					'name'             => 'ft_gallery_height',
					'placeholder'      => '600px',
					'default_value'    => '',
				),
				// Gallery Margin.
				array(
					'option_type'   => 'input',
					'label'         =>
						sprintf(
							esc_html__( 'Gallery Margin%1$s To center feed type auto%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_margin',
					'name'          => 'ft_gallery_margin',
					'placeholder'   => 'auto',
					'default_value' => 'auto',
				),
				// Gallery Padding.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Gallery Padding', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_padding',
					'name'          => 'ft_gallery_padding',
					'placeholder'   => '10px',
					'default_value' => '',
				),
				// ******************************************
				// Gallery Popup
				// ******************************************
				// Display Photos in Popup
				array(
					'grouped_options_title' => esc_html__( 'Popup', 'feed-them-gallery-premium' ),
					'option_type'           => 'select',
					'label'                 => esc_html__( 'Display Photos in Popup', 'feed-them-gallery-premium' ),
					'type'                  => 'text',
					'id'                    => 'ft_gallery_popup',
					'name'                  => 'ft_gallery_popup',
					'default_value'         => 'yes',
					'options'               => array(
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
					),
					'sub_options'           => array(
						'sub_options_wrap_class' => 'facebook-popup-wrap',
					),
					'sub_options_end'       => true,
				),
				// Image Sizes in popup.
				array(
					'input_wrap_class'   => 'ft-images-sizes-popup',
					'option_type'        => 'ft-images-sizes-popup',
					'instructional-text' =>
						sprintf(
							esc_html__( '%1$sNOTE:%2$s If for some reason the image size you choose does not appear on the front end you may need to regenerate your images. This free plugin called %3$sRegenerate Thumbnails%4$s does an amazing job of that.', 'feed-them-gallery-premium' ),
							'<strong>',
							'</strong>',
							'<a href="' . esc_url( 'plugin-install.php?s=regenerate+thumbnails&tab=search&type=term' ) . '" target="_blank">',
							'</a>'
						),
					'label'              => esc_html__( 'Image Size in Popup', 'feed-them-gallery-premium' ),
					'class'              => 'ft-gallery-images-sizes-popup',
					'type'               => 'select',
					'id'                 => 'ft_gallery_images_sizes_popup',
					'name'               => 'ft_gallery_images_sizes_popup',
					'default_value'      => '',
					'placeholder'        => '',
					'autocomplete'       => 'off',
				),
				array(
					'input_wrap_class' => 'ft-popup-display-options',
					'option_type'      => 'select',
					'label'            => esc_html__( 'Popup Options', 'feed-them-gallery-premium' ),
					'type'             => 'text',
					'id'               => 'ft_popup_display_options',
					'name'             => 'ft_popup_display_options',
					'default_value'    => 'no',
					'options'          => array(
						array(
							'label' => esc_html__( 'Default', 'feed-them-gallery-premium' ),
							'value' => 'default',
						),
						array(
							'label' => esc_html__( 'Full Width & Info below Photo', 'feed-them-gallery-premium' ),
							'value' => 'full-width-second-half-bottom',
						),
						array(
							'label' => esc_html__( 'Full Width, Photo Only', 'feed-them-gallery-premium' ),
							'value' => 'full-width-photo-only',
						),
					),
				),

				// ******************************************
				// Gallery Image Count Options
				// ******************************************
				// Load More Style
				array(
					'option_type'        => 'select',
					'label'              => esc_html__( 'Show Image Count', 'feed-them-gallery-premium' ),
					'type'               => 'text',
					'id'                 => 'ft_gallery_show_pagination',
					'name'               => 'ft_gallery_show_pagination',
					'instructional-text' =>
						sprintf(
							esc_html__( '%1$sNOTE:%2$s This will display the number of images you have in your gallery, and will appear centered at the bottom of your image feed. For Example: 4 of 50 (4 being the number of images you have loaded on the page already and 50 being the total number of images in the gallery.', 'feed-them-gallery-premium' ),
							'<strong>',
							'</strong>'
						),
					'default_value'      => 'yes',
					'options'            => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
						2 => array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
					),
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output).
					'sub_options'        => array(
						'sub_options_wrap_class' => 'fts-facebook-load-more-options-wrap',
					),
					'sub_options_end'    => true,
				),

				// ******************************************
				// Gallery Sort Options
				// ******************************************
				array(
					'grouped_options_title' => esc_html__( 'Order of Images', 'feed-them-gallery-premium' ),
					'option_type'           => 'select',
					'label'                 => esc_html__( 'Choose the order of Images', 'feed-them-gallery-premium' ),
					'type'                  => 'text',
					'id'                    => 'ftg_sort_type',
					'name'                  => 'ftg_sort_type',
					'default_value'         => 'above-below',
					'options'               => array(
						1 => array(
							'label' => esc_html__( 'The order you manually sorted images', 'feed-them-gallery-premium' ),
							'value' => 'menu_order',
						),
						2 => array(
							'label' => esc_html__( 'Sort alphabetically (A-Z)', 'feed-them-gallery-premium' ),
							'value' => 'title',
						),
						3 => array(
							'label' => esc_html__( 'Sort by date', 'feed-them-gallery-premium' ),
							'value' => 'date',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Display Options', 'feed-them-gallery-premium' ),
					'label'         =>
						sprintf(
							esc_html__( 'Display Options%1$s Display a select option for this gallery so your users can select the sort order.%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ftg_sorting_options',
					'name'          => 'ftg_sorting_options',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Position of Select Option', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ftg_position_of_sort_select',
					'name'          => 'ftg_position_of_sort_select',
					'default_value' => 'above-below',
					'options'       => array(
						1 => array(
							'label' => esc_html__( 'Top', 'feed-them-gallery-premium' ),
							'value' => 'above',
						),
						2 => array(
							'label' => esc_html__( 'Bottom', 'feed-them-gallery-premium' ),
							'value' => 'below',
						),
						3 => array(
							'label' => esc_html__( 'Top and Bottom', 'feed-them-gallery-premium' ),
							'value' => 'above-below',
						),
					),
					'sub_options'   => array(
						'sub_options_wrap_class' => 'ftg-sorting-options-wrap',
					),
				),

				array(
					'option_type'     => 'select',
					'label'           => esc_html__( 'Align Select Option', 'feed-them-gallery-premium' ),
					'type'            => 'text',
					'id'              => 'ftg_align_sort_select',
					'name'            => 'ftg_align_sort_select',
					'default_value'   => 'left',
					'options'         => array(
						1 => array(
							'label' => esc_html__( 'Left', 'feed-them-gallery-premium' ),
							'value' => 'left',
						),
						2 => array(
							'label' => esc_html__( 'Right', 'feed-them-gallery-premium' ),
							'value' => 'right',
						),
					),
					'sub_options_end' => true,
				),

				// ******************************************
				// Download Free Image Button Sort Options
				// ******************************************
				array(
					'grouped_options_title' => esc_html__( 'Free Image Download', 'feed-them-gallery-premium' ),
					'option_type'           => 'ftg-free-download-size',
					'instructional-text'    =>
						sprintf(
							esc_html__( '%1$sNOTE:%2$s To turn this option on simply choose an image size. A download icon will appear under the image to the right and in the popup. If for some reason the image size you choose does not appear on the front end you may need to regenerate your images. This free plugin called %3$sRegenerate Thumbnails%4$s does an amazing job of that.', 'feed-them-gallery-premium' ),
							'<strong>',
							'</strong>',
							'<a href="' . esc_url( 'plugin-install.php?s=regenerate+thumbnails&tab=search&type=term' ) . '" target="_blank">',
							'</a>'
						),
					'label'                 => esc_html__( 'Choose the size', 'feed-them-gallery-premium' ),
					'class'                 => 'ft-images-sizes-free-download-button',
					'type'                  => 'select',
					'id'                    => 'ftg_free_download_size',
					'name'                  => 'ftg_free_download_size',
					'default_value'         => '',
					'placeholder'           => '',
					'autocomplete'          => 'off',
				),

				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Free Download Text', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_free_download_text',
					'name'          => 'ft_gallery_free_download_text',
					'placeholder'   => 'Free Download',
					'default_value' => '',
					'value'         => '',
				),

			),

		);

		return $this->all_options['layout'];
	} //END LAYOUT OPTIONS

	/**
	 * Color Options
	 *
	 * Options for the Color Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function color_options() {
		$this->all_options['colors'] = array(
			'section_attr_key'   => 'facebook_',
			'section_title'      => esc_html__( 'Feed Color Options', 'feed-them-gallery-premium' ),
			'section_wrap_class' => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'  => 'fb-page-shortcode-form',
			'form_wrap_id'       => 'fts-fb-page-form',
			'main_options'       => array(

				// Feed Background Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Background Color', 'feed-them-gallery-premium' ),
					'class'         => 'ft-gallery-feed-background-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-feed-background-color-input',
					'name'          => 'ft_gallery_feed_background_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				// Feed Grid Background Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Grid Posts Background Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-feed-grid-posts-background-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-grid-posts-background-color-input',
					'name'          => 'ft_gallery_grid_posts_background_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				// Border Bottom Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Border Bottom Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-border-bottom-color-input',
					'name'          => 'ft_gallery_border_bottom_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),

				// Albums Font Size.
				array(
					'grouped_options_title' => esc_html__( 'Gallery Options Only', 'feed-them-gallery-premium' ),
					'option_type'           => 'input',
					'label'                 => esc_html__( 'Text Size', 'feed-them-gallery-premium' ),
					'type'                  => 'text',
					'id'                    => 'ft-album-link-size',
					'name'                  => 'ft_album_link_size',
					'default_value'         => '',
					'placeholder'           => esc_html__( '13px', 'feed-them-gallery-premium' ),
					'autocomplete'          => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Text Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-album-link-color',
					'name'          => 'ft_album_link_color',
					'default_value' => '#fff',
					'placeholder'   => esc_html__( '#fff', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),

				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Text Weight', 'feed-them-gallery-premium' ),

					'type'          => 'text',
					'id'            => 'ft-album-text-weight',
					'name'          => 'ft_album_text_weight',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => esc_html__( 'Bold', 'feed-them-gallery-premium' ),
							'value' => 'bold',
						),
						array(
							'label' => esc_html__( 'Normal', 'feed-them-gallery-premium' ),
							'value' => 'normal',
						),
					),
				),
				// Albums Text Hover Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Hover Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-album-link-hover-color',
					'name'          => 'ft_album_link_hover_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Align Link', 'feed-them-gallery-premium' ),

					'type'          => 'text',
					'id'            => 'ft-album-align-text',
					'name'          => 'ft_album_align_text',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => esc_html__( 'Center', 'feed-them-gallery-premium' ),
							'value' => 'center',
						),
						array(
							'label' => esc_html__( 'Left', 'feed-them-gallery-premium' ),
							'value' => 'left',
						),
						array(
							'label' => esc_html__( 'Right', 'feed-them-gallery-premium' ),
							'value' => 'right',
						),
					),
				),
				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Position Link', 'feed-them-gallery-premium' ),

					'type'          => 'text',
					'id'            => 'ft-album-position-text',
					'name'          => 'ft_album_position_text',
					'default_value' => 'bottom',
					'options'       => array(
						array(
							'label' => esc_html__( 'Bottom', 'feed-them-gallery-premium' ),
							'value' => 'bottom',
						),
						array(
							'label' => esc_html__( 'Top', 'feed-them-gallery-premium' ),
							'value' => 'top',
						),
						array(
							'label' => esc_html__( 'Middle (Does not work with Masonry Layout)', 'feed-them-gallery-premium' ),
							'value' => 'middle',
						),
					),
				),

				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Text Padding', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-album-link-padding',
					'name'          => 'ft_album_link_padding',
					'default_value' => '10px',
					'placeholder'   => esc_html__( '10px', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				// Albums Background Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Background Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-album-link-background-color',
					'name'          => 'ft_album_link_background_color',
					'default_value' => '#000000',
					'placeholder'   => esc_html__( '#000000', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Background Transparency', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-album-background-opacity',
					'name'          => 'ft_album_background_opacity',
					'default_value' => '.5',
					'options'       => array(
						array(
							'label' => esc_html__( 'None', 'feed-them-gallery-premium' ),
							'value' => '1',
						),
						array(
							'label' => esc_html__( 'Hide Background Color', 'feed-them-gallery-premium' ),
							'value' => '.0',
						),
						array(
							'label' => esc_html__( '1 ( Very Transparent )', 'feed-them-gallery-premium' ),
							'value' => '.1',
						),
						array(
							'label' => esc_html__( '2', 'feed-them-gallery-premium' ),
							'value' => '.2',
						),

						array(
							'label' => esc_html__( '3', 'feed-them-gallery-premium' ),
							'value' => '.3',
						),

						array(
							'label' => esc_html__( '4', 'feed-them-gallery-premium' ),
							'value' => '.4',
						),

						array(
							'label' => esc_html__( '5 ( Default )', 'feed-them-gallery-premium' ),
							'value' => '.5',
						),

						array(
							'label' => esc_html__( '6', 'feed-them-gallery-premium' ),
							'value' => '.6',
						),

						array(
							'label' => esc_html__( '7', 'feed-them-gallery-premium' ),
							'value' => '.7',
						),

						array(
							'label' => esc_html__( '8', 'feed-them-gallery-premium' ),
							'value' => '.8',
						),

						array(
							'label' => esc_html__( '9', 'feed-them-gallery-premium' ),
							'value' => '.9',
						),

						array(
							'label' => esc_html__( '10 ( No Transparency )', 'feed-them-gallery-premium' ),
							'value' => '1',
						),
					),
				),

			),
		);

		return $this->all_options['colors'];
	} //END LAYOUT OPTIONS

    /**
     * Woocommerce Options
     *
     * Options for the Woocommerce Tab
     *
     * @return mixed
     * @since 1.0.0
     */
    public function woocommerce_options() {

        $this->all_options['woocommerce'] = array(
            // required_prem_plugin must match the array key returned in ft_gallery_required_plugins function.
            'required_prem_plugin' => 'feed_them_gallery_premium',
            'input_wrap_class'     => 'ft-woocommerce-styles',
            'section_attr_key'     => 'woocommerce_',
            'section_title'        => esc_html__( 'Woocommerce Options', 'feed-them-gallery-premium' ),
            'section_wrap_class'   => 'ftg-section-options',
            // Form Info.
            'form_wrap_classes'    => 'fb-page-shortcode-form',
            'form_wrap_id'         => 'fts-fb-page-form',
            'main_options'         => array(
                // Show Purchase Button.
                array(
                    'input_wrap_class' => 'ft-gallery-purchase-link',
                    'option_type'      => 'select',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Show Cart Icon%1$s Appears on the page and popup. Only appears in popup for the Responsive Image Gallery.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'type'             => 'text',
                    'id'               => 'ft_gallery_purchase_link',
                    'name'             => 'ft_gallery_purchase_link',
                    'default_value'    => 'yes',
                    'options'          => array(
                        array(
                            'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
                            'value' => 'yes',
                        ),
                        array(
                            'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
                            'value' => 'no',
                        ),
                    ),
                ),

                // Purchase Button Text.
                array(
                    'option_type'   => 'input',
                    'label'         =>
                        sprintf(
                            esc_html__( 'Cart Icon Purchase Link text%1$s The default word is Purchase or add a single space to show the Cart Icon only.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'type'          => 'text',
                    'id'            => 'ft_gallery_purchase_word',
                    'name'          => 'ft_gallery_purchase_word',
                    'placeholder'   => esc_html__( 'Purchase', 'feed-them-gallery-premium' ),
                    'default_value' => '',
                ),

                // Show or hide the WooCommerce Variations/ add to cart button.
                array(
                    'input_wrap_class' => 'ft-gallery-hide-add-to-cart',
                    'option_type'      => 'select',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Hide Add to Cart Button%1$s This will hide the add to cart button and any variations on the page and popup so you can direct users to your default shop product.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'type'             => 'text',
                    'id'               => 'ft_gallery_hide_add_to_cart',
                    'name'             => 'ft_gallery_hide_add_to_cart',
                    'default_value'    => 'yes',
                    'options'          => array(
                        array(
                            'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
                            'value' => 'no',
                        ),
                        array(
                            'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
                            'value' => 'yes',
                        ),
                    ),
                ),

                // Show or hide the WooCommerce Variations/ add to cart button.
                array(
                    'input_wrap_class' => 'ft-gallery-hide-add-to-cart-over-image',
                    'option_type'      => 'select',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Show Cart Icon Over Image%1$s Only works for Responsive Image Galleries. This will show a cart icon over top of the image.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'type'             => 'text',
                    'id'               => 'ft_gallery_show_add_to_cart_over_image',
                    'name'             => 'ft_gallery_show_add_to_cart_over_image',
                    'default_value'    => 'yes',
                    'options'          => array(
                        array(
                            'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
                            'value' => 'no',
                        ),
                        array(
                            'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
                            'value' => 'yes',
                        ),
                    ),
                ),

                // Show or hide the WooCommerce Variations/ add to cart button.
                array(
                    'input_wrap_class' => 'ft-gallery-hide-position-add-to-cart-over-image',
                    'option_type'      => 'select',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Position the Cart Icon Over Image%1$sChoose the positioning of the Cart Icon over images.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'type'             => 'text',
                    'id'               => 'ft_gallery_position_add_to_cart_over_image',
                    'name'             => 'ft_gallery_position_add_to_cart_over_image',
                    'default_value'    => 'yes',
                    'options'          => array(
                        array(
                            'label' => esc_html__( 'Top Left', 'feed-them-gallery-premium' ),
                            'value' => 'top-left',
                        ),
                        array(
                            'label' => esc_html__( 'Top Center', 'feed-them-gallery-premium' ),
                            'value' => 'top-center',
                        ),
                        array(
                            'label' => esc_html__( 'Top Right', 'feed-them-gallery-premium' ),
                            'value' => 'top-right',
                        ),
                        array(
                            'label' => esc_html__( 'Middle Right', 'feed-them-gallery-premium' ),
                            'value' => 'middle-right',
                        ),
                        array(
                            'label' => esc_html__( 'Bottom Right', 'feed-them-gallery-premium' ),
                            'value' => 'bottom-right',
                        ),
                        array(
                            'label' => esc_html__( 'Bottom Center', 'feed-them-gallery-premium' ),
                            'value' => 'bottom-center',
                        ),
                        array(
                            'label' => esc_html__( 'Bottom Left', 'feed-them-gallery-premium' ),
                            'value' => 'bottom-left',
                        ),
                        array(
                            'label' => esc_html__( 'Middle Left', 'feed-them-gallery-premium' ),
                            'value' => 'middle-left',
                        ),
                    ),
                ),

                // Popup or use add to cart options from Settings page.
                array(
                    'input_wrap_class' => 'ft-gallery-hide-popup-or-add-to-cart-link',
                    'option_type'      => 'select',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Popup or Add to Cart link%1$s Choose to open the popup or take the user to your product or cart. Add to cart link options are on the Settings page of our plugin.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'type'             => 'text',
                    'id'               => 'ft_gallery_popup_or_add_to_cart_link',
                    'name'             => 'ft_gallery_popup_or_add_to_cart_link',
                    'default_value'    => 'yes',
                    'options'          => array(
                        array(
                            'label' => esc_html__( 'Popup', 'feed-them-gallery-premium' ),
                            'value' => 'popup',
                        ),
                        array(
                            'label' => esc_html__( 'Add to Cart Link', 'feed-them-gallery-premium' ),
                            'value' => 'add-to-cart',
                        ),
                    ),
                ),

                // Icon Background Color.
                array(
                    'input_wrap_class' => 'ft-gallery-hide-icon-background-color',
                    'option_type'      => 'input',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Cart Icon Background Color%1$sBackground Color for is only for the Cart Icon over responsive gallery image.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'class'            => 'ftg-woo-icon-background-color color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
                    'type'             => 'text',
                    'id'               => 'ftg-woo-icon-background-color-input',
                    'name'             => 'ftg_woo_icon_background_color',
                    'default_value'    => '',
                    'placeholder'      => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
                    'autocomplete'     => 'off',
                ),

                // Icon Text Color.
                array(
                    'input_wrap_class' => 'ft-gallery-hide-icon-color',
                    'option_type'      => 'input',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Cart Icon Color%1$sColor is only for the Cart Icon over responsive gallery image.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'class'            => 'ftg-woo-icon-color color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
                    'type'             => 'text',
                    'id'               => 'ftg-woo-icon-color-input',
                    'name'             => 'ftg_woo_icon_color',
                    'default_value'    => '',
                    'placeholder'      => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
                    'autocomplete'     => 'off',
                ),

                // Cart icon Hover Color.
                array(
                    'input_wrap_class' => 'ft-gallery-hide-icon-hover-color',
                    'option_type'      => 'input',
                    'label'            =>
                        sprintf(
                            esc_html__( 'Cart Icon Hover Color%1$sHover Color is only for the Cart Icon over responsive gallery image.%2$s', 'feed-them-gallery-premium' ),
                            '<br/><small>',
                            '</small>'
                        ),
                    'class'            => 'ftg-woo-icon-hover-color color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
                    'type'             => 'text',
                    'id'               => 'ftg-woo-icon-hover-color-input',
                    'name'             => 'ftg_woo_icon_hover_color',
                    'default_value'    => '',
                    'placeholder'      => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
                    'autocomplete'     => 'off',
                ),

            ),
        );

        return $this->all_options['woocommerce'];
    }

	/**
	 * Watermark Options
	 *
	 * Options for the Watermark Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function watermark_options() {
		$this->all_options['watermark'] = array(
			// required_prem_plugin must match the array key returned in ft_gallery_required_plugins function.
			'required_prem_plugin' => 'feed_them_gallery_premium',
			'section_attr_key'     => 'facebook_',
			'section_title'        => esc_html__( 'Watermark Options', 'feed-them-gallery-premium' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
			'main_options'         => array(
				// Disable Right Click.
				array(
					'input_wrap_class'   => 'ft-watermark-disable-right-click',
					'instructional-text' =>
						sprintf(
							esc_html__( '%1$sNOTE:%2$s This option will disable the right click option on desktop computers so people cannot look at the source code. This is not fail safe but for the vast majority this is enough to deter people from trying to find the image source.', 'feed-them-gallery-premium' ),
							'<strong>',
							'</strong>'
						),
					'option_type'        => 'select',
					'label'              => esc_html__( 'Disable Right Click', 'feed-them-gallery-premium' ),
					'type'               => 'text',
					'id'                 => 'ft_gallery_watermark_disable_right_click',
					'name'               => 'ft_gallery_watermark_disable_right_click',
					'default_value'      => '',
					'options'            => array(
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
					),
				),
				// Use Watermark Options.
				array(
					'input_wrap_class' => 'ft-watermark-enable-options',
					'option_type'      => 'select',
					'label'            => esc_html__( 'Use Options Below', 'feed-them-gallery-premium' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_watermark_enable_options',
					'name'             => 'ft_gallery_watermark_enable_options',
					'default_value'    => 'no',
					'options'          => array(
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
					),
				),

				// Choose Watermark Image.
				array(
					'option_type'        => 'input',
					'instructional-text' =>
						sprintf(
							esc_html__( '%1$sNOTE:%2$s These option only allow a watermark overlay. If you need to permanently watermark your images please do so in your gallery. Upload the exact image size you want to display, we will not rescale the image in anyway.', 'feed-them-gallery-premium' ),
							'<strong>',
							'</strong>'
						),
					'label'              => esc_html__( 'Watermark Image', 'feed-them-gallery-premium' ),
					'id'                 => 'ft-watermark-image',
					'name'               => 'ft-watermark-image',
					'class'              => '',
					'type'               => 'button',
					'default_value'      => esc_html__( 'Upload or Choose Watermark', 'feed-them-gallery-premium' ),
					'placeholder'        => '',
					'value'              => '',
					'autocomplete'       => 'off',
				),
				// Watermark Image Link for front end if user does not use imagick or GD library method.
				array(
					'input_wrap_class' => 'ft-watermark-hide-these-options',
					'option_type'      => 'input',
					// 'label' => __('Watermark Image', 'feed-them-gallery-premium'),
					// 'class' => 'fb-link-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',.
					'type'             => 'hidden',
					'id'               => 'ft_watermark_image_input',
					// 'instructional-text' => '<strong>' . __('NOTE:', 'feed-them-gallery-premium') . '</strong> ' . __('Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery-premium'),.
					'name'             => 'ft_watermark_image_input',
					'default_value'    => '',
					// 'placeholder' => __('', 'feed-them-gallery-premium'),
					'autocomplete'     => 'off',
				),
				// Watermark Image ID so we can pass it to merge the watermark over images.
				array(
					'input_wrap_class' => 'ft-watermark-hide-these-options',
					'option_type'      => 'input',
					// 'label' => __('Watermark Image', 'feed-them-gallery-premium'),
					// 'class' => 'fb-link-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',.
					'type'             => 'hidden',
					'id'               => 'ft_watermark_image_id',
					// 'instructional-text' => '<strong>' . __('NOTE:', 'feed-them-gallery-premium') . '</strong> ' . __('Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery-premium'),.
					'name'             => 'ft_watermark_image_id',
					'default_value'    => '',
					// 'placeholder' => __('', 'feed-them-gallery-premium'),.
					'autocomplete'     => 'off',
				),

				// Watermark Options.
				array(
					'input_wrap_class' => 'ft-watermark-enabled',
					'option_type'      => 'select',
					'label'            => esc_html__( 'Watermark Type', 'feed-them-gallery-premium' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_watermark',
					'name'             => 'ft_gallery_watermark',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => esc_html__( 'Watermark Overlay Image (Does not Imprint logo on Image)', 'feed-them-gallery-premium' ),
							'value' => 'overlay',
						),
					),
				),

				// Watermark Options.
				array(
					'input_wrap_class' => 'ft-watermark-overlay-options',
					'option_type'      => 'select',
					'label'            => esc_html__( 'Overlay Options', 'feed-them-gallery-premium' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_watermark',
					'name'             => 'ft_gallery_watermark_overlay_enable',
					'default_value'    => 'popup-only',
					'options'          => array(
						array(
							'label' => esc_html__( 'Select an Option', 'feed-them-gallery-premium' ),
							'value' => '',
						),
						array(
							'label' => esc_html__( 'Watermark in popup only', 'feed-them-gallery-premium' ),
							'value' => 'popup-only',
						),
						array(
							'label' => esc_html__( 'Watermark for image on page only', 'feed-them-gallery-premium' ),
							'value' => 'page-only',
						),
						array(
							'label' => esc_html__( 'Watermark for image on page and popup', 'feed-them-gallery-premium' ),
							'value' => 'page-and-popup',
						),
					),
				),

				// Watermark Opacity.
				array(
					'input_wrap_class' => 'ft-gallery-watermark-opacity',
					'option_type'      => 'input',
					'label'            => esc_html__( 'Image Opacity', 'feed-them-gallery-premium' ),
					'class'            => 'ft-watermark-opacity',
					'type'             => 'text',
					'id'               => 'ft_watermark_image_opacity',
					'name'             => 'ft_watermark_image_opacity',
					'default_value'    => '',
					'placeholder'      => esc_html__( '.5 for example', 'feed-them-gallery-premium' ),
					'autocomplete'     => 'off',
				),
				// Watermark Position.
				array(
					'input_wrap_class' => 'ft-watermark-position',
					'option_type'      => 'select',
					'label'            => esc_html__( 'Watermark Position', 'feed-them-gallery-premium' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_position',
					'name'             => 'ft_gallery_position',
					'default_value'    => 'bottom-right',
					'options'          => array(
						array(
							'label' => esc_html__( 'Centered', 'feed-them-gallery-premium' ),
							'value' => 'center',
						),
						array(
							'label' => esc_html__( 'Top Right', 'feed-them-gallery-premium' ),
							'value' => 'top-right',
						),
						array(
							'label' => esc_html__( 'Top Left', 'feed-them-gallery-premium' ),
							'value' => 'top-left',
						),
						array(
							'label' => esc_html__( 'Top Center', 'feed-them-gallery-premium' ),
							'value' => 'top-center',
						),
						array(
							'label' => esc_html__( 'Bottom Right', 'feed-them-gallery-premium' ),
							'value' => 'bottom-right',
						),
						array(
							'label' => esc_html__( 'Bottom Left', 'feed-them-gallery-premium' ),
							'value' => 'bottom-left',
						),
						array(
							'label' => esc_html__( 'Bottom Center', 'feed-them-gallery-premium' ),
							'value' => 'bottom-center',
						),
					),
				),
				// watermark Image Margin.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Watermark Margin', 'feed-them-gallery-premium' ),
					'class'         => 'ft-watermark-image-margin',
					'type'          => 'text',
					'id'            => 'ft_watermark_image_margin',
					'name'          => 'ft_watermark_image_margin',
					'default_value' => '',
					'placeholder'   => esc_html__( '10px', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
			),
		);

		return $this->all_options['watermark'];
	}
	// END WATERMARK OPTIONS.

	/**
	 * Pagination Options
	 *
	 * Options for the Pagination Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function pagination_options() {
		$this->all_options['pagination'] = array(
			'required_prem_plugin' => 'feed_them_gallery_premium',
			'section_attr_key'     => 'facebook_',
			'section_title'        => esc_html__( 'Pagination', 'feed-them-gallery-premium' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
			// Token Check // We'll use these option for premium messages in the future.
			'premium_msg_boxes'    => array(
				'album_videos' => array(
					'req_plugin' => 'fts_premium',
					'msg'        => '',
				),
				'reviews'      => array(
					'req_plugin' => 'facebook_reviews',
					'msg'        => '',
				),
			),

			'main_options'         => array(

				// ******************************************
				// Gallery Pagination Options
				// ******************************************
				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Display Pagination', 'feed-them-gallery-premium' ),
					'label'         =>
						sprintf(
							esc_html__( 'Display Pagination%1$s Pagination unavailable while using the Load More option.%2$s', 'feed-them-gallery-premium' ),
							'<br/><small class="ftg-pagination-notice-colored" style="display: none;">',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_show_true_pagination',
					'name'          => 'ft_gallery_show_true_pagination',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
					),
				),

				// # of Photos.
				array(

					'option_type'   => 'input',
					'label'         => esc_html__( '# of Photos Visible', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_pagination_photo_count',
					'name'          => 'ft_gallery_pagination_photo_count',
					'default_value' => '',
					'placeholder'   => '',
				),

				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Position of Pagination', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_position_of_pagination',
					'name'          => 'ft_gallery_position_of_pagination',
					'default_value' => 'above-below',
					'options'       => array(
						1 => array(
							'label' => esc_html__( 'Top', 'feed-them-gallery-premium' ),
							'value' => 'above',
						),
						2 => array(
							'label' => esc_html__( 'Bottom', 'feed-them-gallery-premium' ),
							'value' => 'below',
						),
						3 => array(
							'label' => esc_html__( 'Top and Bottom', 'feed-them-gallery-premium' ),
							'value' => 'above-below',
						),
					),
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
					// 'sub_options' => array(
					// 'sub_options_wrap_class' => 'ftg-pagination-options-wrap',
					// ),.
				),

				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Align Pagination', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ftg_align_pagination',
					'name'          => 'ftg_align_pagination',
					'default_value' => 'right',
					'options'       => array(
						1 => array(
							'label' => esc_html__( 'Left', 'feed-them-gallery-premium' ),
							'value' => 'left',
						),
						2 => array(
							'label' => esc_html__( 'Right', 'feed-them-gallery-premium' ),
							'value' => 'right',
						),
					),
				),
				// Pagination Color
				// JUST NEED TO FINISH THE COLOR OPTIONS FOR THE PAGINATION AND APPLY THEM TO THE FRONT END
				// Loadmore background Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Button Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-pagination-background-color-input',
					'name'          => 'ft_gallery_pagination_button_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Active Button', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-pagination-background-color-input',
					'name'          => 'ft_gallery_pagination_active_button_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				// Loadmore background Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Numbers Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-pagination-text-color-input',
					'name'          => 'ft_gallery_pagination_text_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),

				array(
					'grouped_options_title' => esc_html__( 'Image Count Options', 'feed-them-gallery-premium' ),
					'option_type'           => 'select',
					'label'                 =>
						sprintf(
							esc_html__( 'Display Image Count%1$s For Example: Showing 1-50 of 800 Images.%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'                  => 'text',
					'id'                    => 'ftg_display_image_count',
					'name'                  => 'ftg_display_image_count',
					'default_value'         => 'yes',
					'options'               => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
						2 => array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Align Image Count', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ftg_align_count',
					'name'          => 'ftg_align_count',
					'default_value' => 'left',
					'options'       => array(
						1 => array(
							'label' => esc_html__( 'Left', 'feed-them-gallery-premium' ),
							'value' => 'left',
						),
						2 => array(
							'label' => esc_html__( 'Right', 'feed-them-gallery-premium' ),
							'value' => 'right',
						),
					),
				),

				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Image count Text Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-true-pagination-count-text-color-input',
					'name'          => 'ft_gallery_true_pagination_count_text_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),

			),
		);

		return $this->all_options['pagination'];
	}
	// END PAGINATION OPTIONS.


	/**
	 * Tags Options
	 *
	 * Options for the Tags Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function tags_options() {
		$this->all_options['tags'] = array(
			'required_prem_plugin' => 'feed_them_gallery_premium',
			'section_attr_key'     => 'facebook_',
			'section_title'        => esc_html__( 'Tags Select Search', 'feed-them-gallery-premium' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
			// Token Check // We'll use these option for premium messages in the future.
			'premium_msg_boxes'    => array(
				'album_videos' => array(
					'req_plugin' => 'fts_premium',
					'msg'        => '',
				),
				'reviews'      => array(
					'req_plugin' => 'facebook_reviews',
					'msg'        => '',
				),
			),

			'main_options'         => array(

				// ******************************************
				// Gallery Tags Options
				// ******************************************
                array(
                    'option_type'   => 'select',
                    'label'         => esc_html__( 'Show Select Option Search', 'feed-them-gallery-premium' ),
                    'type'          => 'text',
                    'id'            => 'ft-gallery-tags-search-select',
                    'name'          => 'ft_gallery_tags_search_select',
                    'default_value' => 'no',
                    'options'       => array(
                        array(
                            'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
                            'value' => 'no',
                        ),
                        array(
                            'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
                            'value' => 'yes',
                        ),
                    ),
                ),


				array(
				    'grouped_options_title' => esc_html__( 'Image Tags', 'feed-them-gallery-premium' ),
                    'option_type'   => 'select',
					'label'         => esc_html__( 'Show Image Tags', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-gallery-show-tags',
					'name'          => 'ft_gallery_show_tags',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Tags Separator', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ftg-image-tags-separator',
					'name'          => 'ftg_image_tags_separator',
					'default_value' => '',
					'options'       => array(
						array(
							'label' => esc_html__( 'Comma - ie* One, Two, Three', 'feed-them-gallery-premium' ),
							'value' => ',&nbsp;',
						),
						array(
							'label' => esc_html__( 'Period - ie* One &#46; Two &#46; Three', 'feed-them-gallery-premium' ),
							'value' => '&nbsp;&#46;&nbsp;',
						),
						array(
							'label' => esc_html__( 'Bullet - ie* One &bull; Two &bull; Three', 'feed-them-gallery-premium' ),
							'value' => '&nbsp;&bull;&nbsp;',
						),
						array(
							'label' => esc_html__( 'Pipe - ie* One | Two | Three', 'feed-them-gallery-premium' ),
							'value' => '&nbsp;|&nbsp;',
						),
						array(
							'label' => esc_html__( 'Space - ie* One Two Three', 'feed-them-gallery-premium' ),
							'value' => '&nbsp;',
						),
						array(
							'label' => esc_html__( 'Dash - ie* One - Two - Three', 'feed-them-gallery-premium' ),
							'value' => '&nbsp;-&nbsp;',
						),
					),
				),

				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Link Font Size', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-tags-text-size',
					'name'          => 'ft_tags_text_size',
					'default_value' => '',
					'placeholder'   => esc_html__( '12px', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				// Tags Link Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Link Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-tags-link-color',
					'name'          => 'ft_tags_link_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'select',
					'label'         => esc_html__( 'Align', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ftg-align-tags',
					'name'          => 'ftg_align_tags',
					'default_value' => '',
					'options'       => array(
						1 => array(
							'label' => esc_html__( 'Left', 'feed-them-gallery-premium' ),
							'value' => 'left',
						),
						2 => array(
							'label' => esc_html__( 'Right', 'feed-them-gallery-premium' ),
							'value' => 'right',
						),
						3 => array(
							'label' => esc_html__( 'Center', 'feed-them-gallery-premium' ),
							'value' => 'center',
						),
					),
				),

				/*
				Array(
					'option_type' => 'input',
					'label' => __('Link Margin Right', 'feed-them-gallery-premium'),
					'type' => 'text',
					'id' => 'ft-tags-link-margin-right',
					'name' => 'ft_tags_link_margin_right',
					'default_value' => '',
					'placeholder' => __('5px', 'feed-them-gallery-premium'),
					'autocomplete' => 'off',
				),
				*/

				// Tags Background Color.
				array(
					'grouped_options_title' => esc_html__( 'Image Tags Background Wrap', 'feed-them-gallery-premium' ),
					'option_type'           => 'input',
					'label'                 => esc_html__( 'Color', 'feed-them-gallery-premium' ),
					'class'                 => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'                  => 'text',
					'id'                    => 'ft-tags-background-color-input',
					'name'                  => 'ft_gallery_tags_background_color',
					'default_value'         => '',
					'placeholder'           => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'          => 'off',
				),

				// Tags Background Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Padding', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-tags-padding',
					'name'          => 'ft_gallery_tags_padding',
					'default_value' => '',
					'placeholder'   => esc_html__( '18px', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),

				// Tags Text.
				array(
					'grouped_options_title' => esc_html__( 'Customize the word, Tags:', 'feed-them-gallery-premium' ),
					'option_type'           => 'input',
					'label'                 => esc_html__( 'Change Tags Text', 'feed-them-gallery-premium' ),
					'type'                  => 'text',
					'id'                    => 'ftg-image-tags-text',
					'name'                  => 'ftg_image_tags_text',
					'default_value'         => '',
					'placeholder'           => esc_html__( 'Tags:', 'feed-them-gallery-premium' ),
					'autocomplete'          => 'off',
				),
				// Tags Text Size.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Font Size', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-tags-text-size',
					'name'          => 'ft_gallery_tags_text_size',
					'default_value' => '',
					'placeholder'   => esc_html__( '12px', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Font Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-tags-text-color',
					'name'          => 'ft_tags_text_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( ' Margin Right', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-tags-text-margin-right',
					'name'          => 'ft_tags_text_margin_right',
					'default_value' => '',
					'placeholder'   => esc_html__( '5px', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),

			),
		);

		return $this->all_options['tags'];
	}
	// END TAGS OPTIONS.
}
