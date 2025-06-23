<?php
/**
 * Albums Class
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * @version  1.0.0
 * @package  FeedThemSocial/Core
 * @author   SlickRemix
 */

namespace feed_them_gallery;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Gallery
 *
 * @package FeedThemSocial/Core
 */
class Albums {

	/**
	 * Parent Post ID
	 * used to set Gallery ID
	 *
	 * @var string
	 */
	public $parent_post_id = '';

	/**
	 * Saved Settings Array
	 * an array of settings to save when saving page
	 *
	 * @var string
	 */
	public $saved_settings_array = array();

	/**
	 * Global Prefix
	 * Sets Prefix for global options
	 *
	 * @var string
	 */
	public $global_prefix = 'global_';

	/**
	 * ZIP Gallery Class
	 * initiates ZIP Gallery Class
	 *
	 * @var \feed_them_gallery\Zip_Gallery|string
	 */
	public $zip_gallery_class = '';

	/**
	 * Gallery Options
	 * initiates Gallery Options Class
	 *
	 * @var \feed_them_gallery\Zip_Gallery|string
	 */
	public $album_options_class = '';

	/**
	 * Core Functions Class
	 * initiates Core Functions Class
	 *
	 * @var \feed_them_gallery\Core_Functions|string
	 */
	public $core_functions_class = '';

	/**
	 * Metabox Settings Class
	 * initiates Metabox Settings Class
	 *
	 * @var \feed_them_gallery\Metabox_Settings|string
	 */
	public $metabox_settings_class = '';

	/**
	 * Load Class
	 *
	 * Function to initiate class loading.
	 *
	 * @param array  $all_options All options.
	 * @param string $main_post_type Main Post Type.
	 * @since 1.1.8
	 */
	public static function load( $all_options, $main_post_type ) {
		$instance = new self();

		// Set Class Variables.
		$instance->set_class_vars( $all_options, $main_post_type );

		// Add Actions and Filters.
		$instance->add_actions_filters();
	}

	/**
	 * Set Class Variables
	 *
	 *  Sets the variables for this class
	 *
	 * @param array  $all_options All options.
	 * @param string $main_post_type Main Post Type.
	 * @since 1.1.8
	 */
	public function set_class_vars( $all_options, $main_post_type ) {

		// All Album Options.
		$this->saved_settings_array = $all_options;

		// Set local variables.
		$this->plugin_locale = 'feed-them-gallery-premium';

		// Core Functions.
		$this->core_functions_class = new Core_Functions();

		// We set ! wp_doing_ajax() so ajax isn't accessible to the front end.
		// This came about after a ticket we received about our plugin being active and
		// causing a woo booking plugin to not be able to checkout proper and ninja forms submit forms.
		if ( is_admin() && ! wp_doing_ajax() ) {

			// Metabox Settings.
			$this->metabox_settings_class = new Metabox_Settings( $this, $this->saved_settings_array );

			// Set Metabox Specific Form Inputs.
			$this->metabox_settings_class->set_metabox_specific_form_inputs( true );

			// Set Main Post Type.
			$this->metabox_settings_class->set_main_post_type( $main_post_type );
		}
	}

	/**
	 * Gallery constructor.
	 */
	public function __construct() { }

	/**
	 * Add Actions & Filters
	 *
	 * Adds the Actions and filters for the class.
	 *
	 * @since 1.1.8
	 */
	public function add_actions_filters() {

		// Scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'ft_gallery_albums_scripts' ) );

		// Register ALBUMS CPT.
		add_action( 'init', array( $this, 'ft_gallery_albums_cpt' ) );

		// Response Messages.
		add_filter( 'post_updated_messages', array( $this, 'ft_gallery_albums_updated_messages' ) );

		// Register Route
		add_action( 'rest_api_init', array( $this, 'register_album_options_route' ) );

		// Gallery List function.
		add_filter( 'manage_ft_gallery_albums_posts_columns', array( $this, 'ft_gallery_albums_set_custom_edit_columns' ) );
		add_action( 'manage_ft_gallery_albums_posts_custom_column', array( $this, 'ft_gallery_albums_custom_edit_column' ), 10, 2 );
		// Change Button Text.
		add_filter( 'gettext', array( $this, 'ft_gallery_albums_set_button_text' ), 20, 3 );
		// Add Meta Boxes.
		add_action( 'add_meta_boxes', array( $this, 'ft_gallery_albums_add_metaboxes' ) );

		// Rename Submenu Item to Galleries.
		add_filter( 'attribute_escape', array( $this, 'ft_gallery_albums_rename_submenu_name' ), 10, 2 );

		add_action( 'current_screen', array( $this, 'ft_gallery_albums_check_page' ) );

		// Add Galleries to Album.
		add_action( 'wp_ajax_ft_gallery_add_galleries_to_album', array( $this, 'ft_gallery_add_galleries_to_album' ) );
		add_action( 'wp_ajax_nopriv_ft_gallery_add_galleries_to_album', array( $this, 'ft_gallery_add_galleries_to_album' ) );

		add_action( 'wp_ajax_list_update_order', array( $this, 'ft_album_gallery_order_list' ) );
		// Delete Galleries from Album.
		add_action( 'wp_ajax_ft_gallery_delete_galleries_from_album', array( $this, 'ft_gallery_delete_galleries_from_album' ) );
		add_action( 'wp_ajax_nopriv_ft_gallery_delete_galleries_from_album', array( $this, 'ft_gallery_delete_galleries_from_album' ) );

		if ( ! ftg_get_option( 'duplicate_post_show' ) ) {

			add_action( 'admin_action_ft_gallery_albums_duplicate_post_as_draft', array( $this, 'ft_gallery_albums_duplicate_post_as_draft' ) );
			add_filter( 'page_row_actions', array( $this, 'ft_gallery_albums_duplicate_post_link' ), 10, 2 );
			add_filter( 'ft_gallery__albums_row_actions', array( $this, 'ft_gallery_albums_duplicate_post_link' ), 10, 2 );
			add_action( 'post_submitbox_start', array( $this, 'ft_gallery_albums_duplicate_post_add_duplicate_post_button' ) );

		}

		// Show albums within [ftg_client_galleries] shortcode
		add_action( 'ftg_cm_client_galleries_shortcode_list', array( $this, 'client_galleries_shortcode' ) );

	}

    /**
	 * FT Album Tab Requires Extension Notice HTML
	 *
	 * Creates notice html for return
	 *
	 * @param	string	$plugin	The plugin needed
	 * @since 	1.0.0
	 */
	public function requires_extension( $plugin ) {
		$plugins  = ft_gallery_premium_plugins();
		$plugin   = isset( $plugins[ $plugin ] ) ? $plugins[ $plugin ] : false;

        if ( ! $plugin )    {
            return;
        }

		$title    = $plugin['title'];
		$purchase = $plugin['purchase_url'];

		ob_start();

		?>
		<div class="ft-gallery-premium-mesg">
			<?php
			printf(
				__( 'Please purchase, install and activate <a href="%s" target"_blank">%s</a> for these additional awesome features!', 'feed-them-gallery-premium' ),
				esc_url( $purchase ),
				$title
			);
		?>
		</div>
		<?php

		echo ob_get_clean();
	} // requires_extension

