=== Feed Them Gallery Premium ===
Contributors: slickremix, mikeyhoward1977
Tags:   gallery, woocommerce gallery, sell photos
Requires at least: 4.5.0
Tested up to: 5.7
Stable tag: 1.1.9
License: GPLv2 or later
Create customizable Image Galleries and sell your images using WooCommerce.

== Description ==

Simple and easy to setup.

ALL SlickRemix plugins come with Amazing Support! If you need help or have questions we're here to help, just create a ticket on our [website](https://www.slickremix.com/my-account/) and we’ll get to you as quickly as we can! (usually within 24hrs)

== Installation ==

= Install from WordPress Dashboard =
  * Log into WordPress dashboard then click **Plugins** > **Add new** > Then under the title "Install Plugins" click **Upload** > **choose the zip** > **Activate the plugin!**

= Install from FTP =
  * Extract the zip file and drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

== Changelog ==
= Version 1.1.9 Wednesday, April 14th, 2021 =
   * FIX: Compatible with PHP 8
   
= Version 1.1.8 Thursday, March 25th, 2021 =
   * FIX: Add !wp_doing_ajax() around album class scripts so ajax is not accessible to the front end.
   * FIX: Wrong phrase in POT file for, 'You do not currently have access to any galleries.'
   
= Version 1.1.7 Tuesday, March 23rd, 2021 =
   * NEW: div wrapper around Album links on My Account page of WooCommerce if you are using the Client Manager extension.
   * FIX: Disable Right click option on all pages under Settings > Extensions > Premium.
   * WP-ADMIN FIX: Remove scripts loading on page posts causing issues with other plugins. ( Ninja forms )
   * UPDATED: Calls to work with Client Manager Extension framework update.

= Version 1.1.6 Tuesday, December 8th, 2020 =
   * NEW: When products are created the permalink for the product is created from the title of the image. If no title is set it will use the image slug.
   * NEW: Settings > Extensions > Premium: Product Description: If checked, this will place your image description in the Product Description area instead of the Main Descriptions area.
   * FIXED: Edit Product button in popup was not directing to the proper WooCommerce product to edit.

= Version 1.1.5 Thursday, November 5th, 2020 =
   * FIXED: Languages folder path
   * NEW EXTENSION: It's been a long time coming but we are proud to introduce the brand new <a href="https://www.slickremix.com/downloads/feed-them-gallery-clients-manager/" target="_blank">Clients Manager</a> extension for Feed Them Gallery.

