<?php namespace feed_them_gallery;

// Display Images Gallery
$display_gallery = new Display_Gallery();

// Get Gallery Options via the Rest API
// The $post_id string comes from the function this file is included in which is the save_meta_box function on the save-meta-box.php file in the free version on around line 708.
$settings = $display_gallery->ft_gallery_get_option_or_get_postmeta( $post_id );


$my_post = stripslashes_deep( $_POST );

if ( $settings ) {
	$settings['config']['watermarking']          = ( isset( $my_post['ft_gallery_watermark'] ) ? 'imprint' : 'no' );
	$settings['config']['watermarking_image_id'] = absint( $my_post['ft_watermark_image_id'] );
	$settings['config']['watermarking_position'] = preg_replace( '#[^a-z0-9-_]#', '', $my_post['ft_gallery_position'] );
	$settings['config']['watermarking_margin']   = absint( $my_post['ft_watermark_image_margin'] );
}

// Get AJAX instance.
$instance        = new FTGallery_Watermarking_Ajax();
$instance_common = new FTGallery_Create_Image();


// $image_list = $display_gallery->ft_gallery_get_media_rest($post_id, '100');.
$args       = array(
	'post_parent'    => $post_id,
	'post_type'      => 'attachment',
	'post_mime_type' => 'image',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'asc',
	'exclude'        => 0, // Exclude featured thumbnail.
);
$image_list = get_posts( $args );
// $images_count = count( $attachments );
 // error_log(print_r($image_list, true) . '<br/>Image List<br/><br/>');
// $gallery_class = new Gallery();
// $gallery_options_returned = $gallery_class->ft_gallery_get_gallery_options_rest($post_id);.
$display_gallery          = new Display_Gallery();
$gallery_options_returned = $display_gallery->ft_gallery_get_option_or_get_postmeta( $post_id );


$allowed_sizes = isset( $gallery_options_returned['ft_watermark_image_sizes']['image_sizes'] ) ? $gallery_options_returned['ft_watermark_image_sizes']['image_sizes'] : array();
$allowed_sizes = array_keys( $allowed_sizes );
// $allowed_sizes = array('thumbnail', 'large');
// error_log(print_r($allowed_sizes, true) . '<br/>Allowed Sizes<br/><br/>');
// echo '<pre>';
// print_r($allowed_sizes);
 // echo '</pre>';
// Iterate through gallery images.
foreach ( $image_list as $key => $image ) {

	$image = wp_prepare_attachment_for_js( $image->ID );
	// $meta = wp_get_attachment_metadata( $image['id'] );
	// $image_meta = $meta['sizes'];
	// error_log('Image ID ' . $image['id']);.
	$filepath     = get_attached_file( $image['id'] );
	$filebasename = wp_basename( $image['filename'] );

	// error_log('FILEPATH = ' . $filepath);
	// $img_file = str_replace($filebasename, wp_basename($image['media_details']['file']), $filepath);
	// echo $img_file;.
	$sizes          = $image['sizes'];
	$final_image_id = $image['id'];

	// echo '<pre>';
	// print_r($sizes);
	// echo '</pre>';
	// $return = true;
	// Iterate through sizes images.
	foreach ( $sizes as $key => $image_final ) {
		$final_name_size = $key;
		if ( in_array( $key, $allowed_sizes ) ) {

			$url = $image_final['url'];

			$path           = wp_parse_url( $url, PHP_URL_PATH );
			$path_fragments = explode( '/', $path );
			$end            = end( $path_fragments );

			// error_log(print_r($image_final, true) . '<br/>Image Final<br/><br/>');.
			$img_file = str_replace( $filebasename, wp_basename( $end ), $filepath );


			// error_log('My New Attachment ID = '.$image_attachment_id);
			// echo  $img_file;
			// $pathInfo = strstr($img_file, 'wp-content');
			// $pathInfo = $get_site_url.'/'.$pathInfo;
			// error_log('did we get this far? ' . $end . ' Sizes: ' . print_r($sizes, false) . ' Image File: ' . $img_file . ' Path Info: ' . $filebasename);
			// error_log($final_name_size);.
			$watermark_size_check = get_post_meta( $image['id'], '_ft_gallery_watermarking_applied', true );
			$already_watermarked  = explode( ',', $watermark_size_check );
			// error_log(print_r($already_watermarked, true) . 'See list of existing watermarked photos');
			// Check if this attachment has already been watermarked
			// If so, skip it.
			$ft_gallery_duplicate_image = isset( $gallery_options_returned['ft_gallery_duplicate_image'] ) ? $gallery_options_returned['ft_gallery_duplicate_image'] : '';
			if ( ! in_array( 'full', $already_watermarked ) && 'yes' === $ft_gallery_duplicate_image ) {

				$image = $sizes['full']['url'];

				// error_log('asdfasdfsadf'. $image);
				// error_log(print_r($image, true));
				// error_log($image_final['full']['url'] . ' Full FILE NAME WITH HTTP<br/><br/>');
				//
				// Create a duplicate of the full image before watermarking  //
				// .
				$force_overwrite = true;
				// Generate the new cropped gallery image.
				// NOTE: This is working at the moment however we need to find a way to then
				// get the newly created duplicate and add that to the.
				$instance_common->resize_image( $image, $sizes['full']['width'], $sizes['full']['height'], false, 'c', '100', false, null, $force_overwrite );
				// error_log('Duplicate Image URL: ' . $common);
				// error_log($img_file . '<br/>' . $key . ' sent file to get watermarked<br/><br/>');.
				// echo $img_file . '<br/>' . $key . ' sent file to get watermarked<br/><br/>';.
				// error_log('FULL IMAGE HAS BEEN DUPLICATED BEFORE WATERMARKING');.
			}


			// add_watermark() will skip the image if we've already added a watermark to it.
			$instance->add_watermark( $final_image_id, $post_id, $img_file, $final_name_size, $allowed_sizes );


		} else {
			// error_log(' Skipping this image id ' . $end . '<br/>type ' . $key);
			// $new_sizes = $end;
			// error_log('<br/><br/>' . $key . '<br/>' . $new_sizes);
			// echo '<br/><br/>Skipping this image size:'.$key .'<br/>'.$new_sizes ;.
		}
	}
}
