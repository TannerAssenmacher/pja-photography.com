<?php
/**
 * Template Settings Page
 *
 * Class Feed Them Gallery Settings Page
 *
 * @class    Template_Settings_Page
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */

namespace feed_them_gallery;

/**
 * Class Settings_Page
 */
class Template_Settings_Page {

	/**
	 * Hook ID
	 *
	 * @var \feed_them_gallery\Metabox_Settings|string
	 */
	public $hook_id = '';

	/**
	 * Metabox Settings Class
	 * initiates Metabox Settings Class
	 *
	 * @var \feed_them_gallery\Metabox_Settings|string
	 */
	public $metabox_settings_class = '';

	/**
	 * Gallery Options
	 * initiates Gallery Options Class
	 *
	 * @var \feed_them_gallery\Zip_Gallery|string
	 */
	public $gallery_options_class = '';


	/**
	 * Saved Settings Array
	 * an array of settings to save when saving page
	 *
	 * @var string
	 */
	public $saved_settings_array = array();

	/**
	 * Main Post Type
	 *
	 * The post type to be checked
	 *
	 * @var string
	 */
	public $main_post_type = 'ft_gallery';

	/**
	 * Load Function
	 *
	 * Load up all our actions and filters.
	 *
	 * @param array  $all_options All options.
	 * @param string $main_post_type Main Post Type.
	 * @since 1.0.0
	 */
	public static function load( $all_options, $main_post_type ) {
		$instance = new self();

		$instance->set_class_vars( $all_options, $main_post_type );

		$instance->add_actions_filters();
	}

	/**
	 * Settings_Page constructor.
	 */
	public function __construct() { }

	/**
	 * Set Class Variables
	 *
	 *  Sets the variables for this class
	 *
	 * @param array  $all_options All options.
	 * @param string $main_post_type Main Post Type.
	 * @since 1.0.6
	 */
	public function set_class_vars( $all_options, $main_post_type ) {

		// This should be the page= parameter of the url for example page=template_settings_page.
		$this->hook_id = 'template_settings_page';

		// Saved Settings Array.
		$this->saved_settings_array = $all_options;

		// Load Metabox Setings Class (including all of the scripts and styles attached).
		$this->metabox_settings_class = new Metabox_Settings( $this, $this->saved_settings_array, true );

		// Set Main Post Type.
		$this->metabox_settings_class->set_main_post_type( $main_post_type );

		// Set Hook ID.
		$this->metabox_settings_class->set_hook_id( $this->hook_id );

		// Set Hook ID.
		$this->metabox_settings_class->set_metabox_specific_form_inputs( true );

		// This is the page name set for the edit settings page (ie. page=template_settings_page) generally set in URL.
		$this->metabox_settings_class->set_settings_page_name( 'template_settings_page' );
	}

	/**
	 * Add Action Filters
	 *
	 * Load up all our styles and js.
	 *
	 * @since 1.0.0
	 */
	public function add_actions_filters() {
		if ( is_admin() ) {
			// Adds setting page to Feed Them Gallery menu.
			add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		}

		// Add Meta Boxes.
		add_action( 'admin_init', array( $this, 'ft_gallery_template_add_metaboxes' ) );
	}

	/**
	 * FT Gallery Submenu Pages
	 *
	 * Admin Submenu buttons
	 *
	 * @since 1.0.0
	 */
	public function add_submenu_page() {
		// Template Settings Page.
		add_submenu_page(
			'edit.php?post_type=ft_gallery',
			esc_html__( 'Tags Settings', 'feed-them-gallery-premium' ),
			esc_html__( 'Tags Settings', 'feed-them-gallery-premium' ),
			'manage_options',
			$this->hook_id,
			array( $this, 'Template_Settings_Page' )
		);
	}

