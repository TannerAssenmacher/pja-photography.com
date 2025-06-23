<?php
/**
 * ZIP Gallery Class
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * @version  1.0.0
 * @package  FeedThemSocial/ZIP
 * @author   SlickRemix
 */

namespace feed_them_gallery;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zip_Gallery
 *
 * @package FeedThemSocial/ZIP
 */
class Zip_Gallery extends Gallery {
	/**
	 * Zip_Gallery constructor.
	 */
	public function __construct() {
		// Add Ajax for CREATING ZIP Gallery.
		add_action( 'wp_ajax_ft_gallery_create_zip_ajax', array( $this, 'ft_gallery_create_zip_ajax' ) );
		add_action( 'wp_ajax_nopriv_ft_gallery_create_zip_ajax', array( $this, 'ft_gallery_create_zip_ajax' ) );

		// Add Download File Actions.
		add_action( 'init', array( $this, 'ft_gallery_download_file' ) );
		add_action( 'admin_init', array( $this, 'ft_gallery_download_file' ) );

		// Add Ajax for VIEW ZIP Gallery.
		add_action( 'wp_ajax_ft_gallery_view_zip_ajax', array( $this, 'ft_gallery_view_zip_ajax' ) );
		add_action( 'wp_ajax_nopriv_ft_gallery_view_zip_ajax', array( $this, 'ft_gallery_view_zip_ajax' ) );

		// Add Ajax for DELETING  ZIP Gallery.
		add_action( 'wp_ajax_ft_gallery_delete_zip_ajax', array( $this, 'ft_gallery_delete_zip_ajax' ) );
		add_action( 'wp_ajax_nopriv_ft_gallery_delete_zip_ajax', array( $this, 'ft_gallery_delete_zip_ajax' ) );

		// ADD Ajax to Admin and Frontend
		// removing from front end until we get that far
		// add_action('wp_enqueue_scripts', array($this, 'ft_gallery_zip'));.
		add_action( 'admin_enqueue_scripts', array( $this, 'ft_gallery_zip' ) );

		/*
		//Add zip download columns
		add_filter('manage_ft_gallery_posts_columns', array($this, 'ft_gallery_zip_downloads_count_columns'));
		//Add Value to Zip Column
		add_action('manage_ft_gallery_posts_custom_column', array($this, 'ft_gallery_columns_values'), 10, 2);
		//Sort Column by Zip Downloads
		add_filter('request', array($this, 'ft_gallery_column_sort_orderby'));
		*/

		add_shortcode( 'ft_gallery_download_button', array( $this, 'ft_gallery_show_download_button' ) );
	}

	/**
	 * FT Gallery ZIP
	 *
	 * Enqueue Ajax to Admin and Frontend
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_zip() {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'ft_gallery_zip', plugins_url( 'feed-them-gallery/includes/js/zip.js' ), array(), FTGP_CURRENT_VERSION, false );

		wp_localize_script( 'ft_gallery_zip', 'ftgalleryAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * FT Gallery Show Download Button
	 *
	 * Shortcode for showing Download button
	 *
	 * @param string $atts All the attributes.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_show_download_button( $atts ) {

		// Parameters accepted.
		extract(
			shortcode_atts(
				array(
					'id'             => '',
					'btn_type'       => '',
					'text'           => 'Download Attachments',
					'counter'        => false,
					'counter_format' => '(%)',
				),
				$atts
			)
		);

		return $this->ft_gallery_download_button( $id, $btn_type, $text, $counter, $counter_format );
	}

	/**
	 * FT Gallery Download Button
	 *
	 * Generate and return gallery Zip button
	 *
	 * @param string $post_ID The post ID.
	 * @param string $btn_type The button type.
	 * @param string $text The text to be shown.
	 * @param bool   $counter The download counter.
	 * @param string $counter_format The counter format.
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_download_button( $post_ID, $btn_type = 'text', $text = 'Download Gallery', $counter = false, $counter_format = '(%)' ) {

		if ( 'ft_gallery' === get_post_type( $post_ID ) ) {

			/*
			 $ft_gallery_button_counter = ' ';

			 if ($counter === "true") {

				 // Build the counter format
				 $ft_gallery_button_counter .= $counter_format;

				 $ft_gallery_download_counter = get_post_meta($post_ID, '_ft_gallery_downloads_counter', true) != '' ? get_post_meta($post_ID, '_ft_gallery_downloads_counter', true) : '0';

				 $ft_gallery_button_counter = str_replace('%', $ft_gallery_download_counter, $ft_gallery_button_counter);
			 }
			*/