	/**
	 * Register Album Options (REST API)
	 *
	 * Register the Album options via REST API
	 *
	 * @since 1.0.0
	 */
	public function register_album_options_route() {
		register_rest_route(
			'ftgallery/v2',
			'/album-options',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'ft_gallery_get_albums_options' ),
                'permission_callback' => '__return_true'
			)
		);
	}

	/**
	 * Count Gallery Attachments
	 *
	 * How many attachments are there in the gallery
	 *
	 * @param $gallery_id
	 * @param string     $post_mime_type
	 * @return int
	 * @since
	 */
	public function ft_gallery_count_gallery_attachments( $gallery_id, $post_mime_type = 'image' ) {
		$attachments = get_children(
			array(
				'post_parent'    => $gallery_id,
				'post_mime_type' => $post_mime_type,
			)
		);

		return count( $attachments );
	}

	/**
	 * FT Gallery Order List
	 *
	 * Attachment order list
	 *
	 * @since 1.0.0
	 */
	public function ft_album_gallery_order_list() {
		// we use the list_item (id="list_item_23880") which then finds the ID right after list_item and we use the id from there.
		$attachment_id = $_POST['list_item'];
		$post_id       = $_POST['post_id'];

		// empty out the meta array and construct a new one based on the new $_POST['list_item'] coming from the ajax call.
		update_post_meta( $post_id, 'ft_gallery_album_gallery_ids', '' );

		// LEFT OFF FIGURING OUT HOW I CAN UPDATE THE NEW ARRAY WITH MENU ORDER NUMBER, NOT SURE BEST WAY YET.
		foreach ( $attachment_id as $img_index => $img_id ) {
			$object              = new \stdClass();
			$object->ID          = sanitize_text_field( $img_id );
			$object->menu_order  = sanitize_text_field( $img_index );
			$album_gallery_ids[] = $object;

			echo esc_html( $img_id );
		}

		update_post_meta( $post_id, 'ft_gallery_album_gallery_ids', $album_gallery_ids );

		// error_log('wtf is going on with the $_POST[\'list_item\']  '.print_r(get_post_meta(18240, 'wp_logo_slider_images', true), true));
		 return $attachment_id;
	}


	/**
	 * FT Gallery Tab Notice HTML
	 *
	 * Creates notice html for return
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_tab_premium_msg() {
		echo sprintf(
			esc_html( '%1$sPlease purchase, install and activate %2$sFeed Them Gallery Premium%3$s for these additional awesome features!%4$s', 'feed-them-gallery-premium' ),
			'<div class="ft-gallery-premium-mesg">',
			'<a href="' . esc_url( 'https://www.slickremix.com/downloads/feed-them-gallery/' ) . '">',
			'</a>',
			'</div>'
		);
	}

	/**
	 * FT Gallery Add Galleries To Album
	 * Add Galleries to an Album and save to option
	 *
	 * @param null $current_album_id
	 * @param null $galleries_array
	 * @param null $ignore_echos
	 * @since 1.0.0
	 */
	public function ft_gallery_add_galleries_to_album( $current_album_id = null, $galleries_array = null, $ignore_echos = null ) {

		$album_id = empty( $current_album_id ) ? sanitize_text_field( wp_unslash( $_REQUEST['AlbumID'] ) ) : $current_album_id;

		$album_gallery_ids = get_post_meta( $album_id, 'ft_gallery_album_gallery_ids', true );

		// Check and set $album_gallery_ids.
		$album_gallery_ids = isset( $album_gallery_ids ) && ! empty( $album_gallery_ids ) ? $album_gallery_ids : array();

		// Check if we are using the AJAX or the variable set in onclick.
		if ( is_array( $galleries_array ) ) {
			$selected_galleries = $galleries_array;
		} else {
			// Check to see if this is only Selected Images.
			$selected_galleries = isset( $_REQUEST['addselectedGalleries'] ) && ! empty( $_REQUEST['addselectedGalleries'] ) ? json_decode( sanitize_text_field( wp_unslash( $_REQUEST['addselectedGalleries'] ) ) ) : '';

		}

		// Media was Selected so make name have attachments count.
		if ( is_array( $selected_galleries ) && ! empty( $selected_galleries ) ) {

			// Check if $selected_galleries returned an object.
			if ( ! empty( $selected_galleries ) ) {

				if ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) {
					echo esc_html__( 'The following Galleries have been added to this album. Please click the Publish or Update button to see your changes.', 'feed-them-gallery-premium' );
				}
				?><ol>
				<?php

				foreach ( $selected_galleries as $gallery_id ) {

					if ( ! in_array( $gallery_id->ID, array_column( $album_gallery_ids, 'ID' ) ) ) {

						// $album_gallery_ids[] = $gallery_id;
						$object              = new \stdClass();
						$object->ID          = sanitize_text_field( $gallery_id );
						$object->menu_order  = sanitize_text_field( 0 );
						$album_gallery_ids[] = $object;
					}
					?>
							<li><?php echo get_the_title( $gallery_id ); ?></li>
							<?php
				}
				?>
					</ol>
					<?php
						// Add Gallery ID's to Album's array.
						update_post_meta( $album_id, 'ft_gallery_album_gallery_ids', $album_gallery_ids );
			}
		} elseif ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) {

				echo esc_html__( 'No new Galleries in this Album. Please create at least 1 new gallery to attach to this album.', 'feed-them-gallery-premium' );

		}
		exit;
	}

	/**
	 * FT Gallery Delete Galleries from Album
	 *
	 * Delete Galleries from Album and then save option.
	 *
	 * @param null $postID
	 * @param null $galleries_array
	 * @param null $ignore_echos
	 * @since 1.0.0
	 */
	public function ft_gallery_delete_galleries_from_album( $current_album_id = null, $galleries_array = null, $ignore_echos = null ) {

		$album_id = empty( $current_album_id ) ? sanitize_text_field( wp_unslash( $_REQUEST['AlbumID'] ) ) : $current_album_id;

		$album_gallery_ids = get_post_meta( $album_id, 'ft_gallery_album_gallery_ids', true );

		// Check and set $album_gallery_ids.
		$album_gallery_ids = isset( $album_gallery_ids ) && ! empty( $album_gallery_ids ) ? $album_gallery_ids : array();

		// Check if we are using the AJAX or the variable set in onclick.
		if ( is_array( $galleries_array ) ) {
			$selected_galleries = $galleries_array;
		} else {
			// Check to see if this is only Selected Images.
			$selected_galleries = isset( $_REQUEST['deleteselectedGalleries'] ) && ! empty( $_REQUEST['deleteselectedGalleries'] ) ? json_decode( sanitize_text_field( wp_unslash( $_REQUEST['deleteselectedGalleries'] ) ) ) : '';
		}

		// Media was Selected so make name have attachments count.
		if ( is_array( $selected_galleries ) && ! empty( $selected_galleries ) ) {

			// Check if wc_get_product returned an object.
			if ( ! empty( $selected_galleries ) ) {

				if ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) {
					echo esc_html__( ' The following Galleries have been removed from this album. Please click the Publish or Update button to see your changes.', 'feed-them-gallery-premium' );
				}

				?>
				<ol>
					<?php

					// Duplicate Woo Model Product and Update new product.
					foreach ( $selected_galleries as $key => $gallery_id ) {

						// Search Album option for gallery ID if found return array Key if not found returns false.
						// echo  array_search( $gallery_id, array_column($album_gallery_ids, 'ID'), true);
						$album_gallery_ids = array_values( $album_gallery_ids );
						$album_key         = array_search( $gallery_id, array_column( $album_gallery_ids, 'ID' ), true );
						if ( false !== $album_key ) {
							unset( $album_gallery_ids[ $album_key ] );
						}
						?>
						<li><?php echo get_the_title( $gallery_id ) . ' (ID: ' . esc_html( $gallery_id ) . ')'; ?></li>
						<?php
					}
					echo '</ol>';
					$album_gallery_ids = array_values( $album_gallery_ids );

					// Add Gallery ID's to Album's array.
					update_post_meta( $album_id, 'ft_gallery_album_gallery_ids', $album_gallery_ids );

			}
		} elseif ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) {
			echo esc_html__( 'No Galleries selected to delete. Please check the box on any gallery then delete it.', 'feed-them-gallery-premium' );
		}
		exit;
	}

	/**
	 * FT Gallery Custom Thumb Sizes
	 *
	 * Adds Custom sizes too
	 *
	 * @param array $sizes
	 * @return array
	 * @since
	 */
	public function ft_gallery_albums_custom_thumb_sizes( $sizes ) {
		return array_merge(
			$sizes,
			array(
				'ft_gallery_albums_thumb' => esc_html__( 'Feed Them Gallery Thumb', 'feed-them-gallery-premium' ),
			)
		);
	}

	/**
	 * FT Gallery Check Page
	 *
	 * What page are we on?
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_check_page() {
		$current_screen = get_current_screen();

		if ( 'ft_gallery_albums' === $current_screen->post_type && 'post' === $current_screen->base && is_admin() ) {

			if ( isset( $_GET['post'] ) ) {
				$this->parent_post_id = sanitize_text_field( wp_unslash( $_GET['post'] ) );
			}
			if ( isset( $_POST['post'] ) ) {
				$this->parent_post_id = sanitize_text_field( wp_unslash( $_POST['post'] ) );
			}
		}
	}

	/**
	 * Get Album Options (REST API)
	 *
	 * Get options using WordPress's REST API
	 *
	 * @param $album_id
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_get_albums_options_rest( $album_id ) {

		$request = new \WP_REST_Request( 'GET', '/ftgallery/v2/album-options' );

		$request->set_param( 'album_id', $album_id );

		$response = rest_do_request( $request );

		// Check for error.
		if ( is_wp_error( $response ) ) {
			return esc_html__( 'oops something isn\'t right.', 'feed-them-gallery-premium' );
		}

		$final_response = isset( $response->data ) ? $response->data : esc_html__( 'No Images attached to this post.', 'feed-them-gallery-premium' );

		return $final_response;
	}

	/**
	 * FT Album Get Gallery Options
	 *
	 * Get options set for a gallery
	 *
	 * @param array $gallery_id Gallery ID.
	 * @return array
	 * @since 1.0.0
	 */
	public function ft_gallery_get_albums_options( $album_id ) {

		$post_info = get_post( $album_id['album_id'] );

		// echo '<pre>';
		// print_r($post_info);
		// echo '</pre>';
		$old_options   = get_post_meta( $album_id['album_id'], 'ft_gallery_albums_settings_options', true );
		$options_array = isset( $old_options ) && ! empty( $old_options ) ? $old_options : array();

		if ( ! $options_array ) {

			// Basic Post Info.
			$options_array['ft_gallery_image_id'] = isset( $post_info->ID ) ? $post_info->ID : esc_html__( 'This ID does not exist anymore', 'feed-them-gallery-premium' );
			$options_array['ft_gallery_author']   = isset( $post_info->post_author ) ? $post_info->post_author : '';
			// $options_array['ft_gallery_post_date'] = $post_info->post_date_gmt;
			$options_array['ft_gallery_post_title'] = isset( $post_info->post_title ) ? $post_info->post_title : '';
			// $options_array['ft_gallery_post_alttext'] = $post_info->post_title;
			// $options_array['ft_gallery_comment_status'] = $post_info->comment_status;
			foreach ( $this->saved_settings_array as $box_array ) {
				foreach ( $box_array as $box_key => $settings ) {
					if ( 'main_options' === $box_key ) {
						// Gallery Settings.
						foreach ( $settings as $option ) {
							$option_name          = ! empty( $option['name'] ) ? $option['name'] : '';
							$option_default_value = ! empty( $option['default_value'] ) ? $option['default_value'] : '';

							if ( ! empty( $option_name ) && ! empty( $option_default_value ) ) {

								// Set value or use Default_value.
								$options_array[ $option_name ] = $option_default_value;
							}
						}
					}
				}
			}
		}

		return $options_array;
	}

	/**
	 * FT Gallery Custom Post Type
	 *
	 * Create FT Gallery Albums custom post type
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_cpt() {
		$responses_cpt_args = array(
			'label'               => esc_html__( 'Albums', 'feed-them-gallery-premium' ),
			'labels'              => array(
				'menu_name'          => esc_html__( 'Albums', 'feed-them-gallery-premium' ),
				'name'               => esc_html__( 'Albums', 'feed-them-gallery-premium' ),
				'singular_name'      => esc_html__( 'Album', 'feed-them-gallery-premium' ),
				'add_new'            => esc_html__( 'Add Album', 'feed-them-gallery-premium' ),
				'add_new_item'       => esc_html__( 'Add New Album', 'feed-them-gallery-premium' ),
				'edit_item'          => esc_html__( 'Edit Album', 'feed-them-gallery-premium' ),
				'new_item'           => esc_html__( 'New Album', 'feed-them-gallery-premium' ),
				'view_item'          => esc_html__( 'View Album', 'feed-them-gallery-premium' ),
				'search_items'       => esc_html__( 'Search Albums', 'feed-them-gallery-premium' ),
				'not_found'          => esc_html__( 'No Albums Found', 'feed-them-gallery-premium' ),
				'not_found_in_trash' => esc_html__( 'No Albums Found In Trash', 'feed-them-gallery-premium' ),
			),

			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'show_in_menu'        => 'edit.php?post_type=ft_gallery',
			'show_in_nav_menus'   => true,
			'exclude_from_search' => true,

			'capabilities'        => array(
				'create_posts' => true, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )!
			),
			'map_meta_cap'        => true, // Allows Users to still edit Payments!
			'has_archive'         => true,
			'hierarchical'        => true,
			'query_var'           => 'ft_gallery_albums',
			'rewrite'             => array( 'slug' => 'ftg-album' ),

			'menu_icon'           => '',
			'supports'            => array( 'title', 'revisions' ),
			'order'               => 'DESC',
			// Set the available taxonomies here
			// 'taxonomies' => array('ft_gallery_topics')!
		);
		register_post_type( 'ft_gallery_albums', $responses_cpt_args );
	}

	/**
	 * FT Gallery Categories (Custom Taxonomy)
	 *
	 * Create FT Gallery Custom Taxonomy
	 *
	 * @since 1.0.2
	 */
	public function ft_gallery_albums_categories() {

		$labels = array(
			'name'              => esc_html__( 'Categories', 'feed-them-gallery-premium' ),
			'singular_name'     => esc_html__( 'Category', 'feed-them-gallery-premium' ),
			'search_items'      => esc_html__( 'Search Categories', 'feed-them-gallery-premium' ),
			'all_items'         => esc_html__( 'All Categories', 'feed-them-gallery-premium' ),
			'parent_item'       => esc_html__( 'Parent Category', 'feed-them-gallery-premium' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'feed-them-gallery-premium' ),
			'edit_item'         => esc_html__( 'Edit Category', 'feed-them-gallery-premium' ),
			'update_item'       => esc_html__( 'Update Category', 'feed-them-gallery-premium' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'feed-them-gallery-premium' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'feed-them-gallery-premium' ),
			'menu_name'         => esc_html__( 'Categories', 'feed-them-gallery-premium' ),
		);

		register_taxonomy(
			'ft_gallery_albums_cats',
			array( 'ft_gallery_albums' ),
			array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'register_taxonomy'     => true,
				'rewrite'               => array( 'slug' => 'ftg-album' ),
				'update_count_callback' => '_update_generic_term_count',
			)
		);
	}

	/**
	 * FT Gallery Register Taxonomy for Attachments
	 *
	 * Registers
	 *
	 * @since 1.0.2
	 */
	public function ft_gallery_albums_add_cats_to_attachments() {
		register_taxonomy_for_object_type( 'ft_gallery_albums_cats', 'attachment' );
		// add_post_type_support('attachment', 'ft_gallery_albums_cats');
	}

	/**
	 * FT Gallery Rename Submenu Name
	 * Renames the submenu item in the WordPress dashboard's menu
	 *
	 * @param $safe_text
	 * @param $text
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_rename_submenu_name( $safe_text, $text ) {
		if ( 'Galleries' !== $text ) {
			return $safe_text;
		}
		// We are on the main menu item now. The filter is not needed anymore.
		remove_filter( 'attribute_escape', array( $this, 'ft_gallery_albums_rename_submenu_name' ) );

		return 'FT Gallery';
	}

	/**
	 * FT Gallery Updated Messages
	 * Updates the messages in the admin area so they match plugin
	 *
	 * @param $messages
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_updated_messages( $messages ) {
		global $post, $post_ID;
		$messages['ft_gallery_albums'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => esc_html__( 'Album updated.', 'feed-them-gallery-premium' ),
			2  => esc_html__( 'Custom field updated.', 'feed-them-gallery-premium' ),
			3  => esc_html__( 'Custom field deleted.', 'feed-them-gallery-premium' ),
			4  => esc_html__( 'Album updated.', 'feed-them-gallery-premium' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Response restored to revision from %s', 'feed-them-gallery-premium' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => esc_html__( 'Album created.', 'feed-them-gallery-premium' ),
			7  => esc_html__( 'Album saved.', 'feed-them-gallery-premium' ),
			8  => esc_html__( 'Album submitted.', 'feed-them-gallery-premium' ),
			9  => esc_html__( 'Album scheduled for: <strong>%1$s</strong>.', 'feed-them-gallery-premium' ),
			// translators: Publish box date format, see http://php.net/date
			// date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => esc_html__( 'Album draft updated.', 'feed-them-gallery-premium' ),
		);

		return $messages;
	}

	/**
	 * FT Gallery Set Custom Edit Columns
	 *
	 * Sets the custom admin columns for gallery list page
	 *
	 * @param $columns
	 * @return array
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_set_custom_edit_columns( $columns ) {

		$new = array();

		foreach ( $columns as $key => $value ) {

			if ( 'title' === $key ) {
				$new[ $key ]              = $value;
				$new['gallery_thumb']     = '';
				$new['gallery_shortcode'] = esc_html__( 'Album Shortcode', 'feed-them-gallery-premium' );

			} else {
				$new[ $key ] = $value;
			}
		}

		return $new;
	}

	/**
	 * FT Gallery Count Post Images
	 * Return a count of images for our gallery list column.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_count_post_images( $post_id ) {
		$attachments = get_children(
			array(
				'post_parent'    => $post_id,
				'post_mime_type' => 'image',
			)
		);

		return count( $attachments );
	}


	/**
	 * FT Galley Custom Edit Column
	 * Put info in matching coloumns we set
	 *
	 * @param $column
	 * @param $post_id
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_custom_edit_column( $column, $post_id ) {
		switch ( $column ) {
			case 'gallery_thumb':
				$display_gallery   = new Gallery();
				$gallery_meta      = get_post( $post_id );
				$size              = 'ft_gallery_thumb';
				$img_url           = $this->gallery_featured_first( $gallery_meta->ID, $size );
				$album_gallery_ids = get_post_meta( $post_id, 'ft_gallery_album_gallery_ids', true );

				if ( ! empty( $album_gallery_ids ) ) {
					foreach ( $album_gallery_ids as $key => $gallery ) {

						$gallery_meta = get_post( $gallery->ID );

						if ( $gallery_meta ) {
							$img_url = $this->gallery_featured_first( $gallery_meta->ID, $size );
						}
					}

					?>
					<a href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>"><img
								src="<?php echo esc_url( $img_url ); ?>"
								alt=""/><?php echo esc_html( $display_gallery->ft_album_count_post_galleries( $post_id ) ); ?> <?php echo esc_html__( 'Galleries', 'feed-them-gallery-premium' ); ?>
					</a>
					<?php
				}

				break;
			// display a thumbnail photo!
			case 'gallery_shortcode':
				?>
				<input value="[ft-gallery-album id=<?php echo esc_html( $post_id ); ?>]" onclick="this.select()"/>
				<?php
				break;

			default:
				break;
		}
	}

	/**
	 * FT Gallery Set Button Text
	 * Set Edit Post buttons for Galleries custom post type
	 *
	 * @param $translated_text
	 * @param $text
	 * @param $domain
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_set_button_text( $translated_text, $text, $domain ) {
		$post_id          = isset( $_GET['post'] ) ? $_GET['post'] : '';
		$custom_post_type = get_post_type( $post_id );
		if ( ! empty( $post_id ) && 'ft_gallery_albums_responses' === $custom_post_type ) {
			switch ( $translated_text ) {
				case 'Publish':
					$translated_text = esc_html__( 'Save Album', 'feed-them-gallery-premium' );
					break;
				case 'Update':
					$translated_text = esc_html__( 'Update Album', 'feed-them-gallery-premium' );
					break;
				case 'Save Draft':
					$translated_text = esc_html__( 'Save Album Draft', 'feed-them-gallery-premium' );
					break;
				case 'Edit Payment':
					$translated_text = esc_html__( 'Edit Album', 'feed-them-gallery-premium' );
					break;
			}
		}

		return $translated_text;
	}

	/**
	 * FT Gallery Scripts
	 *
	 * Create Gallery custom post type
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_scripts() {

		// Get current screen.
		$current_screen = get_current_screen();

		if ( 'ft_gallery_albums' === $current_screen->post_type && 'post' === $current_screen->base && is_admin() ) {

			wp_enqueue_style( 'ft-gallery-albums-admin-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css', array(), FTG_CURRENT_VERSION );

			wp_register_script( 'ft-gallery-albums-btns-js', plugins_url( 'feed-them-gallery-premium/includes/albums/js/albums.js' ), array(), FTG_CURRENT_VERSION );
			wp_enqueue_script( 'ft-gallery-albums-btns-js' );

		}
	}

	/**
	 * Add Gallery Meta Boxes
	 *
	 * Add metaboxes to the gallery
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_add_metaboxes() {
		global $post;
		// Check we are using Feed Them Gallery Custom Post type!
		if ( 'ft_gallery_albums' !== $post->post_type ) {
			return;
		}

		// Image Uploader and Gallery area in admin!
		add_meta_box( 'ft-galleries-upload-mb', esc_html__( 'Feed Them Gallery Settings', 'feed-them-gallery-premium' ), array( $this, 'ft_gallery_tab_menu_metabox' ), 'ft_gallery_albums', 'normal', 'high', null );

		// Link Settings Meta Box!
		add_meta_box( 'ft-galleries-shortcode-side-mb', esc_html__( 'Album Shortcode', 'feed-them-gallery-premium' ), array( $this, 'ft_gallery_albums_shortcode_meta_box' ), 'ft_gallery_albums', 'side', 'high', null );
	}



	/**
	 * Gallery Featured First
	 *
	 * Look for Gallery featured image if not set get first image of first attachment in list
	 *
	 * @param $gallery_id
	 * @param string     $size
	 * @param string     $type
	 * @return string
	 * @since 1.1.6
	 */
	public function gallery_featured_first( $gallery_id, $size = 'ft_gallery_thumb', $type = 'image' ) {

		// Gallery has a featured image so lets show it!
		if ( has_post_thumbnail( $gallery_id ) ) {
			$image_url = get_the_post_thumbnail_url( $gallery_id, $size );
		}
		// Nope, Gallery doesn't have a featured image so lets get first image attachement url!
		else {
			$gallery_class = new Gallery();

			$attached_media = $gallery_class->ft_gallery_get_gallery_attached_media_ids( $gallery_id, $type );
			if ( isset( $attached_media[0] ) ) {
				$first_attachement = $gallery_class->ft_gallery_get_attachment_info( $attached_media[0] );

				$first_img = wp_get_attachment_image_src( $first_attachement['ID'], $size );
				$image_url = $first_img[0];
			}
		}
			return isset( $image_url ) ? $image_url : '';
	}


	/**
	 * FT Gallery Metabox Tabs List
	 *
	 * The list of tabs Items for settings page metaboxes
	 *
	 * @return array
	 * @since 1.1.6
	 */
	public function ft_gallery_metabox_tabs_list() {

		$metabox_tabs_list = array(
			// Base of each tab! The array keys are the base name and the array value is a list of tab keys!
			'base_tabs' => array(
				'post' => array( 'galleries', 'layout', 'colors', 'watermark', 'pagination', 'clients' ),
			),
			// Tabs List! The cont_func item is relative the the Function name for that tabs content. The array Keys for each tab are also relative to classes and ID on wraps of display_metabox_content function!
			'tabs_list' => apply_filters( 'ftg_premium_metabox_tabs_list', array(
				// Images Tab!
				'galleries'  => apply_filters( 'ftg_premium_metabox_tab_galleries', array(
					'menu_li_class'      => 'tab1',
					'menu_a_text'        => esc_html__( 'Galleries', 'feed-them-gallery-premium' ),
					'menu_a_class'       => 'account-tab-highlight',
					'menu_aria_expanded' => 'true',
					'cont_wrap_id'       => 'ftg-tab-content1',
					'cont_func'          => 'tab_gallery_select_content',
				) ),
				// Layout Tab!
				'layout'     => apply_filters( 'ftg_premium_metabox_tab_layout', array(
					'menu_li_class' => 'tab2',
					'menu_a_text'   => esc_html__( 'Layout', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content2',
					'cont_func'     => 'tab_layout_content',
				) ),
				// Colors Tab!
				'colors'     => apply_filters( 'ftg_premium_metabox_tab_colors', array(
					'menu_li_class' => 'tab3',
					'menu_a_text'   => esc_html__( 'Colors', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content3',
					'cont_func'     => 'tab_colors_content',
				) ),
				// Watermark Tab!
				'watermark'  => apply_filters( 'ftg_premium_metabox_tab_watermark', array(
					'menu_li_class' => 'tab6',
					'menu_a_text'   => esc_html__( 'Watermark', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content6',
					'cont_func'     => 'tab_watermark_content',
				) ),
				// Pagination Tab!
				'pagination' => apply_filters( 'ftg_premium_metabox_tab_pagination', array(
					'menu_li_class' => 'tab7',
					'menu_a_text'   => esc_html__( 'Pagination', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content8',
					'cont_func'     => 'tab_pagination_content',
				) ),
                'clients'     => apply_filters( 'ftg_premium_metabox_tab_clients', array(
					'menu_li_class' => 'tab9',
					'menu_a_text'   => esc_html__( 'Clients', 'feed-them-gallery-premium' ),
					'cont_wrap_id'  => 'ftg-tab-content10',
					'cont_func'     => 'tab_premium_extension_required',
				) )
			) )
		);

		return $metabox_tabs_list;
	}

	/**
	 * FT Gallery Tab Menu Metabox
	 *
	 * Creates the Tabs Menu Metabox
	 *
	 * @param $object
	 * @since 1.0.0
	 */
	public function ft_gallery_tab_menu_metabox( $object ) {

		// Popup HTML
		// $this->ft_gallery_edit_page_popup( $this->parent_post_id );
		$params['object'] = $object;

		$this->metabox_settings_class->display_metabox_content( $this->ft_gallery_metabox_tabs_list(), $params );

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
	 * Tab Gallery Select Content
	 *
	 * Select galleries to be added into this album.
	 *
	 * @param $params
	 * @since 1.0.0
	 */
	public function tab_gallery_select_content( $params ) {

		$object       = $params['object'];
		$albums_class = $params['this'];
		?>

			<div class="ftg-section">
				<div id="ftg-galleries-in-album">
					<?php

					// Happens in JS file!
					$albums_class->ft_gallery_albums_tab_notice_html();

					$album_gallery_ids = get_post_meta( $object->ID, 'ft_gallery_album_gallery_ids', true );
					?>

					<?php
					// adjust values here!
					$svalue = ''; // this will be initial value of the above form field. Image urls.

					$args         = array(
						'post_type'      => 'ft_gallery',
						'posts_per_page' => -1,
						'orderby'        => 'menu_order',
						'order'          => 'asc',
						'exclude'        => 0, // Exclude featured thumbnail!
					);
					$gallery_list = get_posts( $args );

					// echo '<pre>';
					// print_r($album_gallery_ids);
					// echo '</pre>';
					$ftg_count_plural_check = count( 1 ) === $album_gallery_ids ? ' Gallery ' : ' Galleries ';

					// echo '<pre>';
					// print_r($album_gallery_ids);
					// echo '</pre>';
					if ( ! empty( $album_gallery_ids ) ) {
						?>

						<h3 style="padding-bottom: 0;">Galleries in Album</h3>
						<p>To remove Galleries from this album click the Select All button or any image below then click the Remove Galleries from Album button.<br/>You can sort the galleries by dragging them in the order you would like to see.</p>

						<div class="ft-gallery-options-buttons-wrap">
							<div class="gallery-edit-button-wrap">
								<button type="button" class="button"
										id="fts-gallery-checkAll"><?php echo esc_html__( 'Select All', 'feed-them-gallery-premium' ); ?></button>
							</div>
							<div class="gallery-edit-button-wrap">
								<button type="button" disabled="disabled"
										class="ft-remove-gallery-to-album button button-primary button-larg"
										onclick="ft_gallery_delete_galleries_from_album('<?php echo esc_html( $albums_class->parent_post_id ); ?>')"><?php echo esc_html( 'Remove ' . $ftg_count_plural_check . ' from Album', 'feed-them-gallery-premium' ); ?></button>
							</div>
						</div>

						<div class="album-galleries">
							<ul class="plupload-thumbs" id="img1plupload-thumbs"
								data-post-id="<?php echo esc_html( $object->ID ); ?>">
								<?php

								$show_title = get_post_meta( $object->ID, 'ft_gallery_albums_show_title', true );

								// Display Images Gallery!
								$size = 'ft_gallery_thumb';

								// && isset($gallery_list[0])
								if ( is_array( $album_gallery_ids ) && isset( $album_gallery_ids ) ) {

									// echo '<pre>';
									// print_r($album_gallery_ids);
									// echo '</pre>';
									foreach ( $album_gallery_ids as $key => $gallery ) {

										$gallery_meta = get_post( $gallery->ID );

										if ( $gallery_meta ) {
											$gallery_img_url = $albums_class->gallery_featured_first( $gallery_meta->ID, $size );

											$gallery_attachments_count = $albums_class->ft_gallery_count_gallery_attachments( $gallery_meta->ID );

											$gallery_edit_url = get_edit_post_link( $gallery_meta->ID );

											?>
											<li class="thumb in-album" id="list_item_<?php echo esc_html( $gallery->ID ); ?>" data-image-id="<?php echo esc_html( $gallery->ID ); ?>" data-menu-order="<?php echo esc_html( $gallery->menu_order ); ?>">
												<img src="<?php echo esc_html( $gallery_img_url ); ?>"/>
												<div class="ft-gallery-edit-thumb-btn"><a href="<?php echo esc_html( $gallery_edit_url ); ?>" class="ft-gallery-edit-img-popup" title="Edit Gallery" target="_blank">
														<span class="ftg-gallery-images-count"><?php echo esc_html__( 'Images:', 'feed-them-gallery-premium' ); ?> <?php echo esc_html( $gallery_attachments_count ); ?></span></a></div>
												<div class="ft-gallery-select-thumbn"><label class="ft-gallery-myCheckbox"><input type="checkbox" class=“ft-gallery-img-checkbox” rel="<?php echo esc_html( $gallery->ID ); ?>" name="image-<?php echo esc_html( $gallery->ID ); ?>" id="image-<?php echo esc_html( $gallery->ID ); ?>"/><span></span></label></div>
											</li>
											<?php
										}
									}
								}
								?>
							</ul>
						</div>

						<div class="clear"></div>

						<?php
						if ( is_array( $gallery_list ) && true == $object->ID && isset( $gallery_list[0] ) ) {
							?>
						<div class="ftg-number-of-images-wrap"><?php echo esc_html( count( $album_gallery_ids ) ); ?>
							<?php echo esc_html( $ftg_count_plural_check . 'in Album', 'feed-them-gallery-premium' ); ?>
						</div>
							<?php
						}
						?>
						<!-- srl -->
						<input type="hidden" name="<?php echo isset( $gallery_meta->ID ) ? esc_html( $gallery_meta->ID ) : ''; ?>"
							   id="<?php echo isset( $gallery_meta->ID ) ? esc_html( $gallery_meta->ID ) : ''; ?>"
							   value="<?php echo isset( $gallery_meta->ID ) ? esc_html( $svalue ) : ''; ?>"/>

						<div class="clear"></div>
						<script>
							jQuery('.metabox_submit').click(function (e) {
								e.preventDefault();
								//  jQuery('#publish').click();
								jQuery('#post').click();
							});

						</script>
						<?php
					}
					?>
				</div>

				<div id="ftg-albums-available">
					<h3 style="padding-bottom: 0;">Galleries Available</h3>
					<p>Click on an any image below or the Select All button and then click the Add Galleries to Album button.</p>
					<input type="submit" class="metabox_submit" value="Submit" style="display: none;" />
					<div class="ft-gallery-options-buttons-wrap">
						<div class="gallery-edit-button-wrap">
							<button type="button" class="button" id="fts-gallery-checkAll2"><?php echo esc_html__( 'Select All', 'feed-them-gallery-premium' ); ?></button>
						</div>
						<div class="gallery-edit-button-wrap">
							<button type="button" disabled="disabled" class="ft-add-gallery-to-album button button-primary button-larg" onclick="ft_gallery_add_galleries_to_album('<?php echo esc_html( $albums_class->parent_post_id ); ?>')"><?php echo esc_html__( 'Add Galleries to Album', 'feed-them-gallery-premium' ); ?></button>
						</div>
					</div>


					<ul class="plupload-thumbs ftg-available-galleries" id="img2plupload-thumbs" data-post-id="<?php echo esc_html( $object->ID ); ?>">
						<?php

						// Display Images Gallery!
						$size = 'ft_gallery_thumb';

						if ( is_array( $gallery_list ) && isset( $gallery_list[0] ) && ! empty( $gallery_list ) ) {
							$count_me = 0;
							foreach ( $gallery_list as $key => $gallery ) {

								$album_gallery_ids = is_array( $album_gallery_ids ) ? $album_gallery_ids : array();

								// Check if Gallery ID is already in album or album's gallery is isn't created yet!
								if ( ! isset( $album_gallery_ids ) || isset( $album_gallery_ids ) && isset( $album_gallery_ids ) && ! in_array( $gallery->ID, array_column( $album_gallery_ids, 'ID' ) ) ) {

									$gallery_attachments_count = $albums_class->ft_gallery_count_gallery_attachments( $gallery->ID );

									$gallery_img_url = $albums_class->gallery_featured_first( $gallery, $size );

									$gallery_edit_url = get_edit_post_link( $gallery->ID );

									?>
									<li class="thumb out-album" id="list_item_<?php echo esc_html( $gallery->ID ); ?>" data-image-id="<?php echo esc_html( $gallery->ID ); ?>" data-menu-order="0">
										<img src="<?php echo esc_html( $gallery_img_url ); ?>"/>
										<div class="ft-gallery-edit-thumb-btn"><a href="<?php echo esc_html( $gallery_edit_url ); ?>" title="Edit Gallery" target="_blank">
												<span class="ftg-gallery-images-count"><?php echo esc_html__( 'Images:', 'feed-them-gallery-premium' ); ?> <?php echo esc_html( $gallery_attachments_count ); ?></span></a></div>
										<div class="ft-gallery-select-thumbn"><label class="ft-gallery-myCheckbox"><input type="checkbox" class=“ft-gallery-img-checkbox” rel="<?php echo esc_html( $gallery->ID ); ?>" name="image-<?php esc_html( $gallery->ID ); ?>" id="image-<?php esc_html( $gallery->ID ); ?>"/><span></span></label></div>
									</li>
									<?php
									// we use the count me to output the number of galleries available and not used.
									$count_me ++;
								}
							}
						} else {
							?>
						<li><?php echo esc_html__( 'No Galleries to add to this Album.', 'feed-them-gallery-premium' ); ?></li>
							<?php
						}
						?>
					</ul>
					<div class="clear"></div>

					<?php $ftg_count_plural_check2 = isset( $count_me ) && 1 === $count_me ? 'Gallery ' : 'Galleries '; ?>
					<div class="ftg-number-of-images-wrap ftg-available-galleries-count"><?php echo isset( $count_me ) ? esc_html( $count_me ) : '0'; ?>
						<?php
						echo esc_html( $ftg_count_plural_check2 . 'Available', 'feed-them-gallery-premium' );
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		<?php
	}

	/**
	 * Tab Layout Content
	 *
	 * Albums Layout options
	 *
	 * @param $params
	 * @since 1.0.0
	 */
	public function tab_layout_content( $params ) {

		$albums_class = $params['this'];

		// echo '<pre>';
		// print_r($albums_class);
		// echo '</pre>';
		echo $albums_class->metabox_settings_class->settings_html_form( $albums_class->saved_settings_array['layout'], null, $albums_class->parent_post_id );
		?>

		<div class="clear"></div>
		<div class="ft-gallery-note ft-gallery-note-footer">
			<?php
			echo sprintf(
				esc_html__( 'Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery-premium' ),
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery_albums&page=ft-gallery-settings-page' ) . '" >',
				'</a>'
			);
			?>
		</div>

		<?php
	}

	/**
	 * Tab Colors Content
	 *
	 * Albums Colors Options
	 *
	 * @param $params
	 * @since 1.0.0
	 */
	public function tab_colors_content( $params ) {

		$albums_class = $params['this'];

		echo $albums_class->metabox_settings_class->settings_html_form( $albums_class->saved_settings_array['colors'], null, $albums_class->parent_post_id );
		?>

		<div class="clear"></div>
		<div class="ft-gallery-note ft-gallery-note-footer">
			<?php
			echo sprintf(
				esc_html__( 'Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery-premium' ),
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery_albums&page=ft-gallery-settings-page' ) . '" >',
				'</a>'
			);
			?>
		</div>

		<?php
	}

	/**
	 * Tab Watermark Content
	 *
	 * Outputs Watermark tab's content for metabox.
	 *
	 * @since 1.0.0
	 */
	public function tab_watermark_content( $params ) {

		$albums_class = $params['this'];

		echo $albums_class->metabox_settings_class->settings_html_form( $albums_class->saved_settings_array['watermark'], null, $albums_class->parent_post_id );
		?>

		<div class="clear"></div>
		<div class="ft-gallery-note ft-gallery-note-footer">
			<?php

			// echo '<pre>';
			// print_r( $gallery_class->metabox_settings_class->get_saved_settings_array( $gallery_class->parent_post_id ) );
			// echo '</pre>';
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
	 * Tab Pagination Content
	 *
	 * Albums Pagination Options
	 *
	 * @param $params
	 * @since 1.0.0
	 */
	public function tab_pagination_content( $params ) {

		$albums_class = $params['this'];

		if ( ! is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
			?>
			<div class="ftg-section">
				<?php $this->ft_gallery_albums_tab_premium_msg(); ?>
			</div>
			<?php
		}
		 echo $albums_class->metabox_settings_class->settings_html_form( $albums_class->saved_settings_array['pagination'], null, $albums_class->parent_post_id );
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
     * Premium plugin required tab content.
     *
     * @since   1.3.5
     * @param   array   $params Array of tab option params
     * @param   string  $tab    Current tab
     * @return  void
     */
    public function tab_premium_extension_required( $params, $tab )   {
        $albums_class = $params['this'];
        $plugin        = '';

        if ( ! empty( $albums_class->saved_settings_array[ $tab ]['required_prem_plugin'] ) )  {
            $plugin = $albums_class->saved_settings_array[ $tab ]['required_prem_plugin'];
            ?>
            <div class="ftg-section">
                <?php $albums_class->requires_extension( $plugin ); ?>
            </div>
            <?php
        }

        echo $albums_class->metabox_settings_class->settings_html_form(
			$albums_class->saved_settings_array[ $tab ],
			null,
			$albums_class->parent_post_id
		);

    } // tab_premium_extension_required

	/**
	 * FT Gallery Tab Notice HTML
	 *
	 * creates notice html for return
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_tab_notice_html() {
		echo '<div class="ft-gallery-notice"></div>';
	}

	/**
	 * FT Gallery Shortcode Meta Box
	 *
	 * FT Gallery copy & paste shortcode input box
	 *
	 * @param $object
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_shortcode_meta_box( $object ) {
		?>
			<div class="ft-gallery-meta-wrap">
				<?php
				$gallery_id = isset( $_GET['post'] ) ? $_GET['post'] : '';

				$screen = get_current_screen();

				if ( 'edit.php?post_type=ft_gallery_albums' === $screen->parent_file && 'add' === $screen->action ) {
					?>
				<p>
					<label><?php echo esc_html__( 'Save or Publish this Album to be able to copy this Album\'s Shortcode.', 'feed-them-gallery-premium' ); ?></label>
				</p>
					<?php
				} else {
					// Copy Shortcode.
					?>
				<p>
					<label><?php echo esc_html__( 'Copy and Paste this shortcode to any page, post or widget.', 'feed-them-gallery-premium' ); ?></label>
					<input readonly="readonly" value="[ft-gallery-album id=<?php echo esc_html( $gallery_id ); ?>]" onclick="this.select();" name="ft_album_shortcode" id="ft-album-shortcode"/>
				</p>
					<?php
				}
				?>
			</div>
		<?php
	}

	/**
	 * Duplicate Album As Draft
	 *
	 * Function creates post duplicate as a draft and redirects then to the edit post screen
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_duplicate_post_as_draft() {
		global $wpdb;
		if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'ft_gallery_albums_duplicate_post_as_draft' === $_REQUEST['action'] ) ) ) {
			wp_die( esc_html__( 'No Album to duplicate has been supplied!', 'feed-them-gallery-premium' ) );
		}

		/*
		 * Nonce verification
		 */
		if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		/*
		 * get the original post id
		 */
		$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
		/*
		 * and all the original post data then
		 */
		$post = get_post( $post_id );

		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;

		/*
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $post ) && null !== $post ) {

			/*
			 * new post data array
			 */
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order,
			);

			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );

			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies( $post->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			/*
			 * duplicate all post meta just in two SQL queries
			 */
			$post_meta_results = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id ) );

			if ( 0 !== count( $post_meta_results ) ) {
				foreach ( $post_meta_results as $meta_info ) {
					if ( '_wp_old_slug' === $meta_info->meta_value ) {
						continue;
					}
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value) VALUES ( %d, %s, %s )",
							$new_post_id,
							$meta_info->meta_key,
							$meta_info->meta_value
						)
					);
				}
			}

			/*
			 * finally, redirect to the edit post screen for the new draft
			 */
			wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		}

		wp_die( esc_html( 'Album duplication failed, could not find original Album: ' . $post_id, 'feed-them-gallery-premium' ) );

	}

	 /**
	  * FT Gallery Duplicate Post Link
	  *
	  * Add the duplicate link to action list for post_row_actions
	  *
	  * @param $actions
	  * @param $post
	  * @return mixed
	  * @since 1.0.0
	  */
	public function ft_gallery_albums_duplicate_post_link( $actions, $post ) {
		// make sure we only show the duplicate gallery link on our pages.
		if ( 'ft_gallery_albums' === $_GET['post_type'] && current_user_can( 'edit_posts' ) ) {
			$actions['duplicate'] = '<a id="ft-gallery-duplicate-action" href="' . esc_url( wp_nonce_url( 'admin.php?action=ft_gallery_albums_duplicate_post_as_draft&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) ) . '" title="Duplicate this item" rel="permalink">' . esc_html( 'Duplicate', 'feed-them-gallery-premium' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * FT Gallery Duplicate Post ADD Duplicate Post Button
	 * Add a button in the post/page edit screen to create a clone
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_albums_duplicate_post_add_duplicate_post_button() {
		$current_screen = get_current_screen();
		$verify         = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
		// check to make sure we are not on a new ft_gallery post, because what is the point of duplicating a new one until we have published it?
		if ( 'ft_gallery_albums' === $current_screen->post_type && 'ft_gallery_albums' !== $verify ) {
			$id = $_GET['post'];
			?>
			<div id="ft-gallery-duplicate-action">
				<a href="<?php echo esc_url( wp_nonce_url( 'admin.php?action=ft_gallery_albums_duplicate_post_as_draft&post=' . $id, basename( __FILE__ ), 'duplicate_nonce' ) ); ?>" title="Duplicate this item" rel="permalink"><?php esc_html_e( 'Duplicate Album', 'feed-them-gallery-premium' ); ?></a>
			</div>
			<?php
		}
	}

	/**
	 * Metabox Specific Form Inputs
	 *
	 * This adds to the ouput of the metabox output forms for settings_html_form function in the Metabox Settings class.
	 *
	 * @param $params
	 * @param $input_option
	 * @return
	 * @since 1.1.6
	 */
	public function metabox_specific_form_inputs( $params ) {
		// 'This' Class object.
		$album_class = $params['this'];
		// Album ID.
		$album_id = isset( $_GET['post'] ) ? $_GET['post'] : '';

		// Gallery Options (REST API call).
		$gallery_options_returned = $album_class->ft_gallery_get_albums_options_rest( $album_id );

		// Option Info.
		$option = $params['input_option'];

		$output = '';

		if ( isset( $option['option_type'] ) && 'ft-images-sizes-page' === $option['option_type'] ) {
				// Image sizes for page.
					$final_value_images = $gallery_options_returned['ft_gallery_images_sizes_page'];
					$output            .= '<select name="ft_gallery_images_sizes_page" id="ft_gallery_images_sizes_page"  class="feed-them-gallery-admin-input">';

					global $_wp_additional_image_sizes;

					$sizes = array();
			$output       .= '<option value="Choose an option" ' . ( 'not_set' === $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Choose an option', 'feed-them-gallery-premium' ) . '</option>';

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

				$current_selected_size = $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'];

				$output .= '<option value="' . esc_attr( $_size ) . '" ' . selected( $_size, $final_value_images, false ) . '>' . esc_html( $_size ) . ' ' . esc_html( $sizes[ $_size ]['width'] ) . ' x ' . esc_html( $sizes[ $_size ]['height'] ) . '</option>';
			}

			$output .= '<option value="full" ' . ( 'full' === $final_value_images ? 'selected="selected"' : '' ) . '>' . esc_html__( 'full', 'feed-them-gallery-premium' ) . '</option>';
					// TESTING AREA
					// echo $final_value_images;
					 // echo '<pre>';
					 // print_r($sizes);
					 // echo '</pre>';
					$output .= '</select>';
		}

		return $output;
	}

	/**
	 * Displays albums when the [ftg_client_galleries] shortcode is displayed.
	 *
	 * @since	1.1.5
	 * @param	object	$client	FTG_CM_Client object
	 * @return	void
	 */
	public function client_galleries_shortcode( $client )	{
		$albums = $client->get_items( 'albums' );

		ob_start(); ?>
		<div class="ftg-your-albums-wrap"><h3><?php _e( 'Your Albums', 'feed-them-gallery-premium' ); ?></h3>

		<?php if ( empty( $albums ) ) :
			$no_albums = __( 'You do not currently have access to any albums', 'feed-them-gallery-premium' );
			$no_albums = apply_filters( 'ftg_cm_no_albums_notice', $no_albums );
		?>
			<p><?php echo $no_albums; ?></p>
		<?php else : ?>
			<ul>
				<?php foreach( $albums as $album ) : ?>
					<li>
						<a href="<?php echo get_permalink( $album->ID ); ?>">
							<?php echo get_the_title( $album ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		</div>
		<?php echo ob_get_clean();
	} // client_galleries_shortcode

} ?>
