<?php
namespace feed_them_gallery;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Gallery To WooCommerce Class
 *
 * This class extends the Gallery class to give ability to create WooCommerce Products from Feed Them Gallery attachments.
 *
 * @version  1.0.2
 * @package  FeedThemSocial/WooCommerce
 * @author   SlickRemix
 */
class Gallery_to_Woocommerce extends Gallery {

	/**
	 * Gallery_to_Woocommerce constructor.
	 */
	public function __construct() {

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			// Image to Woo AJAX.
			add_action( 'wp_ajax_ft_gallery_image_to_woo_prod', array( $this, 'ft_gallery_image_to_woo_prod' ) );
			add_action( 'wp_ajax_nopriv_ft_gallery_image_to_woo_prod', array( $this, 'ft_gallery_image_to_woo_prod' ) );

			// Zip to Woo AJAX.
			add_action( 'wp_ajax_ft_gallery_zip_to_woo_prod', array( $this, 'ft_gallery_zip_to_woo_prod' ) );
			add_action( 'wp_ajax_nopriv_ft_gallery_zip_to_woo_prod', array( $this, 'ft_gallery_zip_to_woo_prod' ) );

			add_action( 'add_meta_boxes', array( $this, 'ft_gallery_add_woo_metabox' ) );

			add_action( 'save_post', array( $this, 'ft_gallery_woo_metabox_save' ) );
		}

