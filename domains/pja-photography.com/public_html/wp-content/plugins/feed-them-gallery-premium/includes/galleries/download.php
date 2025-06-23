<?php
/*
 This handles the headers to force the zip download
 This file will be accessed directly from the ajax call, look at the /js/general.js.
*/

// Include the WP to use sanatizer functions.
if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
	/** The config file resides in ABSPATH */
	require_once ABSPATH . 'wp-config.php';
}

define( 'zip_attachments_path', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );

if ( isset( $_REQUEST['ft_gallery_pretty_filename'] ) && ! empty( $_REQUEST['ft_gallery_real_filename'] ) ) {

	$pretty_filename = sanitize_file_name( $_GET['ft_gallery_pretty_filename'] );
	$real_filename   = sanitize_file_name( $_GET['ft_gallery_real_filename'] );

	$upload_dir = wp_upload_dir();
	$file       = $upload_dir['path'] . '/' . $real_filename;

	header( 'Content-Type: application/zip' );
	header( 'Content-Length: ' . filesize( $file ) );
	header( 'Content-Disposition: attachment; filename="' . $pretty_filename . '.zip"' );

	readfile( $file );
	unlink( $file );

	exit;
}