	/**
	 * Template Settings Scripts and Styles
	 *
	 * Enqueue and Localize the scripts and styles registered in the Metabox Settings Class!
	 *
	 * @since 1.0.0
	 */
	public function template_settings_scripts_styles() {
		// ADD Scripts needed ONLY for template settings or pages here (if it's something that needs to be added to all Metabox Settings pages please add to Metabox Settings class file instead).
	}

	/**
	 * FT Gallery Template Add Metaboxes
	 *
	 * Creates the Tabs Menu Metabox
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_template_add_metaboxes() {

		// Add Main Template Metabox for Settings!
		add_meta_box( 'ft-template-settings-mb', esc_html__( 'Feed Them Gallery Settings', 'feed-them-gallery-premium' ), array( $this, 'template_tab_menu_metabox' ), $this->hook_id, 'normal', 'high', null );
	}

	/**
	 * FT Gallery Metabox Tabs List
	 *
	 * The list of tabs Items for settings page metaboxes
	 *
	 * @return array
	 * @since 1.1.6
	 */
	public function template_metabox_tabs_list() {

		$metabox_tabs_list = array(
			// Base of each tab! The array keys are the base name and the array value is a list of tab keys.
			'base_tabs' => array(
				'ft_gallery_page_template_settings_page' => array( 'layout', 'colors', 'woocommerce', 'watermark', 'pagination', 'tags' ),
			),
			// Tabs List! The cont_func item is relative the the Function name for that tabs content. The array Keys for each tab are also relative to classes and ID on wraps of display_metabox_content function.
			'tabs_list' => array(
				// Layout Tab!
				'layout'     => array(
					'menu_li_class'      => 'tab2',
					'menu_a_class'       => 'account-tab-highlight',
					'menu_aria_expanded' => 'true',
					'menu_a_text'        => esc_html__( 'Layout', 'feed-them-gallery-premium' ),
					'cont_wrap_id'       => 'ftg-tab-content2',
					'cont_func'          => 'tab_layout_content',
				),
				// Colors Tab!
				'colors'     => array(
					'menu_li_class' => 'tab3',
					'menu_a_text'   => esc_html__( 'Colors', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content3',
					'cont_func'     => 'tab_colors_content',
				),
                // WooCommerce Tab!
                'woocommerce'  => array(
                    'menu_li_class' => 'tab5',
                    'menu_a_text'   => esc_html__( 'WooCommerce', 'feed-them-gallery-premium' ),
                    'cont_wrap_id'  => 'ftg-tab-content5',
                    'cont_func'     => 'tab_woocommerce_content',
                ),
				// Watermark Tab!
				'watermark'  => array(
					'menu_li_class' => 'tab6',
					'menu_a_text'   => esc_html__( 'Watermark', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content6',
					'cont_func'     => 'tab_watermark_content',
				),
				// Pagniation Tab!
				'pagination' => array(
					'menu_li_class' => 'tab7',
					'menu_a_text'   => esc_html__( 'Pagination', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content7',
					'cont_func'     => 'tab_pagination_content',
				),
				// Tags Tab!
				'tags'       => array(
					'menu_li_class' => 'tab8',
					'menu_a_text'   => esc_html__( 'Tags', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content8',
					'cont_func'     => 'tab_tags_content',
				),
			),
		);

		return $metabox_tabs_list;
	}


	/**
	 * Template Tab Menu Metabox
	 *
	 * Creates the Tabs Menu Metabox
	 *
	 * @param string $object The object.
	 * @since 1.0.0
	 */
	public function template_tab_menu_metabox( $object ) {

		$params['object'] = $object;

		$this->metabox_settings_class->display_metabox_content( $this->template_metabox_tabs_list(), $params );

		if ( ! is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
			?>
			<script>
				jQuery('#ftg_sorting_options, #ftg_free_download_size').attr('disabled', 'disabled');
				jQuery('#ftg_sorting_options option[value="no"], #ftg_free_download_size option:first').text('Premium Required');
				jQuery('.ftg-pagination-notice-colored').remove();
			</script>
		<?php } ?>


		<div class="clear"></div>

		<?php
	}

	/**
	 * Settings Page
	 *
	 * Feed Them Gallery Settings Page
	 *
	 * @since 1.0.0
	 */
	public function Template_Settings_Page() {
		?>

		<div class="ft-gallery-main-template-wrapper-all">

			<div class="wrap">

				<h2>Tags Template Settings</h2>
				<div class="ftg-tags-text-message">Settings for the page that displays the images or galleries that have been tagged.</div>

				<?php
				settings_errors();

				// echo '<pre>';
				// print_r($this->metabox_settings_class->get_saved_settings_array());
				// echo '</pre>';.
				?>

				<div class="ftg-settings-meta-box-wrap">

					<form id="post" name="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" >

						<input type="hidden" name="action" value="slickmetabox_form">

						<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
						<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

						<div id="poststuff">

							<div id="post-body" class="metabox-holder columns-<?php echo 1 === get_current_screen()->get_columns() ? '1' : '2'; ?>">

								<div id="postbox-container-1" class="postbox-container">

									<?php do_meta_boxes( $this->hook_id, 'side', null ); ?>
									<!-- #side-sortables -->

								</div><!-- #postbox-container-1 -->

								<div id="postbox-container-2" class="postbox-container">

									<?php do_meta_boxes( $this->hook_id, 'normal', null ); ?>
									<!-- #normal-sortables -->

									<?php do_meta_boxes( $this->hook_id, 'advanced', null ); ?>
									<!-- #advanced-sortables -->

								</div><!-- #postbox-container-2 -->

							</div><!-- #post-body -->

							<div class="clear"></div>

						</div><!-- #poststuff -->

					</form>

				</div><!-- .fx-settings-meta-box-wrap -->

			</div><!-- .wrap -->

		</div>


		<?php
	}

	/**
	 *  Tab Layout Content
	 *
	 * Outputs Layout tab's content for metabox.
	 *
	 * @param string $params All the parameters.
	 * @since 1.0.0
	 */
	public function tab_layout_content( $params ) {
		$this_class = $params['this'];

		echo $this_class->metabox_settings_class->settings_html_form( $this_class->saved_settings_array['layout'], null );
		?>
		<div class="clear"></div>
		<div class="ft-gallery-note ft-gallery-note-footer">
			<?php
			echo sprintf(
				esc_html__( 'Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery-premium' ),
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-settings-page' ) . '" >',
				'</a>'
			);
			?>
		</div>
		<?php
	}

	/**
	 * Tab Colors Content
	 *
	 * Outputs Colors tab's content for metabox.
	 *
	 * @param string $params All the parameters.
	 * @since 1.0.0
	 */
	public function tab_colors_content( $params ) {

		$this_class = $params['this'];

		echo $this_class->metabox_settings_class->settings_html_form( $this_class->saved_settings_array['colors'], null );
		?>
		<div class="clear"></div>

		<div class="ft-gallery-note ft-gallery-note-footer">
			<?php
			echo sprintf(
				esc_html__( 'Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery-premium' ),
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-settings-page' ) . '" >',
				'</a>'
			);
			?>
		</div>
		<?php
	}

	/**
	 * Tab Pagination Content
	 *
	 * Outputs Watermark tab's content for metabox.
	 *
	 * @param string $params All the parameters.
	 * @since 1.0.0
	 */
	public function tab_pagination_content( $params ) {
		$this_class = $params['this'];

		if ( ! is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
			?>
			<div class="ftg-section">
				<?php $this_class->ft_gallery_tab_premium_msg(); ?>
			</div>
			<?php
		}
		echo $this_class->metabox_settings_class->settings_html_form( $this_class->saved_settings_array['pagination'], null );
		?>

		<div class="clear"></div>

		<div class="ft-gallery-note ft-gallery-note-footer">
			<?php
			echo sprintf(
				esc_html__( 'Please %1$screate a ticket%2$s if you are experiencing trouble and one of our team members will be happy to assist you.', 'feed-them-gallery-premium' ),
				'<a href="' . esc_url( 'https://www.slickremix.com/my-account/#tab-support' ) . '" target="_blank">',
				'</a>'
			);
			?>
		</div>
		<?php
	}


    /**
     * Tab Woocommerce Content
     *
     * Outputs WooCommerce tab's content for metabox.
     *
     * @since 1.0.0
     */
    public function tab_woocommerce_content( $params ) {
        $this_class = $params['this'];
        if ( ! is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
            ?>

            <div class="ftg-section">
                <?php $this_class->ft_gallery_tab_premium_msg(); ?>
            </div>
        <?php } ?>

        <?php
        // echo '<pre>';
        // print_r(wp_prepare_attachment_for_js('21529'));
        // echo '</pre>';
        echo $this_class->metabox_settings_class->settings_html_form( $this_class->saved_settings_array['woocommerce'], null );
        ?>

        <?php if ( ! is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) { ?>
            <script>
                jQuery('#ftg-tab-content5 input, #ftg-tab-content5 select').attr('disabled', 'disabled');
                jQuery('#ftg-tab-content5 input').val('Premium Required');
                jQuery('#ftg-tab-content5 select option').text('Premium Required');
            </script>
            <?php
        }
    }

	/**
	 * Tab Watermark Content
	 *
	 * Outputs Watermark tab's content for metabox.
	 *
	 * @param string $params All the parameters.
	 * @since 1.0.0
	 */
	public function tab_watermark_content( $params ) {
		$this_class = $params['this'];
		if ( ! is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
			?>
			<div class="ftg-section">
				<?php $this_class->ft_gallery_tab_premium_msg(); ?>


			</div>
			<?php
		}
		echo $this_class->metabox_settings_class->settings_html_form( $this_class->saved_settings_array['watermark'], null );
		?>

		<div class="clear"></div>

		<div class="ft-gallery-note ft-gallery-note-footer">
			<?php

			// echo '<pre>';
			// print_r( $gallery_class->metabox_settings_class->get_saved_settings_array( $gallery_class->parent_post_id ) );
			// echo '</pre>';.
			echo sprintf(
				esc_html__( 'Please %1$screate a ticket%2$s if you are experiencing trouble and one of our team members will be happy to assist you.', 'feed-them-gallery-premium' ),
				'<a href="' . esc_url( 'https://www.slickremix.com/my-account/#tab-support' ) . '" target="_blank">',
				'</a>'
			);
			?>
		</div>
		<?php
	}

	/**
	 * Tab Tags Content
	 *
	 * Outputs Tags tab's content for metabox.
	 *
	 * @param string $params All the parameters.
	 * @since 1.0.0
	 */
	public function tab_tags_content( $params ) {
		$this_class = $params['this'];
		if ( ! is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
			?>
			<div class="ftg-section">
				<?php $this_class->ft_gallery_tab_premium_msg(); ?>
			</div>
			<?php
		}
		echo $this_class->metabox_settings_class->settings_html_form( $this_class->saved_settings_array['tags'], null );
		?>

		<div class="clear"></div>

		<div class="ft-gallery-note ft-gallery-note-footer"
		">
		<?php
		echo sprintf(
			esc_html__( 'Please %1$screate a ticket%2$s if you are experiencing trouble and one of our team members will be happy to assist you.', 'feed-them-gallery-premium' ),
			'<a href="' . esc_url( 'https://www.slickremix.com/my-account/#tab-support' ) . '" target="_blank">',
			'</a>'
		);
		?>
		</div>
		<?php
	}

	/**
	 * Metabox Specific Form Inputs
	 *
	 * This adds to the output of the metabox output forms for settings_html_form function in the Metabox Settings class.
	 *
	 * @param string $params All the parameters.
	 * @return array
	 * @since 1.1.6
	 */
	public function metabox_specific_form_inputs( $params ) {

		// Gallery Options (REST API call).
		$gallery_options_returned = $this->metabox_settings_class->get_saved_settings_array();
		// Option Info.
		$option = $params['input_option'];

		$output = '';

		if ( isset( $option['option_type'] ) ) {
			switch ( $option['option_type'] ) {

				// Checkbox for image sizes COMMENTING OUT BUT LEAVING FOR FUTURE QUICK USE
				// case 'checkbox-image-sizes':
				// $final_value_images = array('thumbnailzzz','mediummmm', 'large', 'full');
				// Get Gallery Options via the Rest API
				// $final_value_images = $gallery_options_returned['ft_watermark_image_sizes']['image_sizes'];
				// print_r($final_value_images);
				// array('thumbnailzzz','mediummmm', 'largeee', 'fullll');
				// $output .= '<label for="'. $option['id'] . '"><input type="checkbox" val="' . $option['default_value'] . '" name="ft_watermark_image_sizes[image_sizes][' . $option['default_value'] . ']" id="'.$option['id'] . '" '. ( array_key_exists($option['default_value'], $final_value_images) ? ' checked="checked"' : '') .'/>';
				// $output .= '' . $option['default_value'] . '</label>';
				// break;
				// Checkbox for image sizes used so you can check the image sizes you want to be water marked after you save the page.
				case 'checkbox-dynamic-image-sizes':
					$final_value_images = isset( $gallery_options_returned['ft_watermark_image_sizes']['image_sizes'] ) ? $gallery_options_returned['ft_watermark_image_sizes']['image_sizes'] : array();
					$output            .= '<div class="clear"></div>';

					global $_wp_additional_image_sizes;

					$sizes = array();
					foreach ( get_intermediate_image_sizes() as $_size ) {
						if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
							$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
							$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
							$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
						} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
							$sizes[ $_size ] = array(
								'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
								'height' => $_wp_additional_image_sizes[ $_size ]['height'],
								'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
							);
						}
						$output .= '<label for="' . $_size . '"><input type="checkbox" val="' . $_size . '" name="ft_watermark_image_sizes[image_sizes][' . $_size . ']" id="' . $option['id'] . '-' . $_size . '" ' . ( array_key_exists( $_size, $final_value_images ) ? ' checked="checked"' : '' ) . '/>' . $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] . '</label><br/>';

					}
					$output .= '<label for="full"><input type="checkbox" val="full" id="ft_watermark_image_-full" name="ft_watermark_image_sizes[image_sizes][full]" ' . ( array_key_exists( 'full', $final_value_images ) ? 'checked="checked"' : '' ) . '/>full</label><br/>';
					$output .= '<br/><br/>';
					// TESTING AREA
					// echo $final_value_images;
					// echo '<pre>';
					// print_r($sizes);
					// echo '</pre>';.
					break;

				// Image sizes for page.
				case 'ft-images-sizes-page':
					$final_value_images = isset( $gallery_options_returned['ft_gallery_images_sizes_page'] ) ? $gallery_options_returned['ft_gallery_images_sizes_page'] : '';
					$output            .= '<select name="' . $option['name'] . '" id="' . $option['id'] . '"  class="feed-them-gallery-admin-input">';

					global $_wp_additional_image_sizes;

					$sizes   = array();
					$output .= '<option val="Choose an option" ' . ( 'not_set' == $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Choose an option', 'feed-them-gallery-premium' ) . '</option>';
					foreach ( get_intermediate_image_sizes() as $_size ) {
						if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
							$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
							$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
							$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
						} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
							$sizes[ $_size ] = array(
								'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
								'height' => $_wp_additional_image_sizes[ $_size ]['height'],
								'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
							);
						}
						$output .= '<option val="' . $_size . '" ' . ( $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] === $final_value_images ? 'selected="selected"' : '' ) . '>' . $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] . '</option>';
					}
					$output .= '<option val="full" ' . ( 'full' === $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'full', 'feed-them-gallery-premium' ) . '</option>';
					// TESTING AREA
					// echo $final_value_images;
					// echo '<pre>';
					// print_r($sizes);
					// echo '</pre>';.
					$output .= '</select>';
					break;

				// Image sizes for popup.
				case 'ft-images-sizes-popup':
					$final_value_images = isset( $gallery_options_returned['ft_gallery_images_sizes_popup'] ) ? $gallery_options_returned['ft_gallery_images_sizes_popup'] : '';
					$output            .= '<select name="' . $option['name'] . '" id="' . $option['id'] . '"  class="feed-them-gallery-admin-input">';

					global $_wp_additional_image_sizes;

					$sizes = array();

					$output .= '<option val="Choose an option" ' . ( 'not_set' === $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Choose an option', 'feed-them-gallery-premium' ) . '</option>';
					foreach ( get_intermediate_image_sizes() as $_size ) {
						if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
							$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
							$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
							$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
						} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
							$sizes[ $_size ] = array(
								'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
								'height' => $_wp_additional_image_sizes[ $_size ]['height'],
								'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
							);
						}
						$output .= '<option val="' . $_size . '" ' . ( $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] === $final_value_images ? 'selected="selected"' : '' ) . '>' . $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] . '</option>';
					}
					$output .= '<option val="full" ' . ( 'full' === $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'full', 'feed-them-gallery-premium' ) . '</option>';
					// TESTING AREA
					// echo $final_value_images;
					// echo '<pre>';
					// print_r($sizes);
					// echo '</pre>';.
					$output .= '</select>';
					break;

				// Image sizes for Free download icon.
				case 'ftg-free-download-size':
					$final_value_images = isset( $gallery_options_returned['ftg_free_download_size'] ) ? $gallery_options_returned['ftg_free_download_size'] : '';
					$output            .= '<select name="' . $option['name'] . '" id="' . $option['id'] . '"  class="feed-them-gallery-admin-input">';

					global $_wp_additional_image_sizes;

					$sizes   = array();
					$output .= '<option val="Choose an option" ' . ( 'not_set' === $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Choose an option', 'feed-them-gallery-premium' ) . '</option>';
					foreach ( get_intermediate_image_sizes() as $_size ) {
						if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
							$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
							$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
							$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
						} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
							$sizes[ $_size ] = array(
								'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
								'height' => $_wp_additional_image_sizes[ $_size ]['height'],
								'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
							);
						}
						$output .= '<option val="' . $_size . '" ' . ( $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] === $final_value_images ? 'selected="selected"' : '' ) . '>' . $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] . '</option>';
					}
					$output .= '<option val="full" ' . ( 'full' === $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'full', 'feed-them-gallery-premium' ) . '</option>';
					// TESTING AREA
					// echo $final_value_images;
					// echo '<pre>';
					// print_r($sizes);
					// echo '</pre>';.
					$output .= '</select>';
					break;

			}
		}

		return wp_kses(
			$output,
			array(
				'a'      => array(
					'href'  => array(),
					'title' => array(),
					'class' => array(),
				),
				'div'    => array(
					'class' => array(),
					'id'    => array(),
					'style' => array(),
				),
				'select' => array(
					'name'  => array(),
					'class' => array(),
					'id'    => array(),
				),
				'option' => array(
					'value'    => array(),
					'selected' => array(),
				),
				'input'  => array(
					'value'       => array(),
					'type'        => array(),
					'class'       => array(),
					'id'          => array(),
					'placeholder' => array(),
					'name'        => array(),
					'checked'     => array(),
				),
				'h3'     => array(
					'class' => array(),
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'small'  => array(),
			)
		);
	}

}//end class