		// Enqueue JS.
		add_action( 'admin_enqueue_scripts', array( $this, 'ft_gallery_to_woo_scripts' ) );
	}

	/**
	 * FT Gallery to Woo Ajax
	 * Adds actions to WordPress for Ajax
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_to_woo_ajax() {
		// Image to Woo AJAX.
		add_action( 'wp_ajax_ft_gallery_image_to_woo_prod', array( $this, 'ft_gallery_image_to_woo_prod' ) );
		add_action( 'wp_ajax_nopriv_ft_gallery_image_to_woo_prod', array( $this, 'ft_gallery_image_to_woo_prod' ) );
		// Zip to Woo AJAX.
		add_action( 'wp_ajax_ft_gallery_zip_to_woo_prod', array( $this, 'ft_gallery_zip_to_woo_prod' ) );
		add_action( 'wp_ajax_nopriv_ft_gallery_zip_to_woo_prod', array( $this, 'ft_gallery_zip_to_woo_prod' ) );
	}

	/**
	 * FT Gallery To Woo Scripts
	 *
	 * Enqueue Ajax to Admin and Frontend
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_to_woo_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'ft_gallery_to_woo', plugins_url( 'feed-them-gallery/includes/js/gallery-to-woo.js', true ) );

		// Localize the script with new data. We use this in our gallery-to-woo.js file in this plugin.
		$ftg_translation_array = array(
			'images_products_complete_on_auto_upload'      => sprintf(
				esc_html__( 'The Image(s) are done uploading and all the Product(s) are now complete. Click the cart icon below on any image to edit that product or you can view the product(s) on the %1$sWooCommerce Products Page%2$s. Please refresh the page to edit images.', 'feed-them-gallery-premium' ),
				'<a href="' . esc_url( 'edit.php?post_status=publish&post_type=product&orderby=date&order=desc' ) . '" target="_blank">',
				'</a>'
			),
			'images_products_already_complete_using_button' => __( 'The other checked image(s) have existing products.', 'feed-them-gallery-premium' ),
			'images_products_complete_using_button'        => sprintf(
				esc_html__( 'Image Product(s)%2$s created. Click the cart icon below on any image to edit that product or you can view them on the %3$sWooCommerce Products Page%4$s.', 'feed-them-gallery-premium' ),
				'<strong>',
				'</strong>',
				'<a href="' . esc_url( 'edit.php?post_status=publish&post_type=product&orderby=date&order=desc' ) . '" target="_blank">',
				'</a>'
			),
			'images_products_already_created'              => esc_html__( 'The items(s) you have checked already have Products created for them.', 'feed-them-gallery-premium' ),
			'global_product_option'                        => esc_html__( 'Edit Model Product', 'feed-them-gallery-premium' ),
			'admin_url'                                    => admin_url(),
			// error message used on metabox.js file if users did not select any global options or did not check the smart image orientation checkbox and did not select a smart image option.
			'must_have_option_selected_to_create_products' => sprintf(
				esc_html__( 'Oops, to create products on upload please go to our %1$sWooCommerce tab%2$s and choose a Global Product or Smart Image Orientation Product. Once you have done that you can come back here and select the images that did not get products created for them and click the "Create Individual Products" button.', 'feed-them-gallery-premium' ),
				'<a href="#woocommerce" class="ftg-woo-tab" target="_blank">',
				'</a>'
			),

		);
		wp_localize_script( 'ft_gallery_to_woo', 'ftg_woo', $ftg_translation_array );

		wp_enqueue_script( 'ft_gallery_to_woo', plugins_url( 'feed-them-gallery/includes/js/gallery-to-woo.js', true ), array(), FTGP_CURRENT_VERSION, false );

		wp_localize_script( 'ft_gallery_to_woo', 'ftgallerytoWooAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * FT Gallery Insert Woo Category Term
	 *
	 * Inserts a WooCommerce category term into woo's taxonomy
	 *
	 * @param string $gallery_id The Gallery ID.
	 * @return string
	 * @since 1.0.2
	 */
	public function ft_gallery_insert_woo_term( $gallery_id ) {

		$term_name = get_the_title( $gallery_id );
		// Make sure there is a Gallery Title set.
		if ( isset( $term_name ) && ! empty( $term_name ) ) {
			// Set the Woo Term.
			$term = wp_insert_term(
				$term_name,
				'product_cat',
				[
					'description' => $term_name,
					'slug'        => sanitize_title( esc_html( $term_name ) ),
				]
			);

			// Check for Errors.
			if ( is_wp_error( $term ) ) {
				$term_id = isset( $term->error_data['term_exists'] ) ? $term->error_data['term_exists'] : null;
			} else {
				$term_id = $term['term_id'];
			}

			return $term_id;
		}

		return 'no_gallery_title';
	}

	/**
	 * Smart Image Orientation List
	 *
	 * List of Image Orientations
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function ft_gallery_smart_image_orientation_list() {
		$orientation_list = array( 'global', 'landscape', 'square', 'portrait' );

		return $orientation_list;
	}

	/**
	 * Model Product Type List
	 *
	 * List of Model Product Types
	 *
	 * @param null $orientation Accepts: global | landscape | square | portrait.
	 * @param null $orientation_spec_info Accepts: name | type | field_name | special_note.
	 * @return array|mixed
	 * @since 1.0.2
	 */
	public function ft_gallery_model_product_type_list( $orientation = null, $orientation_spec_info = null ) {
		$model_product_list = array(
			'global'    => array(
				'name'       => __( 'Global', 'feed-them-gallery-premium' ),
				'type'       => 'global',
				'field_name' => 'ft_gallery_image_to_woo_model_prod',
			),
			'landscape' => array(
				'name'       => __( 'Landscape', 'feed-them-gallery-premium' ),
				'type'       => 'landscape',
				'field_name' => 'ft_gallery_landscape_to_woo_model_prod',
			),
			'square'    => array(
				'name'       => __( 'Square', 'feed-them-gallery-premium' ),
				'type'       => 'square',
				'field_name' => 'ft_gallery_square_to_woo_model_prod',
			),
			'portrait'  => array(
				'name'       => __( 'Portrait', 'feed-them-gallery-premium' ),
				'type'       => 'portrait',
				'field_name' => 'ft_gallery_portrait_to_woo_model_prod',
			),
			'zip'       => array(
				'name'         => __( 'ZIP', 'feed-them-gallery-premium' ),
				'type'         => 'zip',
				'field_name'   => 'ft_gallery_zip_to_woo_model_prod',
                'special_note' => sprintf(
                    esc_html__( 'NOTE: A Zip Model Product must have the options %1$sVirtual%2$s AND %3$sDownloadable%4$s checked to appear in ZIP Model Product select option. No Download link is needed in product though as it will be auto-filled in when Feed Them Gallery creates a new ZIP product based on the ZIP\'s location.', 'feed-them-gallery-premium' ),
                    '<a href="' . esc_url( 'https://docs.woocommerce.com/document/managing-products/#section-14' ) . '">',
                    '</a>',
                    '<a href="' . esc_url( 'https://docs.woocommerce.com/document/managing-products/#section-15' ) . '">',
                    '</a>'
                ),
			),
		);

		if ( $orientation ) {
			// Return Specific Orientation info.
			if ( $orientation_spec_info ) {
				return $model_product_list[ $orientation ][ $orientation_spec_info ];
			}

			// Return All Orientation info.
			return $model_product_list[ $orientation ];
		}

		return $model_product_list;
	}

	/**
	 * Smart Image Orientation
	 *
	 * Finds out the orientation of an image
	 *
	 * @param string $file The file name to check for.
	 * @return string
	 * @since 1.0.2
	 */
	public function ft_gallery_smart_image_orientation( $file ) {
		// Make sure its a valid image (Full path to the file required.).
		$valid_image = wp_get_image_mime( $file );

		if ( $valid_image ) {
			list($width, $height) = getimagesize( $file );
			// Landscape Image.
			if ( $width > $height ) {
				return 'landscape';
			}
			// Square Image.
			if ( $width === $height ) {
				return 'square';
			}
			// Portrait Image.
			if ( $width < $height ) {
				return 'portrait';
			}
		}

		return 'global';
	}

	/**
	 * FT Gallery Image To WooCommerce Product
	 *
	 * Creates a WooCommerce product from a Feed Them Gallery Image attachment
	 *
	 * @param string $gallery_id The gallery ID.
	 * @param string $orientation The orientation of the image.
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_image_to_woo_model_prod_select( $gallery_id, $orientation = 'global' ) {

		$products = get_option( 'feed-them-gallery-model-product-list' );

		$select_field_name = $this->ft_gallery_model_product_type_list( $orientation, 'field_name' );

		$select_build = '<select name="' . $select_field_name . '" id="' . $select_field_name . '" class="feed-them-social-admin-input"' . ( isset( $products ) && is_array( $products ) ? '' : ' disabled' ) . '>';

		$display_gallery = new Display_Gallery();
		$option          = $display_gallery->ft_gallery_get_option_or_get_postmeta( $gallery_id );

		$selected_product = isset( $option[ $select_field_name ] ) ? $option[ $select_field_name ] : '';

		$select_build .= '<option ' . ( empty( $selected_product ) || ! empty( $selected_product ) && isset( $products[ $orientation ] ) && ! in_array( $selected_product, $products[ $orientation ] ) ? 'selected="selected "' : '' ) . 'value=""> ' . esc_attr( __( 'Select a Product', 'feed-them-gallery-premium' ) ) . '</option>';

		// Check Products array isset and has products under this orientation.
		if ( isset( $products[ $orientation ] ) ) {
			foreach ( $products[ $orientation ] as $product_id => $product_value ) {
				$product_object = wc_get_product( $product_id );
				if ( $product_object ) {
					$select_build .= '<option value="' . $product_id . '" ';
					// must use == as === will not work (strict comparison).
					$select_build .= ( $product_id == $selected_product ) ? 'selected="selected"' : '';
					$select_build .= '>';
					$select_build .= $product_object->get_title();
					$select_build .= '</option>';
				}
			}
		}

		$select_build .= '</select>';

		$check_selected = $this->ft_gallery_create_woo_prod_exists_check( $selected_product );

		// Edit Selected Module Product Button.
		$select_build .= isset( $selected_product ) && ! empty( $selected_product ) && true == $check_selected ? sprintf(
			esc_html__( '%1$sEdit Model Product%2$s', 'feed-them-gallery-premium' ),
			'<div class="ft-gallery-edit-woo-model-prod ftg-hide-me"><a href="' . esc_url( get_edit_post_link( $selected_product ) ) . '" target="_blank">',
			'</a></div>'
		) : '';

		// If Edit Selected Module Product Button has been deleted.
		$select_build .= isset( $selected_product ) && ! empty( $selected_product ) && false == $check_selected ? esc_html__( 'This Model Product has been deleted or in the trash.', 'feed-them-gallery-premium' ) : '';

		return $select_build;
	}

	/**
	 * FT Gallery ZIP To WooCommerce Product select option
	 *
	 * Creates ZIP to WooCommerce product select option dynamically from WooCommerce Products.
	 *
	 * @param string $gallery_id The gallery ID.
	 * @param string $orientation The orientation to check for.
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_zip_to_woo_model_prod_select( $gallery_id, $orientation = 'zip' ) {

		$products = get_option( 'feed-them-gallery-model-product-list' );

		$select_build = '<select name="ft_gallery_zip_to_woo_model_prod" id="ft_gallery_zip_to_woo_model_prod" class="feed-them-social-admin-input" ' . ( isset( $products ) && is_array( $products ) ? '' : ' disabled' ) . '>';

		$display_gallery = new Display_Gallery();
		$option          = $display_gallery->ft_gallery_get_option_or_get_postmeta( $gallery_id );

		$selected_product = isset( $option['ft_gallery_zip_to_woo_model_prod'] ) ? $option['ft_gallery_zip_to_woo_model_prod'] : '';

		$select_build .= '<option ' . ( empty( $selected_product ) || ! empty( $selected_product ) && isset( $products[ $orientation ] ) && ! in_array( $selected_product, $products[ $orientation ] ) ? 'selected="selected "' : '' ) . ' value=""> ' . esc_attr( __( 'Select a Product', 'feed-them-gallery-premium' ) ) . '</option>';

		// Check Products array isset and has products under this orientation.
		if ( isset( $products[ $orientation ] ) ) {
			foreach ( $products[ $orientation ] as $product_id => $product_value ) {
				$product_object = wc_get_product( $product_id );
				if ( $product_object ) {
					// Check If product is virtual and Downloadable.
					if ( $product_object->is_virtual() && $product_object->is_downloadable() ) {
						$select_build .= '<option value="' . $product_id . '" ';
						// must use == as === will not work (strict comparison).
						$select_build .= ( $product_id == $selected_product ) ? 'selected="selected"' : '';
						$select_build .= '>';
						$select_build .= $product_object->get_title();
						$select_build .= '</option>';
					}
				}
			}
		}

		$select_build .= '</select>';

		$check_selected = $this->ft_gallery_create_woo_prod_exists_check( $selected_product );

		// Edit Selected Module Product Button.
		$select_build .= isset( $selected_product ) && ! empty( $selected_product ) && true == $check_selected ? sprintf(
			esc_html__( '%1$sEdit Model Product%2$s', 'feed-them-gallery-premium' ),
			'<div class="ft-gallery-edit-woo-model-prod ftg-hide-me"><a href="' . esc_url( get_edit_post_link( $selected_product ) ) . '" target="_blank">',
			'</a></div>'
		) : '';

		// If Edit Selected Module Product Button has been deleted.
		$select_build .= isset( $selected_product ) && ! empty( $selected_product ) && false == $check_selected ? esc_html__( 'This Model Product has been deleted or in the trash.', 'feed-them-gallery-premium' ) : '';

		return $select_build;
	}

	/**
	 * FT Gallery Image To WooCommerce Product
	 *
	 * Creates a WooCommerce product from a Feed Them Gallery Image attachment.
	 *
	 * @param null $post_id The post ID.
	 * @param null $images_array Array of images.
	 * @param null $ignore_echos Ignore echos.
	 * @since 1.0.0
	 */
	public function ft_gallery_image_to_woo_prod( $post_id = null, $images_array = null, $ignore_echos = null ) {
		$wc_adp = new \WC_Admin_Duplicate_Product();

		$my_request = stripslashes_deep( $_REQUEST );

		$gallery_id = empty( $post_id ) ? intval( sanitize_text_field( $my_request['GalleryID'] ) ) : $post_id;

		// Completion Report Variables.
		$completion_report_array = array();
		$final_completion_report = array();

		// Check if Media has been selected.
		if ( ! is_array( $images_array ) ) {
			$gallery_id = intval( sanitize_text_field( $my_request['GalleryID'] ) );
			// Check to see if this is only Selected Images.
			$selected_media = isset( $my_request['selectedMedia'] ) && ! empty( $my_request['selectedMedia'] ) ? json_decode( stripslashes( $my_request['selectedMedia'] ) ) : $this->ft_gallery_get_gallery_attached_media_ids( $gallery_id, '' );
		} else {
			$selected_media = $images_array;
		}

		$display_gallery = new Display_Gallery();
		$option          = $display_gallery->ft_gallery_get_option_or_get_postmeta( $gallery_id );

		// Create WooCommerce Category based on Gallery Title?
		if ( ftg_get_option( 'woo_attch_prod_to_gallery_cat' ) ) {
			$ft_gallery_woo_cat = $this->ft_gallery_insert_woo_term( $gallery_id );

			// Add Error message to Final Completion Report array if title is empty.
			if ( 'no_gallery_title' === $ft_gallery_woo_cat ) {
				$final_completion_report['woo_categories'] = esc_html__( 'A title for this Gallery must be set to attach WooCommerce image products to a category using the Gallery\'s name.', 'feed-them-gallery-premium' ) . '<br/><br/>';
			}
		}

		// Create Smart Image Orientation if needed.
		$use_smart_image_orientation = $option['ft_gallery_smart_image_orient_prod'];

		$selected_product_array = array();

		foreach ( $this->ft_gallery_smart_image_orientation_list() as $image_orientation ) {
			$orientation_name                                        = $this->ft_gallery_model_product_type_list( $image_orientation, 'field_name' );
			$selected_product_array[ $image_orientation ]['option']  = $option[ $orientation_name ];
			$selected_product_array[ $image_orientation ]['product'] = wc_get_product( $selected_product_array[ $image_orientation ]['option'] );
		}

		// See if we are using Selected Media array or function's $images_array array
		// $image_array = (is_array($selected_media) && !empty($selected_media)) ? $selected_media : $images_array;.
		$image_array = ! empty( $selected_media ) ? $selected_media : $images_array;

		// Media was Selected so make name have attachments count.
		if ( ! empty( $image_array ) ) {

			// Duplicate Woo Model Product and Update new product.
			// foreach ($image_array as $image_id) {.
			$image_id        = $image_array;
			$attachment_info = $this->ft_gallery_get_attachment_info( $image_id );

			$this_image_orientation = ( isset( $use_smart_image_orientation ) && 'true' === $use_smart_image_orientation ) ? $this->ft_gallery_smart_image_orientation( $attachment_info['src'] ) : 'global';

			$selected_product = isset( $selected_product_array[ $this_image_orientation ]['option'] ) ? $selected_product_array[ $this_image_orientation ]['option'] : '';
			$product          = isset( $selected_product_array[ $this_image_orientation ]['product'] ) ? $selected_product_array[ $this_image_orientation ]['product'] : '';

			// Check if wc_get_product returned an object.
			if ( ! empty( $selected_product ) && is_object( $product ) ) {

				// Get Product Type!
				$product_type = $product->get_type();

				// error_log( print_r( $product, true ) );
				// Only for variable products! Check Model Product has Variations with prices set.
				if ( 'variable' === $product_type ) {
					$variations = $product->get_available_variations();

					if ( empty( $variations ) ) {
						echo esc_html__( 'Are you sure you set prices for Variations for "Model Product" if not this wont work! Any Variations with without a price set will not be copied to newly created products', 'feed-them-gallery-premium' );
					}
				}

				$image_post_meta = get_post_meta( $image_id, 'ft_gallery_woo_prod', true );
				// If Image already has product meta check the product still exists.
				if ( ! empty( $image_post_meta ) ) {
					$product_exist = $this->ft_gallery_create_woo_prod_exists_check( $image_post_meta );
				}
				// If a product is not attached to this image yet.
				if ( empty( $image_post_meta ) || isset( $product_exist ) && false == $product_exist ) {
					$duplicate = $wc_adp->product_duplicate( $product );
					// Check if product_duplicate returned an object.
					// Hook rename to match other woocommerce_product_* hooks, and to move away from depending on a response from the wp_posts table.
					do_action( 'woocommerce_product_duplicate', $duplicate, $product );

					// REFERENCE: https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html
					// Get Product.
					$duplicate_object = wc_get_product( $duplicate );
					// Set Status.
					$duplicate_object->set_status( 'publish' );

					// Set Name and if for some reason the image name is blank use the slug of the image so the product gets created and does not fail.
					'' !== $attachment_info['title'] ? $duplicate_object->set_name( $attachment_info['title'] ) : $duplicate_object->set_name( $attachment_info['slug'] );

					// Set Title for permalink of product if it's available, otherwise set image slug
					'' !== $attachment_info['title'] ? $duplicate_object->set_slug( $attachment_info['title'] ) : $duplicate_object->set_slug( $attachment_info['slug'] );

					// Set Main Image ID.
					$duplicate_object->set_image_id( $image_id );
					// Set Woo Categories.
					if ( isset( $ft_gallery_woo_cat ) && null !== $ft_gallery_woo_cat ) {
						// Set Woo Categories from Array of product_cat ids.
						$duplicate_object->set_category_ids( array( $ft_gallery_woo_cat ) );
					}
					// Set Woo Description.
					if ( ftg_get_option( 'woo_product_short_description' ) ) {
						// Set to the Short Product Description area.
						$duplicate_object->set_short_description( $attachment_info['description'] );
					}
					else {
						// Set to the Main Description area if option chosen above that comes originates the Premium Settings page option.
						$duplicate_object->set_description( $attachment_info['description'] );
					}

					$duplicated_product_type = $duplicate_object->get_type();

					// Only for variable products.
					if ( isset( $duplicated_product_type ) && 'variable' === $duplicated_product_type ) {

						$variations = $duplicate_object->get_available_variations();

						foreach ( $variations as $variation_value ) {
							// Variable PRODUCT!
							$variation = wc_get_product( $variation_value['variation_id'] );

							// Set Variation Image!
							$variation->set_image_id( $image_id );

							$download_name = isset( $attachment_info['name'] ) ? $attachment_info['name'] : $attachment_info['file'];

							// Set Downloads array information!
							$variation->set_downloads(
								array(
									array(
										// Set Download File name.
										'name' => $download_name,
										// Set the Download Source to be the original name.
										'file' => $attachment_info['src'],
									),
								)
							);

							$variation->save();

							// error_log( print_r( $variation_value, true ) );
							// Clear/refresh the variation cache!
							wc_delete_product_transients( $variation_value['variation_id'] );
						}
					} else {
						// SIMPLE PRODUCT!
						// Check if selected product or "single model product" is downloadable.
						$selected_product_downloadable = $product->is_downloadable();
						// Downloadable if so set original image link as download link.
						if ( $selected_product_downloadable ) {
							// Set product to downloadable.
							$duplicate_object->set_downloadable( true );

							$download_name = isset( $attachment_info['name'] ) ? $attachment_info['name'] : $attachment_info['file'];

							// Set Downloads array information.
							$duplicate_object->set_downloads(
								array(
									array(
										// Set Download File name.
										'name' => $download_name,
										// Set the Download Source to be the original name.
										'file' => $attachment_info['src'],
									),
								)
							);
						}
					}

					// Save the info set above.
					$duplicate_object->save();
					update_post_meta( $image_id, 'ft_gallery_woo_prod', $duplicate_object->get_id() );

					// Completion report.
					if ( isset( $use_smart_image_orientation ) && 'true' === $use_smart_image_orientation ) {
						// commenting this out for now as it's throwing warnings in debug.log
						// $completion_report_array[ $this_image_orientation ]['created'] += 1;.
					} else {
						// commenting this out for now as it's throwing warnings in debug.log
						// $completion_report_array['global']['created'] += 1;.
					}
				} else {
					// Product existed already
					// Completion report.
					if ( isset( $use_smart_image_orientation ) && 'true' === $use_smart_image_orientation ) {
						// commenting this out for now as it's throwing warnings in debug.log
						// $completion_report_array[ $this_image_orientation ]['existed'] += 1;.
					} else {
						// commenting this out for now as it's throwing warnings in debug.log
						// $completion_report_array['global']['existed'] += 1;.
					}
				}
			} else {
				// No Product Object returned [Let users know what to do].
				if ( ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) && ( ! isset( $use_smart_image_orientation ) || 'false' === $use_smart_image_orientation ) ) {
					$final_completion_report['global'] = sprintf(
						esc_html__( 'No "%1$sGlobal Image Model Product%2$s" Found or selected. No products were created. Please select on the %3$sWooCommerce Tab.%4$s and try again.%5$s', 'feed-them-gallery-premium' ),
						'<strong>',
						'</strong>',
						'<a href="' . esc_url( 'post.php?post=' . $gallery_id . '&action=edit&tab=ft_woo_commerce' ) . '">',
						'</a>',
						'<br/><br/>'
					);
				} elseif ( ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) && isset( $use_smart_image_orientation ) && 'true' === $use_smart_image_orientation ) {
					$final_completion_report[ $this_image_orientation ] = sprintf(
						esc_html__( 'No "%1$s%2$s Image Model Product%3$s" Found or selected. Products were not created for this Smart Image Orientation type. Please select on the %4$sWooCommerce Tab%5$s and try again.%6$s', 'feed-them-gallery-premium' ),
						'<strong>',
						$this->ft_gallery_model_product_type_list( $this_image_orientation, 'name' ),
						'</strong>',
						'<a href="' . esc_url( 'post.php?post=' . $gallery_id . '&action=edit&tab=ft_woo_commerce' ) . '">',
						'</a>',
						'<br/><br/>'
					);
				}
			}
			// }
			// Start Completion Report.
			if ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) {

				// Completion report.
				if ( isset( $use_smart_image_orientation ) && 'true' === $use_smart_image_orientation ) {
					$final_completion_report['global'] = 'post.php?post=' . $duplicate_object->get_id() . '&action=edit';
				} else {
					// we pass the url to the newly created product so we can allow users to click the cart icon button right away and edit the product without having to save the page.
					$final_completion_report['global'] = 'post.php?post=' . $duplicate_object->get_id() . '&action=edit';
				}
			}

			// Print Completion Report.
			foreach ( $final_completion_report as $completed_output ) {
				echo esc_url( $completed_output );
			}
		} else {
			if ( empty( $ignore_echos ) || ! empty( $ignore_echos ) && 'true' !== $ignore_echos ) {
				esc_html__( 'No Images in this Gallery. Please upload images to use this feature', 'feed-them-gallery-premium' );
			}
		}
		exit;
	}

	/**
	 * FT Gallery ZIP To WooCommerce Product
	 *
	 * Creates a WooCommerce product from a Feed Them Gallery ZIP attachment.
	 *
	 * @param null $gallery_id The gallery ID.
	 * @param null $zip_id The ZIP ID.
	 * @since 1.0.0
	 */
	public function ft_gallery_zip_to_woo_prod( $gallery_id = null, $zip_id = null ) {
		$wc_adp = new \WC_Admin_Duplicate_Product();

		$my_request = stripslashes_deep( $_REQUEST );

		$gallery_id = isset( $gallery_id ) && ! empty( $gallery_id ) ? $gallery_id : intval( sanitize_text_field( $my_request['GalleryID'] ) );
		$zip_id     = isset( $zip_id ) && ! empty( $zip_id ) ? $zip_id : intval( sanitize_text_field( $my_request['ZIP_ID'] ) );

		$display_gallery = new Display_Gallery();
		$option          = $display_gallery->ft_gallery_get_option_or_get_postmeta( $gallery_id );

		$selected_product = $option['ft_gallery_zip_to_woo_model_prod'];

		$product = wc_get_product( $selected_product );

		// Check if wc_get_product returned an object.
		if ( ! empty( $selected_product ) && is_object( $product ) ) {

			// Duplicate Woo Model Product and Update new product.
			$zip_post_meta = get_post_meta( $zip_id, 'ft_gallery_zip_woo_prod', true );

			// If Image already has product meta check the product still exists.
			if ( ! empty( $zip_post_meta ) ) {
				$product_exist = $this->ft_gallery_create_woo_prod_exists_check( $zip_post_meta );
			}

			// If a product is not attached to this image yet.
			if ( empty( $zip_post_meta ) || isset( $product_exist ) && false == $product_exist ) {

				$duplicate = $wc_adp->product_duplicate( $product );

				// Check if product_duplicate returned an object.
				// Hook rename to match other woocommerce_product_* hooks, and to move away from depending on a response from the wp_posts table.
				do_action( 'woocommerce_product_duplicate', $duplicate, $product );

				$attachment_info = $this->ft_gallery_get_attachment_info( $zip_id );

				// REFERENCE: https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html
				// Get Product.
				$duplicate_object = wc_get_product( $duplicate );
				// Set Status.
				$duplicate_object->set_status( 'publish' );
				// Set Name.
				$duplicate_object->set_name( $attachment_info['title'] );
				// Set Slug.
				$duplicate_object->set_slug( $attachment_info['slug'] );
				// Set Main Image ID.
				$duplicate_object->set_image_id( $zip_id );
				// Set Description.
				$duplicate_object->set_description( $attachment_info['description'] );

				// print_r($attachment_info);
				// Set Download file source to ZIP source
				// we pass the full array so woo can process the name, and the file url, do all it's checks etc.
				// if the fle type zip is not added in the theme or somewhere else that may be a problem.
				$duplicate_object->set_downloads( array( $attachment_info ) );

				// Save the info set above.
				$duplicate_object->save();

				update_post_meta( $zip_id, 'ft_gallery_zip_woo_prod', $duplicate_object->get_id() );
				echo sprintf(
					esc_html__( 'ZIP Product created. You can see it on the %1$sWoocommerce Products Page%2$s.', 'feed-them-gallery-premium' ),
					'<a href="' . esc_url( 'edit.php?post_status=publish&post_type=product&orderby=date&order=desc' ) . '" target="_blank">',
					'</a>'
				);
			} else {
				// returning this to our jquery ajax to do stuff.
				echo sprintf(
					esc_html__( 'This ZIP Product has already been created. You can see it on the %1$sWoocommerce Products Page%2$s.', 'feed-them-gallery-premium' ),
					'<a href="' . esc_url( 'edit.php?post_status=publish&post_type=product&orderby=date&order=desc' ) . '" target="_blank">',
					'</a>'
				);
			}

			// echo sprintf(__('ZIP Product created you can see it on the "%1$sWoocommerce Products Page%2$s', 'feed-them-gallery-premium'),
			// '<a href="' . esc_url('edit.php?post_status=publish&post_type=product&orderby=date&order=desc') . '" target="_blank">',
			// '</a>'
			// );.
		} //No Product Object returned [Let users know what to do]
		// else {
		// echo sprintf(__('No Model Product Found or selected. Please select a %1$sZIP Model Product%2$s on the on the %3$sWooCommerce Tab.%4$s', 'feed-them-gallery-premium'),
		// '<strong>',
		// '</strong>',
		// '<a href="#woocommerce" class="ftg-woo-tab" target="_blank">',
		// '</a>'
		// );
		// }.
		exit;
	}

	/**
	 * FT Gallery Create Woo Prod Exists Check
	 *
	 * Check if WooCommerce Product exists and is not in the "trash".
	 *
	 * @param string $id_to_check The ID to check against.
	 * @return bool
	 * @since 1.0.0
	 */
	public function ft_gallery_create_woo_prod_exists_check( $id_to_check ) {
		$ft_gallery_woo_product_status = get_post_status( $id_to_check );

		// Check the Status if False or in Trash return false.
		return false == $ft_gallery_woo_product_status || 'trash' === $ft_gallery_woo_product_status ? false : true;
	}

	/**
	 * FT Gallery Add Woo Metabox
	 *
	 * Adds a meta box to the WooCommerce editing screen
	 *
	 * @since 1.0.2
	 */
	public function ft_gallery_add_woo_metabox() {
		add_meta_box( 'ft_gallery_woo_metabox', esc_html__( 'Feed Them Gallery', 'feed-them-gallery-premium' ), array( $this, 'ft_gallery_woo_metabox' ), 'product', 'side', 'high' );
	}

	/**
	 * FT Gallery Woo Metabox
	 *
	 * Outputs the content of the meta box.
	 *
	 * @param string $post The post ID.
	 * @since 1.0.2
	 */
	public function ft_gallery_woo_metabox( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'ft_gallery_woo_metabox_nonce' );
		$model_product_array = get_option( 'feed-them-gallery-model-product-list' );
		?>
		<p>
		<small><?php esc_html_e( 'Check the Model Product type(s) if you want this product to show up in the Model Product select options area in the WooCommece tab of a Feed Them Gallery.', 'feed-them-gallery-premium' ); ?></small>

		<?php foreach ( $this->ft_gallery_model_product_type_list() as $model_product ) { ?>
			<div>
				<label for="feed-them-gallery-model-product-<?php echo esc_attr( $model_product['type'] ); ?>">
					<input type="checkbox" name="feed-them-gallery-model-product-<?php echo esc_attr( $model_product['type'] ); ?>" id="feed-them-gallery-model-product-<?php echo esc_attr( $model_product['type'] ); ?>" value=""
																							<?php
																							if ( isset( $model_product_array[ $model_product['type'] ][ $post->ID ] ) ) {
																								checked( $model_product_array[ $model_product['type'] ][ $post->ID ], 'true' );}
																							?>
					 />
					<span><?php echo esc_html( $model_product['name'] ); ?> <?php esc_html_e( 'Model Product', 'feed-them-gallery-premium' ); ?></span>
				</label><br />
				<?php
				// Special Note?
				if ( isset( $model_product['special_note'] ) ) {
					echo '<br/><small><em>' . wp_kses(
					    $model_product['special_note'],
					    array(
					        'a'      => array(
					            'href'  => array(),
					            'title' => array(),
					        ),
					        'br'     => array(),
					        'em'     => array(),
					        'strong' => array(),
					        'small'  => array(),
					    )
					)  . '</em></small>';
				}
				?>
			</div>
		<?php } ?>
		</p>

		<?php
	}

	/**
	 * FT Gallery Woo Metabox Save
	 *
	 * Saves the custom meta input
	 *
	 * @param string $post_id The post ID.
	 * @since 1.0.2
	 */
	public function ft_gallery_woo_metabox_save( $post_id ) {
		$model_product_array = get_option( 'feed-them-gallery-model-product-list' );

		$my_post = stripslashes_deep( $_POST );

		// Make sure Model Product Option Array is set if no set it!
		$model_product_array = isset( $model_product_array ) && ! empty( $model_product_array ) ? $model_product_array : array();

		// Checks save status - overcome autosave, etc.
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $my_post['ft_gallery_woo_metabox_nonce'] ) && wp_verify_nonce( $my_post['ft_gallery_woo_metabox_nonce'], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		foreach ( $this->ft_gallery_model_product_type_list() as $model_product ) {
			$model_product_type     = $model_product['type'];
			$model_product_variable = 'feed-them-gallery-model-product-' . $model_product_type;

			// Checks for input and saves - save checked as yes and unchecked at no.
			if ( isset( $_POST[ $model_product_variable ] ) ) {
				$model_product_array[ $model_product_type ][ $post_id ] = 'true';
			} else {
				if ( isset( $model_product_array[ $model_product_type ][ $post_id ] ) ) {
					unset( $model_product_array[ $model_product_type ][ $post_id ] );
				}
			}
		}

		update_option( 'feed-them-gallery-model-product-list', $model_product_array );
	}
}
