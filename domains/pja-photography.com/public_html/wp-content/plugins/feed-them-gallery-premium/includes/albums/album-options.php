<?php
/**
 * Album Options Class
 *
 * This class has the options for building and saving on the Custom Meta Boxes
 *
 * @class    Album_Options
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */
namespace feed_them_gallery;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Gallery_Options
 */
class Album_Options {

	public $all_options;

	public function __construct() { }

	/**
	 * All Gallery Options
	 *
	 * Function to return all Gallery options
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_all_options() {
		$instance = new self();

		$instance->layout_options();
		$instance->color_options();
        $instance->watermark_options();
		$instance->pagination_options();
        $instance->clients_options();

		$instance->all_options = apply_filters( 'ftg_premium_album_options', $instance->all_options );

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
							'label' => esc_html__( 'Image Gallery Collage (Masonry)', 'feed-them-gallery-premium' ),
							'value' => 'gallery-collage',
						),
					),
				),
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
				),

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
					'placeholder'        => esc_html__( '', 'feed-them-gallery-premium' ),
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
				// Gallery Load More Options
				// ******************************************
				// Load More Button.
				array(
					'grouped_options_title' => esc_html__( 'Load More Images', 'feed-them-gallery-premium' ),
					'option_type'           => 'select',
					'label'                 =>
						sprintf(
							esc_html__( 'Load More Button%1$s Load More unavailable while using the Pagination option.%2$s', 'feed-them-gallery-premium' ),
							'<br/><small class="ftg-loadmore-notice-colored" style="display: none;">',
							'</small>'
						),
					'type'                  => 'text',
					'id'                    => 'ft_gallery_load_more_option',
					'name'                  => 'ft_gallery_load_more_option',
					'default_value'         => 'no',
					'options'               => array(
						array(
							'label' => esc_html__( 'No', 'feed-them-gallery-premium' ),
							'value' => 'no',
						),
						array(
							'label' => esc_html__( 'Yes', 'feed-them-gallery-premium' ),
							'value' => 'yes',
						),
					),
					'sub_options'           => array(
						'sub_options_wrap_class' => 'facebook-loadmore-wrap',
					),
				),

				// # of Photos.
				array(

					'option_type'   => 'input',
					'label'         => esc_html__( '# of Photos Visible', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_photo_count',
					'name'          => 'ft_gallery_photo_count',
					'default_value' => '',
					'placeholder'   => '',
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
					'sub_options'   => array(
						'sub_options_wrap_class' => 'fts-facebook-load-more-options-wrap',
					),

				),

				// Load More Style.
				array(
					'option_type'        => 'select',
					'label'              => esc_html__( 'Load More Style', 'feed-them-gallery-premium' ),
					'type'               => 'text',
					'id'                 => 'ft_gallery_load_more_style',
					'name'               => 'ft_gallery_load_more_style',
					'instructional-text' =>
						sprintf(
							esc_html__( '%1$sNOTE:%2$s The Button option will show a "Load More Posts" button under your feed. The AutoScroll option will load more posts when you reach the bottom of the feed. AutoScroll ONLY works if you\'ve filled in a Fixed Height for your feed.', 'feed-them-gallery-premium' ),
							'<strong>',
							'</strong>'
						),
					'default_value'      => 'button',
					'options'            => array(
						1 => array(
							'label' => esc_html__( 'Button', 'feed-them-gallery-premium' ),
							'value' => 'button',
						),
						2 => array(
							'label' => esc_html__( 'AutoScroll', 'feed-them-gallery-premium' ),
							'value' => 'autoscroll',
						),
					),
					'sub_options_end'    => true,
				),

				// Load more Button Width.
				array(
					'option_type'   => 'input',
					'label'         =>
						sprintf(
							esc_html__( 'Load more Button Width%1$s Leave blank for auto width%2$s', 'feed-them-gallery-premium' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_loadmore_button_width',
					'name'          => 'ft_gallery_loadmore_button_width',
					'placeholder'   => '300px ' . esc_html__( 'for example', 'feed-them-gallery-premium' ),
					'default_value' => '300px',
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output).
					'sub_options'   => array(
						'sub_options_wrap_class' => 'fts-facebook-load-more-options2-wrap',
					),
				),
				// Load more Button Margin.
				array(
					'option_type'     => 'input',
					'label'           => esc_html__( 'Load more Button Margin', 'feed-them-gallery-premium' ),
					'type'            => 'text',
					'id'              => 'ft_gallery_loadmore_button_margin',
					'name'            => 'ft_gallery_loadmore_button_margin',
					'placeholder'     => '10px ' . esc_html__( 'for example', 'feed-them-gallery-premium' ),
					'default_value'   => '10px',
					'value'           => '',
					'sub_options_end' => 2,
				),

				// ******************************************
				// Gallery Image Count Options
				// ******************************************
				// Load More Style.
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
                            'label' => esc_html__( 'Sort by date', 'feed-them-gallery-premium' ),
                            'value' => 'date',
                        ),
						2 => array(
							'label' => esc_html__( 'The order you manually sorted images', 'feed-them-gallery-premium' ),
							'value' => 'menu_order',
						),
						3 => array(
							'label' => esc_html__( 'Sort alphabetically (A-Z)', 'feed-them-gallery-premium' ),
							'value' => 'title',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         =>
						sprintf(
							esc_html__( 'Display Options%1$s Display a select option for this gallery so your users can select the sort order. Does not work with Loadmore button, only works with Pagination.%2$s', 'feed-them-gallery-premium' ),
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
				// Loadmore background Color.
				array(
					'grouped_options_title' => esc_html__( 'Loadmore Button', 'feed-them-gallery-premium' ),
					'option_type'           => 'input',
					'label'                 => esc_html__( 'Background Color', 'feed-them-gallery-premium' ),
					'class'                 => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'                  => 'text',
					'id'                    => 'ft-gallery-loadmore-background-color-input',
					'name'                  => 'ft_gallery_loadmore_background_color',
					'default_value'         => '',
					'placeholder'           => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'          => 'off',
				),
				// Loadmore background Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Text Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-loadmore-text-color-input',
					'name'          => 'ft_gallery_loadmore_text_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				// Loadmore Count Color.
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Image Count Text Color', 'feed-them-gallery-premium' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
                    'id'            => 'ft-gallery-loadmore-count-text-color-input',
                    'name'          => 'ft_gallery_loadmore_count_text_color',
					'default_value' => '',
					'placeholder'   => esc_html__( '#ddd', 'feed-them-gallery-premium' ),
					'autocomplete'  => 'off',
				),
				// Albums Font Size.
				array(
					'grouped_options_title' => esc_html__( 'Link to Gallery', 'feed-them-gallery-premium' ),
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
					'default_value' => 'middle',
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
	} //END COLOR OPTIONS

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
                    // 'class' => 'fb-link-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
                    'type'             => 'hidden',
                    'id'               => 'ft_watermark_image_input',
                    // 'instructional-text' => '<strong>' . __('NOTE:', 'feed-them-gallery-premium') . '</strong> ' . __('Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery-premium'),
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
                    // 'class' => 'fb-link-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
                    'type'             => 'hidden',
                    'id'               => 'ft_watermark_image_id',
                    // 'instructional-text' => '<strong>' . __('NOTE:', 'feed-them-gallery-premium') . '</strong> ' . __('Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery-premium'),
                    'name'             => 'ft_watermark_image_id',
                    'default_value'    => '',
                    // 'placeholder' => __('', 'feed-them-gallery-premium'),
                    'autocomplete'     => 'off',
                ),

                // Watermark Options
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

                // Watermark Options
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
                            'label' => esc_html__( 'Watermark for image on page only', 'feed-them-gallery-premium' ),
                            'value' => 'page-only',
                        ),
                    ),
                ),


                // Watermark Opacity
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
                // Watermark Position
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
                // watermark Image Margin
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
    } //END WATERMARK OPTIONS


	/**
	 * Pagination Options
	 *
	 * Options for the Layout Tab
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

				// # of Photos
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
				// Pagination Color.
				// JUST NEED TO FINISH THE COLOR OPTIONS FOR THE PAGINATION AND APPLY THEM TO THE FRONT END.
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
					'grouped_options_title' => esc_html__( 'Image Count  Options', 'feed-them-gallery-premium' ),
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
	} //END PAGINATION OPTIONS.

    /**
	 * Clients Options.
	 *
	 * @since	1.0
	 * @return	array	Array of clients options
	 */
	public function clients_options()	{
		$this->all_options['clients'] = array(
			'required_prem_plugin' => 'ftg_clients_manager',
			'section_attr_key'     => 'facebook_',
			'section_title'        => esc_html__( 'Client Options', 'feed-them-gallery-premium' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
            'menu_li_class'        => 'tab8',
            'menu_a_text'          => esc_html__( 'Clients', 'feed-them-gallery-premium' ),
            'cont_wrap_id'         => 'ftg-tab-content10',
            'cont_func'            => 'premium_extension_required',
			'main_options'         => array(

				// ******************************************
				// Gallery Clients Options
				// ******************************************
				array(
					'option_type'   => 'input',
					'label'         => esc_html__( 'Album Clients', 'feed-them-gallery-premium' ),
					'type'          => 'text',
					'id'            => 'ft-album-show-clients',
					'name'          => 'ft_album_show_clients',
					'default_value' => __( 'Clients Manager Required', 'feed-them-gallery-premium' ),
					'disabled'      => true
				)
			)
        );

		return $this->all_options['clients'];
	}
}