			switch ( $btn_type ) {
				case 'icon':
					$button = '<button type="button" class="ft_gallery_download_button" onclick="ft_gallery_create_zip(\'' . $post_ID . '\',\'yes\',\'no\',\'yes\')">' . sanitize_text_field( $text ) . '</button>';
					break;

				case 'text':
				default:
					$button = '<button type="button" class="ft_gallery_download_button" onclick="ft_gallery_create_zip(\'' . $post_ID . '\',\'yes\',\'no\',\'yes\')">' . sanitize_text_field( $text ) . '</button>';
					break;
			}

			return $button;
		} else {
			return 'This is not an Feed Them Gallery';
		}
	}

	/**
	 * FT Gallery Create ZIP Ajax
	 *
	 * Generate Gallery Zip in upload directory (ft-gallery folder)
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_create_zip_ajax() {

		$my_post = stripslashes_deep( $_POST );

		$post_id = intval( sanitize_text_field( $my_post['postId'] ) );

		// Create Woocommerce Product?
		$create_woo_prod = isset( $my_post['CreateWooProd'] ) ? $my_post['CreateWooProd'] : '';

		// Download the Newest Zip Available.
		$download_newest_zip = isset( $my_post['DownloadNewestZIP'] ) ? $my_post['DownloadNewestZIP'] : '';

		// Newest ZIP Available?
		$newest_zip = get_post_meta( $post_id, 'ft_gallery_newest_zip_id' );

		if ( 'yes' === $download_newest_zip ) {
			$newest_zip_check = $this->ft_gallery_zip_exists_check( $newest_zip );
		}

		// If Download Newest ZIP is set then Skip this as long as newest ZIP still exists.
		if ( 'yes' !== $download_newest_zip || 'yes' === $download_newest_zip && ( ! isset( $newest_zip_check ) || isset( $newest_zip_check ) && 'false' === $newest_zip_check ) ) {

			$pretty_filename = sanitize_file_name( get_the_title( $post_id ) );

			$args = array(
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'post_parent'    => $post_id,
			);

			$attachments = get_posts( $args );
			// Check for attachements!
			if ( $attachments ) {

				$ft_gallery_rel_upload_folder = $this->ft_gallery_get_rel_zip_folder( $post_id );
				$ft_gallery_abs_upload_folder = $this->ft_gallery_get_abs_zip_folder( $post_id );

				// If Directory doesn't exist create it.
				if ( ! file_exists( $ft_gallery_rel_upload_folder ) ) {
					wp_mkdir_p( $ft_gallery_rel_upload_folder );
				}
				// Check to see if this is only Selected Images.
				$gallery_dirname = ! empty( $pretty_filename ) ? $pretty_filename : $post_id;

				$final_file_zipname = $gallery_dirname . '-all.zip';

				$zip_path = $ft_gallery_rel_upload_folder . $final_file_zipname;

				$zip = new \ZipArchive();
				$zip->open( $zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );

				// Loop through the attachments and add them to the file.
				foreach ( $attachments as $attachment ) {
					if ( 'application/zip' !== $attachment->post_mime_type ) {
						// Get the file name.
						$name = explode( '/', get_attached_file( $attachment->ID ) );
						$name = $name[ count( $name ) - 1 ];
						$zip->addFile( get_attached_file( $attachment->ID ), $name );
					}
				}

				// Store the filename before closing the file.
				$filename_array = explode( '/', $zip->filename );
				$filename       = $filename_array[ count( $filename_array ) - 1 ];

				// Close the file.
				$zip->close();

				$zip_final_abs_path = $ft_gallery_abs_upload_folder . $final_file_zipname;

				// Insert uploaded file as attachment.
				$zip_attach_id = wp_insert_attachment(
					array(
						'post_mime_type' => 'application/zip',
						'post_title'     => $final_file_zipname,
						'post_content'   => '',
						'post_status'    => 'inherit',
					),
					$zip_final_abs_path,
					$post_id
				);

				// Include the image handler library.
				require_once ABSPATH . 'wp-admin/includes/image.php';

				// Generate meta data and update attachment.
				$zip_attach_data = wp_generate_attachment_metadata( $zip_attach_id, $zip_final_abs_path );

				wp_update_attachment_metadata( $zip_attach_id, $zip_attach_data );

				update_post_meta( $post_id, 'ft_gallery_newest_zip_id', $zip_attach_id );

				$ft_gallery_get_attachment_info = $this->ft_gallery_get_attachment_info( $zip_attach_id );

				if ( isset( $_REQUEST['ActivateDownload'] ) && 'yes' === $_REQUEST['ActivateDownload'] || 'yes' === $download_newest_zip ) {
					// We have to return an actual URL, that URL will set the headers to force the download.
					echo esc_url_raw( $ft_gallery_get_attachment_info['download_url'] );
				} else {
					echo 'ZIP Created.';
				}

				if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && 'yes' === $create_woo_prod ) {
					$gallery_class = new Gallery_to_Woocommerce();
					$gallery_class->ft_gallery_zip_to_woo_prod( $post_id, $zip_attach_id );
				}

				die();
			} else {
				echo 'No Attachments found';
			}
		}
		// If Download Newest ZIP is set then just download.
		elseif ( $download_newest_zip && isset( $newest_zip_check ) && 'true' === $newest_zip_check ) {
			$ft_gallery_get_attachment_info = $this->ft_gallery_get_attachment_info( $newest_zip );

			echo esc_url_raw( $ft_gallery_get_attachment_info['download_url'] );
			// echo 'this is a test';.
			die();
		}
	}

	/**
	 * FT Gallery Zip Downloads Count Columns
	 *
	 * Create Zip Downloads Column
	 *
	 * @param string $columns Show download option in the column.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_zip_downloads_count_columns( $columns ) {
		// $columns['ft_gallery_downloads_counter'] = 'Downloads';
		return $columns;
	}

	/**
	 * FT Gallery Columns Values
	 *
	 * Add Value to downloads Zip Columns
	 *
	 * @param string $column Show download option in the column.
	 * @param int    $post_id The post ID.
	 * @since 1.0.0
	 */
	public function ft_gallery_columns_values( $column, $post_id ) {
		switch ( $column ) {
			case 'ft_gallery_downloads_counter':
				// echo get_post_meta($post_id, '_ft_gallery_downloads_counter', true) != '' ? get_post_meta($post_id, '_ft_gallery_downloads_counter', true) : '0';.
				break;
		}
	}

	/**
	 * FT Gallery Column Sort Order by
	 *
	 * Download zip column sort (Tell WordPress our fields are numeric)
	 *
	 * @param string $vars The order by option.
	 * @return array
	 * @since 1.0.0
	 */
	public function ft_gallery_column_sort_orderby( $vars ) {

		if ( isset( $vars['orderby'] ) && '_ft_gallery_downloads_counter' === $vars['orderby'] ) {
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => '_ft_gallery_downloads_counter',
					'orderby'  => 'meta_value_num',
				)
			);
		}
		return $vars;
	}


	/**
	 * FT Gallery Get Rel ZIP Folder
	 *
	 * Get RELATIVE ZIP folder directory
	 *
	 * @param int    $post_id The post ID.
	 * @param string $pretty_filename The filename.
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_get_rel_zip_folder( $post_id, $pretty_filename = '' ) {

		$pretty_filename = sanitize_file_name( get_the_title( $post_id ) );

		// Prepare File.
		$upload_dir = wp_upload_dir();
		// Name for File either Gallery Title or Gallery ID.
		$gallery_dirname = ! empty( $pretty_filename ) ? $pretty_filename : $post_id;
		// FT Gallery Uploads Folder (wp-content/uploads/ft-gallery/).
		return $upload_dir['basedir'] . '/ft-gallery/' . $gallery_dirname . '/';
	}

	/**
	 * FT Gallery Get Absolute ZIP Folder Director
	 *
	 * Get ABSOLUTE ZIP folder directory
	 *
	 * @param string $post_id The post ID.
	 * @param string $pretty_filename The filename.
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_get_abs_zip_folder( $post_id, $pretty_filename = '' ) {

		$pretty_filename = sanitize_file_name( get_the_title( $post_id ) );

		// Prepare File.
		$upload_dir = wp_upload_dir();
		// Name for File either Gallery Title or Gallery ID.
		$gallery_dirname = ! empty( $pretty_filename ) ? $pretty_filename : $post_id;
		// FT Gallery Uploads Folder (wp-content/uploads/ft-gallery/).
		return $upload_dir['baseurl'] . '/ft-gallery/' . $gallery_dirname . '/';
	}

	/**
	 * FT Gallery Return ZIP Attachments
	 *
	 * Return Zip Attachments
	 *
	 * @param string $post_id The post ID.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_return_zip_attachments( $post_id ) {
		// Get ZIPs attached to this post.
		$args = array(
			'post_mime_type' => 'application/zip',
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_parent'    => $post_id,
		);

		$attachments = get_posts( $args );

		return $attachments;
	}


	/**
	 * FT Gallery List ZIP Files
	 *
	 * Display List of ZIP is this Galleries uploads folder
	 *
	 * @param string $post_id The post ID.
	 * @return string
	 * @since 1.0.0
	 */
	public function ft_gallery_list_zip_files( $post_id ) {

		$zips = $this->ft_gallery_return_zip_attachments( $post_id );

		if ( ! empty( $zips ) ) {
			// list the contents.
			$zip_list = '<ul id="ft-gallery-zip-list">';
			foreach ( $zips as $zip ) {
				$abs_file_url = wp_get_attachment_url( $zip->ID );
				$zip_name     = pathinfo( $abs_file_url, PATHINFO_FILENAME );

				$ftg_custom_timezone = get_option( 'timezone_string' ) ? get_option( 'timezone_string' ) : 'America/Los_Angeles';
				date_default_timezone_set( $ftg_custom_timezone );

				$zip_list .= '<li class="ft-gallery-zip zip-list-item-' . $zip->ID . '">';

				$ft_gallery_get_attachment_info = $this->ft_gallery_get_attachment_info( $zip->ID );

				// Download Link.
				$zip_list .= '<div class="ft-gallery-file-name"><a href="' . $ft_gallery_get_attachment_info['download_url'] . '" title="Download">' . $zip_name . '</a></div>';
				// Creation Date/Time.
				$zip_list .= '<div class="ft-gallery-file-time">' . date_i18n( 'F j, Y - g:ia', strtotime( $zip->post_date ) ) . '</div>';

				// Delete ZIP.
				$zip_list .= '<div class="ft-gallery-file-delete"><a class="ft_gallery_delete_zip_button" onclick="ft_gallery_delete_zip(\'' . $zip->ID . '\',\'' . $zip_name . '\')">Delete</a></div>';
				// Zip to WooCommerce.
				if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

					$gallery_to_woo = new Gallery_to_Woocommerce();

					// Duplicate Woo Model Product and Update new product.
					$image_post_meta = get_post_meta( $zip->ID, 'ft_gallery_woo_prod', true );

					// If Image already has product meta check the product still exists.
					if ( ! empty( $image_post_meta ) ) {
						$product_exist = $gallery_to_woo->ft_gallery_create_woo_prod_exists_check( $image_post_meta );
						if ( $product_exist ) {
							$zip_list .= '<div class="ft-gallery-file-delete ft-gallery-file-zip-to-woo"><a class="ft_gallery_create_woo_prod_button" target="_blank" href="' . get_edit_post_link( $image_post_meta ) . '" ">Edit product</a></div>';
						} else {
							continue;
						}
					} else {
						$zip_list .= '<div class="ft-gallery-file-delete ft-gallery-file-zip-to-woo"><a class="ft_gallery_create_woo_prod_button" onclick="ft_gallery_zip_to_woo(\'' . $post_id . '\',\'' . $zip->ID . '\')">Create product</a></div>';
					}
				}
				// View ZIP.
				$zip_list .= '<div class="ft-gallery-file-view"><a class="ft_gallery_view_zip_button" onclick="ft_gallery_view_zip_contents(\'' . $post_id . '\',\'' . $zip->ID . '\',\'' . $zip_name . '\')">View Contents</a></div>';

				$zip_list .= '<ol class="zipcontents_list"></ol>';

				$zip_list .= '</li>';

			}
			$zip_list .= '</ul>';

			return $zip_list;
		}

		return '<div class="ft-gallery-no-zips">' . sprintf(
			esc_html__( 'You have not created any ZIPs yet. You can do so from the %1$s Images tab%2$s. Please reload this page if you have already created a ZIP from the Images tab.', 'feed-them-gallery-premium' ),
			'<a href="#images" class="ftg-images-tab">',
			'</a>'
		) . '</div>';
	}

	/**
	 * FT Gallery Download File
	 *
	 * Download ZIP File
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_download_file() {

		$my_get = stripslashes_deep( $_GET );

		if ( isset( $my_get['attachment_name'], $my_get['download_file'] ) && '1' === sanitize_text_field( $my_get['download_file'] ) ) {
			$this->ft_gallery_send_file();
		}
	}

	/**
	 * FT Gallery Send File
	 *
	 * Send Zip File and Force Download
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_send_file() {

		$my_get = stripslashes_deep( $_GET );

		$zip_id = sanitize_text_field( $my_get['attachment_name'] );

		$attachment_info = $this->ft_gallery_get_attachment_info( $zip_id );

		$zip_name = pathinfo( $attachment_info['src'], PATHINFO_BASENAME );

		if ( ! $zip_name ) {
			return;
		}

		$content_type = '';
		// check filetype.
		switch ( $attachment_info['mime-type'] ) {
			case 'image/png':
				$content_type = 'image/png';
				break;
			case 'image/gif':
				$content_type = 'image/gif';
				break;
			case 'image/tiff':
				$content_type = 'image/tiff';
				break;
			case 'image/jpg':
				$content_type = 'image/jpg';
				break;
			case 'application/zip':
				$content_type = 'application/zip';
				break;
			default:
				$content_type = 'application/force-download';
		}

		header( 'Expires: 0' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Cache-Control: pre-check=0, post-check=0, max-age=0', false );
		header( 'Pragma: no-cache' );
		header( "Content-type: $content_type" );
		header( "Content-Disposition:attachment; filename=$zip_name" );
		header( 'Content-Type: application/force-download' );

		ob_end_clean();

		readfile( $attachment_info['src'] );
		exit();
	}

	/**
	 * FT Gallery View ZIP Ajax
	 *
	 * View ZIP ajax
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_view_zip_ajax() {

		$my_post = stripslashes_deep( $_POST );

		$post_id = intval( sanitize_text_field( $my_post['postId'] ) );
		$zip_id  = intval( sanitize_text_field( $my_post['ZIP_ID'] ) );

		$abs_file_url = wp_get_attachment_url( $zip_id );
		$zip_name     = pathinfo( $abs_file_url, PATHINFO_FILENAME );

		$zip_filename = $zip_name . '.zip';

		if ( ! $zip_filename ) {
			return 'No vaild Filename found';
		}
		// clean the fileurl.
		$file_url = stripslashes( trim( $zip_filename ) );

		$ft_gallery_upload_folder = $this->ft_gallery_get_rel_zip_folder( $post_id );

		if ( true == strpos( $file_url, '.php' ) ) {
			die( 'Invalid file!' );
		}

		$zip_contents = '';
		$zip          = zip_open( $ft_gallery_upload_folder . $zip_filename );

		if ( is_resource( $zip ) ) {

			while ( $zip_entry = zip_read( $zip ) ) {
				if ( zip_entry_open( $zip, $zip_entry ) ) {
					$filename      = zip_entry_name( $zip_entry );
					$zip_contents .= '<li>' . $filename . '</li>';
					zip_entry_close( $zip_entry );
				}
			}

			zip_close( $zip );

			ob_end_clean();

			echo $zip_contents;

			wp_die();
		} else {
			return 'This ZIP is empty';
		}
	}

	/**
	 * Generate Gallery Zip in upload directory (ft-gallery folder)
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_delete_zip_ajax() {

		$my_request = stripslashes_deep( $_REQUEST );

		$zip_id = intval( sanitize_text_field( $my_request['ZIP_ID'] ) );

		if ( ! $zip_id ) {
			return;
		}

		// ZIP id and Force Delete.
		wp_delete_attachment( $zip_id, true );

		exit();
	}

}//end class
