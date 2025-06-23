<?php
/**
 * Feed Them Gallery Premium (Main Class)
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * Plugin Name: Feed Them Gallery Premium
 * Plugin URI: https://www.slickremix.com/
 * Description: Extends Feed Them Gallery by adding Gallery ZIPPING, Tags, Pagination, Sort order, Image Protection (watermarking) and WooCommerce image/ZIP product creation.
 * Version: 1.1.9
 * Tested up to: WordPress 5.7
 * Stable tag: 1.1.9
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 3.0.0
 * WC tested up to: 5.2.0
 *
 * @package  FeedThemSocialPremium/Core
 * @copyright   Copyright (c) 2012-2021 SlickRemix
 *
 * Need Support? https://www.slickremix.com/my-account
 */

// Makes sure any js or css changes are reloaded properly. Added to enqued css and js files throughout.
define( 'FTGP_CURRENT_VERSION', '1.1.9' );

final class Feed_Them_Gallery_Premium {

    /**
     * Main Instance of Display Posts Feed
     *
     * @var $instance
     */
    private static $instance;

    /**
     * @var		int		$required_ftg	The minimum required Feed Them Gallery version
     * @since	1.0
     */
    private static $required_ftg = '1.3.7';

    /**
     * Create Instance of Feed Them Gallery
     *
     * @since 1.0.0
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Feed_Them_Gallery_Premium ) ) {
            self::$instance = new Feed_Them_Gallery_Premium();

            if ( ! function_exists( 'is_plugin_active' ) ) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }

            // Third check the php version is not less than 5.2.9
            // Make sure php version is greater than 5.3.
            if ( function_exists( 'phpversion' ) ) {
                $phpversion = phpversion();
            }
            $phpcheck = '5.2.9';
            if ( $phpversion <= $phpcheck ) {
                // if the php version is not at least 5.3 do action.
                deactivate_plugins( 'feed-them-gallery-premium/feed-them-gallery-premium.php' );
                if ( $phpversion < $phpcheck ) {
                    add_action( 'admin_notices', array( self::$instance, 'ft_gallery_premium_required_php_check1' ) );

                }
            }

            // Setup Constants for FT Gallery.
            self::$instance->setup_constants();
            // Include the files.
            self::$instance->includes();

			self::$instance->load_textdomain();

            // Hooks
            self::$instance->hooks();

            // Plugin License Page
            // self::$instance->plugin_license_page = new feed_them_gallery_premium\FT_Gallery_Plugin_License_Page();.
        }

        return self::$instance;
    }

    /**
     * Setup hooks.
     *
     * @since   1.0
     * @return  void
     */
    private function hooks()    {
		add_action( 'plugins_loaded', array( $this, 'check_requirements' ) );
    } // hooks

