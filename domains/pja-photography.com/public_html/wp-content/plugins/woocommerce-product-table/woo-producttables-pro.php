<?php
/**
 * Plugin Name: Woo Product Table PRO
 * Description: Product Table by WooBeWoo PRO. Best plugins from Woobewoo!
 * Plugin URI: https://woobewoo.com/plugins/table-woocommerce-plugin/
 * Author: WooBeWoo
 * Author URI: https://woobewoo.com/
 * Version: 1.5.8
 * Woo: 5249458:4744c2f1ec117aea18fef1f9237307fb
 * CC: 25553217
 * WC requires at least: 3.4.0
 * WC tested up to: 6.5.0
 **/
 
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wpUpdater.php');

register_activation_hook(__FILE__, 'wooproducttablesProActivateCallback');
register_deactivation_hook(__FILE__, array('ModInstallerWtbp', 'deactivate'));
register_uninstall_hook(__FILE__, array('ModInstallerWtbp', 'uninstall'));

add_filter('pre_set_site_transient_update_plugins', 'checkForPluginUpdatewooproducttablesPro');
add_filter('plugins_api', 'myPluginApiCallwooproducttablesPro', 10, 3);

if (!function_exists('getProPlugCodeWtbp')) {
	function getProPlugCodeWtbp() {
		return 'woo_producttables_pro';
	}
}
if (!function_exists('getProPlugDirWtbp')) {
	function getProPlugDirWtbp() {
		return basename(dirname(__FILE__));
	}
}
if (!function_exists('getProPlugFileWtbp')) {
	function getProPlugFileWtbp() {
		return basename(__FILE__);
	}
}
if (!function_exists('getProPlugFullPathWtbp')) {
	function getProPlugFullPathWtbp() {
		return __FILE__;
	}
}
if (!function_exists('getProPlugSlugWtbp')) {
	function getProPlugSlugWtbp() {       
		return 'woo-producttables-pro';
	}
}

if (!defined('S_YOUR_SECRET_HASH_' . getProPlugCodeWtbp())) {
	define('S_YOUR_SECRET_HASH_' . getProPlugCodeWtbp(), 'ng93#g3j9g#R#E)@KDPWKOK)Fkvvk#f30f#KF');
}

if (!function_exists('checkForPluginUpdatewooproducttablesPro')) {
	function checkForPluginUpdatewooproducttablesPro( $checkedData ) {
		if (class_exists('WpUpdaterWtbp')) {
			return WpUpdaterWtbp::getInstance( getProPlugDirWtbp(), getProPlugFileWtbp(), getProPlugCodeWtbp(), getProPlugFullPathWtbp() )->checkForPluginUpdate($checkedData);
		}
		return $checkedData;
	}
}
if (!function_exists('myPluginApiCallwooproducttablesPro')) {
	function myPluginApiCallwooproducttablesPro( $def, $action, $args ) {
		if (class_exists('WpUpdaterWtbp')) {
			return WpUpdaterWtbp::getInstance( getProPlugDirWtbp(), getProPlugFileWtbp(), getProPlugCodeWtbp(), getProPlugFullPathWtbp() )->myPluginApiCall($def, $action, $args);
		}
		return $def;
	}
}
/**
 * Check if there are base (free) version installed
 *
 * @param bool $isNetworkWide Check if site activated for network
 */
if (!function_exists('wooproducttablesProActivateCallback')) {
	function wooproducttablesProActivateCallback( $isNetworkWide ) {
		if (class_exists('FrameWtbp')) {
			$arguments = func_get_args();
			if (function_exists('is_multisite') && is_multisite()) {
				global $wpdb;
				if ($isNetworkWide) {
					$blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach ($blog_id as $id) {
						if (switch_to_blog($id)) {
							call_user_func_array(array('ModInstallerWtbp', 'check'), $arguments);
							restore_current_blog();
						}
					}
				} else {
					call_user_func_array(array('ModInstallerWtbp', 'check'), $arguments);
				}
			} else {
				call_user_func_array(array('ModInstallerWtbp', 'check'), $arguments);
			}
		}
	}
}
add_action('admin_notices', 'wooproducttablesProInstallBaseMsg');
if (!function_exists('wooproducttablesProInstallBaseMsg')) {
	function wooproducttablesProInstallBaseMsg() {
		if (!get_option('wtbp_full_installed') || !class_exists('FrameWtbp')) {
			$plugName = 'Product Table by WooBeWoo';
			$plugWpUrl = 'https://wordpress.org/plugins/woo-product-tables/';
			echo '<div class="notice error is-dismissible"><p><strong>
				Please install Free (Base) version of ' . esc_html($plugName) . ' plugin, you can get it <a target="_blank" href="' . esc_url($plugWpUrl) . '">here</a> or use Wordpress plugins search functionality, 
				activate it, then deactivate and activate again PRO version of ' . esc_html($plugName) . '. 
				In this way you will have full and upgraded PRO version of ' . esc_html($plugName) . '.</strong></p></div>';
		}
	}
}
