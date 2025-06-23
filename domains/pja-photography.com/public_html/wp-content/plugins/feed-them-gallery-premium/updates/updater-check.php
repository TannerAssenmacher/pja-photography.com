<?php
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
if (!defined('SLICKREMIX_STORE_URL')) {
    define('SLICKREMIX_STORE_URL', 'https://www.slickremix.com/'); // you should use your own CONSTANT name, and be sure to replace it throughout this file.
}
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
//New Updater
include(dirname(__FILE__) . '/namespaced_updater_overrides.php');
// Licensing and update code
/**
 * Feed Them Social Premium Plugin Updateru
 *
 * @since 1.0.0
 */
function feed_them_gallery_premium_plugin_updater() {
    $plugin_identifier = 'feed_them_gallery_Premium';
    $item_name = 'Feed Them Gallery Premium';
    $current_version = FEED_THEM_GALLERY_PREMIUM_VERSION;
    $author = 'slickremix';
    // retrieve our license key from the DB
    $license_key = trim(get_option($plugin_identifier . '_license_key'));
    $store_url = SLICKREMIX_STORE_URL;
    //Build updater Array
    $plugin_details = array(
        'version' => $current_version,      // current version number
        'license' => $license_key,          // license key (used get_option above to retrieve from DB)
        'item_name' => $item_name,          // name of this plugin
        'author' => $author                 // author of this plugin
    );

    // setup the updater
    $edd_updater = new feed_them_gallery_premium\SlickRemix_updater_overrides($store_url, __FILE__, $plugin_details, $plugin_identifier, $item_name);
    //Setup the activator
    $edd_update = new feed_them_gallery_premium\EDD_SL_Plugin_Licence_Manager($plugin_identifier, $item_name, $store_url);

}

add_action('plugins_loaded', 'feed_them_gallery_premium_plugin_updater');
?>