    /**
     * Setup Constants
     *
     * Setup plugin constants for plugin
     *
     * @since 1.0.0
     */
    private function setup_constants() {
        // Makes sure the plugin is defined before trying to use it.
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $plugin_data    = get_plugin_data( __FILE__ );
        $plugin_version = $plugin_data['Version'];
        // Plugin version.
        if ( ! defined( 'FEED_THEM_GALLERY_PREMIUM_VERSION' ) ) {
            define( 'FEED_THEM_GALLERY_PREMIUM_VERSION', $plugin_version );
        }
        // Plugin Folder Path.
        if ( ! defined( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_PATH' ) ) {
            define( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_PATH', plugins_url() );
        }
        // Plugin Directoy Path.
        if ( ! defined( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR' ) ) {
            define( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR', plugin_dir_path( __FILE__ ) );
        }
        // Basename
        if ( ! defined( 'FEED_THEM_GALLERY_PREMIUM_BASENAME' ) )	{
            define( 'FEED_THEM_GALLERY_PREMIUM_BASENAME', plugin_basename( __FILE__ ) );
        }
    }

    /**
     * Includes Files
     *
     * Include files needed for Feed Them Gallery
     *
     * @since 1.0.0
     */
    private function includes() {

        // Settings
        include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'admin/settings/settings-functions.php';

        // Updater Files.
        include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'updates/namespaced_EDD_SL_Plugin_Licence_Manager.php';
        include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'updates/updater-check.php';

        // Image and Gallery Tags.
        include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/tags/image_and_gallery_tags_class.php';

		// Client functions
		include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'admin/clients/client-functions.php';

		// Email functions
		include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/emails/email-functions.php';

        // License Page
        // include(FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'admin/plugin-license-page.php');.
    }

    /**
	 * Check for plugin minimum requirements.
	 *
	 * @since	1.0
	 * @return	void
	 */
	public function check_requirements()	{
		// Do nothing if FTG is not activated
        if ( ! class_exists( 'Feed_Them_Gallery', false ) || version_compare( self::$required_ftg, FTG_CURRENT_VERSION, '>' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
			deactivate_plugins( FEED_THEM_GALLERY_PREMIUM_BASENAME );
        }
	} // check_requirements

    /**
     * FT Gallery Required php Check
     *
     * Are they running proper PHP version?
     *
     * @since 1.0.0
     */
    public function ft_gallery_premium_required_php_check1() {
        echo '<div class="error"><p>';
        echo sprintf(
            esc_html( '%1$s Feed Them Gallery Warning:%2$s Your php version is %1$s%3$s%2$s. You need to be running at least %1$s5.3%2$s or greater to use this plugin. Please upgrade the php by contacting your host provider. Some host providers will allow you to change this yourself in the hosting control panel too. %4$s If you are hosting with BlueHost or Godaddy and the php version above is saying you are running %1$s5.2.17%2$s but you are really running something higher please %5$sclick here for the fix%6$s. If you cannot get it to work using the method described in the link please contact your host provider and explain the problem so they can fix it.', 'feed-them-gallery-premium' ),
            '<strong>',
            '</strong>',
            esc_html( phpversion() ),
            '<br/><br/>',
            '<a href="' . esc_url( 'https://wordpress.org/support/topic/php-version-difference-after-changing-it-at-bluehost-php-config?replies=4' ) . '" target="_blank">',
            '</a>'
        );
        echo '</p></div>';
    }

    /**
     * Display a notice if FTG not active or at required version.
     *
     * @since	1.0
     */
    public static function notices()	{
        if ( ! defined( 'FTG_CURRENT_VERSION' ) )	{
            $message = __( 'Feed Them Gallery - Premium has been deactivated as it requires that Feed Them Gallery be installed and activated.', 'feed-them-gallery-premium' );
        } else	{
            $message = sprintf( __( 'Feed Them gallery - Premium has been deactivated as it requires Feed Them Gallery version %s and higher.', 'feed-them-gallery-premium' ), self::$required_ftg );
        }

        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>' . $message . '</p>';
        echo '</div>';
    } // notices

	/**
	 * Internationalization
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function load_textdomain()	{
		$lang_dir = FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'languages/';
		$lang_dir = apply_filters( 'feed-them-gallery-premium_languages_directory', $lang_dir );

		$locale = apply_filters( 'feed-them-gallery-premium', get_locale(), 'feed-them-gallery-premium' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'feed-them-gallery-premium', $locale );

		$mofile_local   = $lang_dir . $mofile;
		$mofile_global  = WP_LANG_DIR . '/feed-them-gallery-premium/' . $mofile;

		if ( file_exists( $mofile_global ) )	{
			load_textdomain( 'feed-them-gallery-premium', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			load_textdomain( 'feed-them-gallery-premium', $mofile_local );
		} else {
			load_plugin_textdomain( 'feed-them-gallery-premium', false, $lang_dir );
		}
	} // load_textdomain
}

/**
 * FT Gallery System Version
 *
 * Returns current plugin version (Must be outside the final class to work)
 *
 * @return mixed
 * @since 1.0.0
 */
function ft_gallery_premium_check_version() {
    $plugin_data = get_plugin_data( __FILE__ );
    return $plugin_data['Version'];
}

/**
 * Feed Them Gallery
 *
 * Start it up!
 *
 * @return feed_them_gallery_premium
 * @since 1.0.0
 */
function feed_them_gallery_premium() {
    return Feed_Them_Gallery_Premium::instance();
}
// Get FTG Running.
feed_them_gallery_premium();
