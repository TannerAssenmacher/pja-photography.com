<?php
/**
 * FT Gallery Watermarking Ajax Class
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * @version  1.0.0
 * @package  FeedThemSocial/Watermarking
 * @author  Tim Carr
 */

namespace feed_them_gallery;

/**
 * Class FTGallery_Watermarking_Ajax
 *
 * @package feed_them_gallery
 */
class FTGallery_Watermarking_Ajax {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Path to the file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the base class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $base;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'ft_gallery_ajax_load_image', array( $this, 'add_watermark' ), 10, 2 );

	}

	/**
	 * Add Watermark
	 * Overlay a watermark, if specified, to the uploaded image
	 *
	 * @param int $attachment_id Attachment ID.
	 * @param int $gallery_id Gallery ID.
	 * @param int $img_file The image file name.
	 * @param int $image_size The size of the image.
	 * @param int $allowed_sizes The allowed sizes.
	 *
	 * @since 1.0.0
	 */
	public function add_watermark( $attachment_id, $gallery_id, $img_file, $image_size, $allowed_sizes ) {

		$display_gallery = new Display_Gallery();
		$settings        = $display_gallery->ft_gallery_get_option_or_get_postmeta( $gallery_id );

		// $display_gallery = new Display_Gallery();
		// $gallery_settings = $display_gallery->ft_gallery_get_image_sizes($attachment_id);
		// $meta = get_post_meta($gallery_id, '_wp_attachment_metadata', true);
		$final_image_size_check = $image_size;

		// error_log('Final Image Size = '.$final_image_size_check);
		// error_log('attachment id: '.$attachment_id);
		// error_log('attachment id: '.$attachment_id);
		// error_log('gallery/post id: '.$gallery_id);
		// $attachment_id = $attachment_id;
		// $gallery_id = $gallery_id;
		// Get instance
		// $instance = FTGallery_Gallery_Shortcode::get_instance_ftgallery_watermarking_ajax();
		// Get gallery
		// $data = get_post_meta( $gallery_id, '_eg_gallery_data', true );
		// try this instead
		// $meta = wp_get_attachment_metadata( $attachment_id );
		// $meta =  get_post_meta($gallery_id, '_wp_attachment_metadata', true);
		// $filepath = get_attached_file($attachment_id);
		// $filebasename = wp_basename($meta['file']);
		// $get_site_url = get_site_url();
		// error_log(print_r($meta,true) .' meta infooooooooooo  File Path: ' . $filepath. ' File Basename: '.$filebasename);
		// error_log('Site URL: '.$get_site_url);
		// $allowed_sizes = $settings['ft_watermark_image_thumb'];
		// Check if watermarking is enabled.
		if ( 'imprint' !== $settings['ft_gallery_watermark'] ) {
			return;
		}
		// error_log('made it past ft_gallery_watermark is yes check ');.
		$watermarking_image_id = $settings['ft_watermark_image_id'];
		if ( empty( $watermarking_image_id ) ) {
			return;
		}

		// error_log('made it past ft_watermark_image_id check and our image size is = ' .$image_size);
		// I need to compare the get post meta info and see if the $final_image_size_check is there, and if not to proceed.
		$watermark_size_check = get_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', true );

		// in_array($already_watermarked, $allowed_sizes);.
		$already_watermarked = explode( ',', $watermark_size_check );

		// error_log(print_r($already_watermarked, true) .'exploded get post meta db info');
		// Check if this attachment has already been watermarked
		// If so, skip it.
		if ( in_array( $final_image_size_check, $already_watermarked ) ) {
			// error_log('RETURN ALREADY MARKED - get_post_meta response should be marked = '.print_r($already_watermarked, true));.
			return;
		}
		// error_log('WATERMARK NOT APPLIED SO WE CONTINUE _ft_gallery_watermarking_applied check ');
		// Watermark image using GD or Imagick.
		if ( $this->has_gd_extension() ) {
			// if(!$this->watermark_single_image($img_file, $img['mime-type'])).
			$this->watermark_gd( $attachment_id, $gallery_id, $img_file, $final_image_size_check );

		} elseif ( $this->has_imagick_extension() ) {
			$this->watermark_imagick( $attachment_id, $gallery_id, $img_file, $final_image_size_check );
		} //else {
		// return;
		// }
		// error_log('checking for imagick or GD extension');.
	}


	/**
	 * Has GD Extension
	 * Flag to determine if the GD library has been compiled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if has proper extension, false otherwise.
	 */
	public function has_gd_extension() {

		// error_log('checking for GD extension');.
		return extension_loaded( 'gd' ) && function_exists( 'gd_info' );

	}

	/**
	 * Has Imagick Extension
	 *
	 * Flag to determine if the Imagick library has been compiled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if has proper extension, false otherwise.
	 */
	public function has_imagick_extension() {

		// error_log('checking for imagick extension');.
		return extension_loaded( 'imagick' );

	}

	/**
	 * Watermark GD
	 * Watermark image using GD
	 *
	 * @param int $attachment_id Attachment ID.
	 * @param int $gallery_id Gallery ID.
	 * @param int $filepath The path of the image.
	 * @param int $final_image_size_check The final size check of the image.
	 * @since 1.0
	 */
	public function watermark_gd( $attachment_id, $gallery_id, $filepath, $final_image_size_check ) {

		// Get instance and Test in error log to make sure the output is proper
		// error_log('watermark_gd galler/post id: '.$gallery_id);
		// error_log('watermark_gd attachement id: '.$attachment_id);
		// error_log('watermark meta updated');.
		$display_gallery = new Display_Gallery();
		$settings        = $display_gallery->ft_gallery_get_option_or_get_postmeta( $gallery_id );

		// Get image and watermark image
		// $image_path = '/home/sidebarsupport/public_html/wp-content/uploads/2017/05/latest-NEW.jpg';.
		$image_path = $filepath;

		// error_log('Image Path New: '.$image_path);
		// $image_path = get_attached_file( $attachment_id );
		// error_log('Image Path New2: '.$image_path);
		// error_log('Image Path Old: '.$image_path);.
		$watermark_path = get_attached_file( $settings['ft_watermark_image_id'] );

		// error_log('Our Watermark Image ID: '.$settings['ft_watermark_image_id']. ' Watermark Path: ' .$watermark_path);
		// Get images.
		$image     = @imagecreatefromstring( file_get_contents( $image_path ) );
		$watermark = @imagecreatefromstring( file_get_contents( $watermark_path ) );

		// error_log('Get Image file get contents check: ' . $image);
		// Get widths and heights for the image and watermark.
		$image_width      = imagesx( $image );
		$image_height     = imagesy( $image );
		$watermark_width  = imagesx( $watermark );
		$watermark_height = imagesy( $watermark );

		// error_log('Image width: '.$image_width.'px');
		// Get metadata (MIME type) for the image and watermark.
		$image_meta     = getimagesize( $image_path );
		$watermark_meta = getimagesize( $watermark_path );
		// error_log('Image Meta:' .print_r($image_meta, true));
		// if our image width is greater than 150
		// error_log('Made it past image size check');
		// If the watermark exceeds the width or height of the image, scale the watermark down.
		$scale_factor = 0.3;
		if ( $watermark_width > $image_width || $watermark_height > $image_height ) {
			// Calculate new watermark size.
			$new_watermark_width  = $watermark_width * $scale_factor;
			$new_watermark_height = $watermark_height * $scale_factor;

			// Create resized watermark image.
			$watermark = imagecreatetruecolor( $new_watermark_width, $new_watermark_height );
			imagecolortransparent( $watermark, imagecolorallocatealpha( $watermark, 0, 0, 0, 127 ) );
			imagealphablending( $watermark, false );
			imagesavealpha( $watermark, true );
			imagecopyresampled( $watermark, @imagecreatefromstring( file_get_contents( $watermark_path ) ), 0, 0, 0, 0, $new_watermark_width, $new_watermark_height, $watermark_width, $watermark_height );

			// From here on out, the "new" values are the actual width/height values to consider.
			$watermark_width  = $new_watermark_width;
			$watermark_height = $new_watermark_height;

			// error_log('$watermark_width: ' .$watermark_width);
			// error_log('$watermark_height: ' .$watermark_height);.
		}

		// Enable imagealphablending for correct PNG rendering.
		imagealphablending( $image, true );
		imagealphablending( $watermark, true );

		// Calculate position of watermark based on settings.
		$watermark_position = $settings['ft_gallery_position'];
		$watermark_margin   = $settings['ft_watermark_image_margin'];
		$position           = array(
			'x' => 0,
			'y' => 0,
		);
		switch ( $watermark_position ) {
			case 'top-left':
				$position = array(
					'x' => 0 + $watermark_margin,
					'y' => 0 + $watermark_margin,
				);
				break;
			case 'top-right':
				$position = array(
					'x' => ( $image_width - $watermark_width ) - $watermark_margin,
					'y' => 0 + $watermark_margin,
				);
				break;
			case 'top-center':
				$position = array(
					'x' => ( $image_width - $watermark_width ) / 2,
					'y' => 0 + $watermark_margin,
				);
				break;
			case 'center':
				$position = array(
					'x' => ( $image_width - $watermark_width ) / 2,
					'y' => ( $image_height - $watermark_height ) / 2,
				);
				break;
			case 'bottom-left':
				$position = array(
					'x' => 0 + $watermark_margin,
					'y' => ( $image_height - $watermark_height ) - $watermark_margin,
				);
				break;
			case 'bottom-right':
				$position = array(
					'x' => ( $image_width - $watermark_width ) - $watermark_margin,
					'y' => ( $image_height - $watermark_height ) - $watermark_margin,
				);
				break;
			case 'bottom-center':
				$position = array(
					'x' => ( $image_width - $watermark_width ) / 2,
					'y' => ( $image_height - $watermark_height ) - $watermark_margin,
				);
				break;
			default:
				// Allow devs to run their own calculations here.
				$position = apply_filters( 'ft_watermarking_add_watermark_position', $position, $attachment_id, $gallery_id );
				break;
		}

		// Copy the entire $watermark image onto a matching sized portion of the $image.
		imagecopy( $image, $watermark, $position['x'], $position['y'], 0, 0, $watermark_width, $watermark_height );

		// Get the MIME type of the original image, so we know which image function to call when saving.
		switch ( $image_meta['mime'] ) {
			/**
			 * JPEG
			 */
			case 'image/jpeg':
			case 'image/jpg':
				// Save image as JPEG.
				imagejpeg( $image, $image_path );
				break;

			/**
			 * PNG
			 */
			case 'image/png':
				// Save image as PNG.
				imagepng( $image, $image_path );
				break;

			/**
			 * GIF
			 */
			case 'image/gif':
				// Save image as GIF.
				imagegif( $image, $image_path );
				break;
		}

		// Free up resources.
		imagedestroy( $image );
		imagedestroy( $watermark );

		// Mark attachment as watermarked, so we don't do this again.
		if ( true == get_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', true ) ) {
			$final_list = get_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', true ) . ',' . $final_image_size_check;
		} else {
			$final_list = $final_image_size_check;
		}
		update_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', $final_list );
		// error_log('RETURN POST META FOR IMAGE = '.get_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', true ) == TRUE);
		// return
		// error_log('created new image with GD Library');
		// error_log(print_r($image));.
	}

	/**
	 * Watermark Imagick
	 *
	 * Watermark image using Imagick
	 *
	 * @param int $attachment_id Attachment ID.
	 * @param int $gallery_id Gallery ID.
	 * @param int $final_image_size_check The final size check of the image.
	 * @since 1.0
	 */
	public function watermark_imagick( $attachment_id, $gallery_id, $final_image_size_check ) {

		// Get instance
		// $instance = FTGallery_Gallery_Shortcode::get_instance_ftgallery_watermarking_ajax();.
		$display_gallery = new Display_Gallery();
		$settings        = $display_gallery->ft_gallery_get_option_or_get_postmeta( $gallery_id );

		// Get image and watermark image.
		$image_path     = get_attached_file( $attachment_id );
		$watermark_path = get_attached_file( $settings['ft_watermark_image_id'] );

		// Get images.
		$image     = new Imagick( $image_path );
		$watermark = new Imagick( $watermark_path );

		// Get widths and heights for the image and watermark.
		$image_size   = $image->getImageGeometry();
		$image_width  = $image_size['width'];
		$image_height = $image_size['height'];

		$watermark_size   = $image->getImageGeometry();
		$watermark_width  = $watermark_size['width'];
		$watermark_height = $watermark_size['height'];

		// Get metadata (MIME type) for the image and watermark.
		$image_meta     = $image->getFormat();
		$watermark_meta = $watermark->getFormat();

		// If the watermark exceeds the width or height of the image, scale the watermark down.
		$scale_factor = 0.3;
		if ( $watermark_width > $image_width || $watermark_height > $image_height ) {
			// Calculate new watermark size.
			$new_watermark_width  = $watermark_width * $scale_factor;
			$new_watermark_height = $watermark_height * $scale_factor;

			// error_log('$watermark_width: ' .$new_watermark_width);
			// error_log('$watermark_height: ' .$new_watermark_height);
			// Create resized watermark image.
			$watermark->scaleImage( $new_watermark_width, $new_watermark_height );

		}

		// Calculate position of watermark based on settings.
		$watermark_position = $settings['ft_gallery_position'];
		$watermark_margin   = $settings['ft_watermark_image_margin'];
		$position           = array(
			'x' => 0,
			'y' => 0,
		);
		switch ( $watermark_position ) {
			case 'top-left':
				$position = array(
					'x' => ( 0 + $watermark_margin ),
					'y' => ( 0 + $watermark_margin ),
				);
				break;
			case 'top-right':
				$position = array(
					'x' => ( ( $image_width - $watermark_width ) - $watermark_margin ),
					'y' => ( 0 + $watermark_margin ),
				);
				break;
			case 'top-center':
				$position = array(
					'x' => ( ( $image_width - $watermark_width ) / 2 ),
					'y' => ( 0 + $watermark_margin ),
				);
				break;
			case 'center':
				$position = array(
					'x' => ( ( $image_width - $watermark_width ) / 2 ),
					'y' => ( ( $image_height - $watermark_height ) / 2 ),
				);
				break;
			case 'bottom-left':
				$position = array(
					'x' => ( 0 + $watermark_margin ),
					'y' => ( ( $image_height - $watermark_height ) - $watermark_margin ),
				);
				break;
			case 'bottom-right':
				$position = array(
					'x' => ( ( $image_width - $watermark_width ) - $watermark_margin ),
					'y' => ( ( $image_height - $watermark_height ) - $watermark_margin ),
				);
				break;
			case 'bottom-center':
				$position = array(
					'x' => ( ( $image_width - $watermark_width ) / 2 ),
					'y' => ( ( $image_height - $watermark_height ) - $watermark_margin ),
				);
				break;
			default:
				// Allow devs to run their own calculations here.
				$position = apply_filters( 'ft_watermarking_add_watermark_position', $position, $attachment_id, $gallery_id );
				break;
		}

		// Copy the entire $watermark image onto a matching sized portion of the $image.
		$image->compositeImage( $watermark, Imagick::COMPOSITE_MATHEMATICS, $position['x'], $position['y'] );

		// Save.
		$image->writeImage( $image_path );

		// Free up resources.
		unset( $image );
		unset( $watermark );

		// Mark attachment as watermarked, so we don't do this again.
		if ( true == get_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', true ) ) {
			$final_list = get_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', true ) . ',' . $final_image_size_check;
		} else {
			$final_list = $final_image_size_check;
		}
		update_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', $final_list );
		// error_log('RETURN POST META FOR IMAGE = '.get_post_meta( $attachment_id, '_ft_gallery_watermarking_applied', true ) == TRUE);
		// error_log('created new image with Imagick Library');.
	}

	/**
	 * Get Instance FT Gallery Watermarking Ajax
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The FTGallery_Watermarking_Ajax object.
	 */
	public static function get_instance_ftgallery_watermarking_ajax() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof FTGallery_Watermarking_Ajax ) ) {
			self::$instance = new FTGallery_Watermarking_Ajax();
		}

		return self::$instance;

	}

}
// Load the AJAX class.
$ftgallery_watermarking_ajax = FTGallery_Watermarking_Ajax::get_instance_ftgallery_watermarking_ajax();
