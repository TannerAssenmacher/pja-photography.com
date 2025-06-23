<?php
/**
 * For now this is just dummy mode to identify that we have installed licensed version
 */
class LicenseWtbp extends ModuleWtbp {
	// wc - from WC subscriptions, cc - codecanyon, ''/null - our (default)
	public $licenseType = '';
	public $pluginData = array();

	public function init() {
		parent::init();
		DispatcherWtbp::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		add_action('admin_notices', array($this, 'checkActivation'));
		add_action('init', array($this, 'addAfterInit'));
		$this->_licenseCheck();
		$this->_updateDb();
	}
	public function addAfterInit() {
		if (!function_exists('getProPlugDirWtbp')) {
			return;
		}
		$this->_setLicenceType();
		if ($this->licenseType != $this->getModel()->getLicenseType(false)) {
			$this->getModel()->setCredentials('', '', $this->licenseType);
			if ($this->getModel()->isActive()) {
				$this->getModel()->_removeActive();
			}
		}
		add_action('in_plugin_update_message-' . getProPlugDirWtbp() . '/' . getProPlugFileWtbp(), array($this, 'checkDisabledMsgOnList'), 1, 2);
	}
	public function checkDisabledMsgOnList( $plugin_data, $r ) {
		if ($this->getModel()->isExpired()) {
			$licenseTabUrl = FrameWtbp::_()->getModule('options')->getTabUrl('license');
			/* translators: 1: license link 2: PRO link for license tab */
			echo '<br />' . sprintf(esc_html__('Your license is expired. Once you extend your license - you will be able to Update PRO version. To extend PRO version license - follow %1$s, then - go to %2$s tab and click on "Re-activate" button to re-activate your PRO version.', 'woo-product-tables'),
				'<a href="' . esc_url($this->getExtendUrl()) . '" target="_blank">' . esc_html__('this link', 'woo-product-tables') . '</a>',
				'<a href="' . esc_url($licenseTabUrl) . '">' . esc_html__('License', 'woo-product-tables') . '</a>');
		}
	}
	public function checkActivation() {
		//$this->getModel()->_removeActive();
		if (!$this->getModel()->isActive()) {
			$isDismissable = false;
			$msgClasses = 'error';
			if ($isDismissable) {
				$dismiss = (int) FrameWtbp::_()->getModule('options')->get('dismiss_pro_opt');
				if ($dismiss) {
					return;	// it was already dismissed by user - no need to show it again
				}
				// Those classes required to display close "X" button in message
				$msgClasses .= ' notice is-dismissible woobewoo-pro-notice wtbp-notification';
				// And ofcorse - connect our core scripts (to use core ajax handler), and script with saving "dismiss_pro_opt" option ajax send request
				FrameWtbp::_()->getModule('templates')->loadCoreJs();
				FrameWtbp::_()->addScript('wtbp.admin.license.notices', $this->getModPath() . 'js/admin.license.notices.js');
			}
			$isExpired = $this->getModel()->isExpired();

			if ('wc' === $this->licenseType) {
				$wcLicenseData = $this->getWCLicenseData();
				if (false === $wcLicenseData) {
					echo '<div class="' . esc_attr($msgClasses) . '"><p>';
					if ($isExpired) {
						/* translators: 1: plugin name, 2: subscriptions path */
						echo sprintf(esc_html__('Your plugin %1$s PRO license is expired and we are unable to verify your subscriptions. Please go to %2$s and make sure you are logged in your WooCommerce.com account.', 'woo-product-tables'), esc_html(WTBP_WP_PLUGIN_NAME), '<b>' . esc_html('WooCommerce > Extensions > WooCommerce.com Subscriptions') . '</b>');
					} else {
						/* translators: 1: plugin name, 2: subscriptions path */
						echo sprintf(esc_html__('To activate your %1$s PRO plugin verification of your subscription is required. Go to %2$s and make sure you are logged in to your WooCommerce.com account.', 'woo-product-tables'), esc_html(WTBP_WP_PLUGIN_NAME), '<b>' . esc_html('WooCommerce > Extensions > WooCommerce.com Subscriptions') . '</b>');
					}
					echo '</p></div>';
				} else {
					$isExpired = $this->getModel()->isExpiredWC($wcLicenseData);
					if ($isExpired) {
						echo '<div class="' . esc_attr($msgClasses) . '"><p>';
						/* translators: %s: plugin name */
						echo sprintf(esc_html__('Your plugin %s PRO license is expired. It means your PRO version will work as usual - with all features and options, but you will not be able to update the PRO version and use PRO support.', 'woo-product-tables'), esc_html(WTBP_WP_PLUGIN_NAME));
						echo '</p></div>';
					} else if ($this->getModel()->activate($wcLicenseData)) { //if (true) {
						return;
					} else {
						echo '<div class="' . esc_attr($msgClasses) . '"><p>';
						foreach ($this->getModel()->getErrors() as $error) {
							echo esc_html(WTBP_WP_PLUGIN_NAME) . ' PRO: ' . esc_html($error);
						}
						echo '</p></div>';
					}
				}
			} else {
				
				echo '<div class="' . esc_attr($msgClasses) . '"><p>';

				if ($isExpired) {
					/* translators: %s: plugin name */
					echo sprintf(esc_html__('Your plugin %s PRO license is expired. It means your PRO version will work as usual - with all features and options, but you will not be able to update the PRO version and use PRO support.', 'woo-product-tables'), esc_html(WTBP_WP_PLUGIN_NAME));
					if (empty($this->licenseType)) {
						/* translators: %s: PRO version license url */
						echo ' ' . sprintf(esc_html__('To extend PRO version license - follow %s.', 'woo-product-tables'),
							'<a href="' . esc_url($this->getExtendUrl()) . '" target="_blank">' . esc_html__('this link', 'woo-product-tables') . '</a>');
					}
				} else {
					/* translators: 1: plugin name 2: PRO version license url */
					echo sprintf(esc_html__('You need to activate your copy of PRO version %1$s plugin. Go to %2$s tab and finish your software activation process.', 'woo-product-tables'),
						esc_html(WTBP_WP_PLUGIN_NAME),
						'<a href="' . esc_url(FrameWtbp::_()->getModule('options')->getTabUrl('license')) . '">' . esc_html__('License', 'woo-product-tables') . '</a>');
				}				
				echo '</p></div>';
			}
		}
	}
	public function getExtendUrl() {
		return $this->getModel()->getExtendUrl();
	}
	public function addAdminTab( $tabs ) {
		if (defined('IS_WOOBEWOO_DEMO') && IS_WOOBEWOO_DEMO) {
			$availableSites = array( SITE_ID_CURRENT_SITE, get_option( 'wpmuclone_default_blog' ) );
			if ( !in_array( get_current_blog_id(), $availableSites ) ) {
				return $tabs;
			}
		}
		$tabs[$this->getCode()] = array(
			'label' => esc_html__('License', 'woo-product-tables'),
			'callback' => array($this, 'getTabContent'),
			'fa_icon' => 'fa-hand-o-right',
			'sort_order' => 999,
		);

		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	private function _licenseCheck() {
		if ($this->getModel()->isActive()) {
			$this->getModel()->check();
			$this->getModel()->checkPreDeactivateNotify();
		}
	}
	private function _setLicenceType() {
		$pluginData = get_file_data(getProPlugFullPathWtbp(), array('cc_id'=>'CC', 'woo_id'=>'Woo'));
		if (!empty($pluginData['woo_id'])) {
			if ($this->isWCHelperExists()) {
				foreach (WC_Helper::get_local_woo_plugins() as $plugin) {
					if ($plugin['Woo'] == $pluginData['woo_id']) {
						$this->licenseType = 'wc';
						$this->pluginData = $plugin;
						return;
					}
				}
			}
		}

		if (!empty($pluginData['cc_id'])) {
			$this->licenseType = 'cc';
			$this->pluginData['product_id'] = $pluginData['cc_id'];
			$this->pluginData['type'] = 'cc-1-12-' . $pluginData['cc_id'];
			$this->pluginData['email'] = 'cc_user@cc.com';
		}
	}
	public function getWCLicenseData() {
		if ($this->isWCHelperExists() && 'wc' == $this->licenseType) {
			$pluginSlug = getProPlugSlugWtbp();
			$productId =  isset($this->pluginData['_product_id']) ? $this->pluginData['_product_id'] : -1;
			foreach (WC_Helper::get_subscriptions() as $subscription) {
				if ($subscription['product_id'] == $productId) {
					$start = isset($subscription['expires']) ? $subscription['expires'] - 24 * 3600 * 365 : 0;
					$now = time();
					if ($start > $now || $start <= 0) {
						$start = $now;
					}
					$subscription['type'] = 'wc-1-12-' . $start;
					$subscription['email'] = 'wc_user@wc.com';
					$subscription['key'] = $subscription['product_key'];
					return $subscription;
				}
			}
		}
		return false;
	}
	public function isWCHelperExists() {
		return class_exists('WC_Helper');
	}
	private function _updateDb() {
		$this->getModel()->updateDb();
	}
}