= Version 1.1.4 Monday, August 17th, 2020 =
   * FIX: Make compatible with WordPress 5.5 update. (There is still a [bug](https://core.trac.wordpress.org/ticket/50976#comment:7) with pagination on the front end but this is a core issue we hope will be resolved in the next week);
   * FIX: permission_callback "__return_true" set for register_rest_route

= Version 1.1.3 Thursday, August 11th, 2020 =
    * FIX: Multi-site compatibility that was causing Feed Them Gallery to not be activated and error on Multi-site network.
    * UPDATED: Text in Readme and FeedThemGallery.com Examples versus Demo text to make things more understandable.
    * NEW: Demo Builder launched! You can now try Feed Them Gallery out and it's premium extensions before purchasing. [Try the Demo!](https://demo.feedthemgallery.com/)

= Version 1.1.2 Tuesday, July 14th, 2020 =
   * Works with WordPress 5.4.2 & WooCommerce 4.3.0

= Version 1.1.1 Thursday, July 11th, 2019 =
   * FIX: Wrapped Album Class calls in current_user_can( 'manage_options' ) since we only need to load these functions for logged in admin users.

= Version 1.1.0 Tuesday, July 9th, 2019 =
   * NEW: Tags Settings: When using the Image post in grid (Masonry) layout we added an additional option for Photo Caption Placement above or below the image.

= Version 1.0.9 Monday, July 8th, 2019 =
   * NEW: Tags Page: Added WooCommerce Add to cart options so you can make your tags page look similar to your gallery if you want.
   * FIX: Settings: Take user to product option not working if your product was a variable.

= Version 1.0.8 Wednesday, July 3rd, 2019 =
   * NEW: Security Refactor of the whole plugin to stop XSS injections and other possibly malicious attempts to hack through the plugin.
   * NEW: WooCommerce Tab: Cart Icon over image if using the Gallery layout option. You can choose the position, and colors for the icon.
   * NEW: WooCommerce Tab: Hide the Add to Cart options/variations on page and popup.
   * NEW: Tags Page: Select option added that will show a list of existing tags you can choose to search.
   * FIX: Albums Edit Screen: Select option for Image Size on Page was not working correctly.

= Version 1.0.7 Friday, May 24th, 2019 =
 * FIX: Unneeded isset for term_slug on the media taxonomies file. This was causing a conflict with the Enhanced Media Library PRO plugin.
 * NEW: Global disable right click option on the settings page now. This will allow for the no right click option to work on the whole website.

= Version 1.0.6 Tuesday, May 14th, 2019 =
  * NOTE: Thanks to everyone who has been waiting so patiently for this update. It's taken us over 7 months to complete these additions between the 2 of us, we hope you enjoy them.
  * NEW: Optimized the plugins framework to be more streamlined. Including the options for Galleries. This will speed up the time the galleries takes to save and the front end loading.
  * NEW: The gallery page now has a template you can customize by adding it to your theme. If you have the premium version you can utilize the albums and tags templates too. [See how it works](https://www.slickremix.com/docs/overriding-templates/).
  * NEW: Add your own text for the Free Download option under the Layout tab of your galleries.
  * NEW: Now you can view galleries and albums using the permalink link as well as the shortcode option. This speeds up the time it takes to get galleries/albums completed and also makes it perfect if you are using the premium version and have created albums.
  * NEW: Create Albums of Galleries. We made it easy to add the galleries you want and then even drag and drop to sort them.
  * NEW: Add Tags to Galleries and images in a gallery. You will see a new Gallery Tab option called Tags where you can customize the look of the tags on the page or the images.
  * NEW: Tags Template page where you can customize the look of the tags page for images and galleries. In our next update we will be creating a search option on the tags page as well.
  * NOTE: Works with WooCommerce 3.6.2

= Version 1.0.5 Wednesday, October 24th, 2018 =
  * NEW: Update Download/file links on "Variable" WooCommerce products when creating individual woo products for image(s) in gallery this way customers/clients can download the highest rez image(s) after purchase.
  * NEW: Update each Image to be the main image for each variation of a "Variable" WooCommerce product when creating individual woo products for images in gallery.
  * UPDATED: Updated JS scripts to have versioning so browsers clear js cache when we push updates.
  * ADDED: WooCommerce version compatibility to header of main file.

= Version 1.0.4 Thursday, August 23rd, 2018 =
  * NEW: When clicking the Create Individual Image Product(s) button you will see a progress count down and loading cue as each image is converted into a product.
  * NEW: Added constant FTGP_CURRENT_VERSION at the end of our css and js files so updated files in new version will reload right away. ie * ...js?ver=1.0.4
  * FIX: Timeout on some servers when converting a lot of images into products.
  * FIX: Create ZIP product option now adding the file url to the product and creating the product fully.
  * CHANGE: The Cart icon is now always visible on each image when editing a gallery. This makes it visually easier to verify that all the image have products.
  * CHANGE: Clarify some WooCommerce wording throughout admin pages.
  * CHANGE: Success and Error messages are now returned with jquery for image or ZIP product creation.

= Version 1.0.3 Wednesday, July 4th, 2018 =
  * FIX: Text edits for certain notices
  * CHANGE: Link for Zip and WooCommerce tab #hashtag for easier navigation.

= Version 1.0.2 Thursday, May 10th, 2018 =
  * FIX: Create WooCommerce Product category on product creation.
  * CHANGED: "Single Model Product" to Global Model Product under the WooCommerce tab.
  * ADDED: Use Smart Image Orientation  option. This allows users to create WooCommerce tab model products for each orientation. The plugin will then automatically what orientation the photo is and create the product based on it's orientation.
    (Smart Image Orientation will also work with the "Auto Create a product for each image uploaded." option.)
  * ADDED: Landscape Image Model Product - set this model product option under the WooCommerce tab.
  * ADDED: Square Image Model Product - set this model product option under the WooCommerce tab.
  * ADDED: Portrait Image Model Product - set this model product option under the WooCommerce tab.
  * FIX: Text throughout plugin to have proper translation code and proper text domain.

= Version 1.0.1 Saturday, December 2nd, 2017 =
  * FIX: Create WooCommerce Product on upload to add proper source and filename to download link in simple product.
  * FIX: Single Model Product select option to show all of the woocommerce products in the list.

= Version 1.0.0 Friday, October 6th, 2017 =
 * Initial Release

== Frequently Asked Questions ==
You can find answers to your questions on our website https://www.slickremix.com/

= Are there Extensions for this plugin? =

Coming soon.

== Screenshots ==

Coming soon.
