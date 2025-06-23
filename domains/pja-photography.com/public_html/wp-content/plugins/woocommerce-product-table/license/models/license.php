<?php
class LicenseModelWtbp extends ModelWtbp {
	private $_apiUrl = '';
	public function __construct() {
		$this->_initApiUrl();
	}
	public function check() {
		$time = time();
		$lastCheck = (int) get_option('_last_important_check_' . WTBP_CODE);
		if (!$lastCheck || ( $time - $lastCheck ) >= 5 * 24 * 3600 /** 0/*remove last!!!*/) {
			$resData = $this->_req('check', array_merge(array(
				'url' => WTBP_SITE_URL,
				'plugin_code' => $this->_getPluginCode(),
			), $this->getCredentials()));
			if ($resData) {
				$this->_updateLicenseData( $resData['data']['save_data'] );
			} else {
				$this->_setExpired();
			}
			update_option('_last_important_check_' . WTBP_CODE, $time);
		} else {
			$daysLeft = (int) FrameWtbp::_()->getModule('options')->getModel()->get('license_days_left');
			if ($daysLeft) {
				$lastServerCheck = (int) FrameWtbp::_()->getModule('options')->getModel()->get('license_last_check');
				$day = 24 * 3600;
				$daysPassed = floor(( $time - $lastServerCheck ) / $day);
				if ($daysPassed > 0) {
					$daysLeft -= $daysPassed;
					FrameWtbp::_()->getModule('options')->getModel()->save('license_days_left', $daysLeft);
					FrameWtbp::_()->getModule('options')->getModel()->save('license_last_check', time());
					if ($daysLeft < 0) {
						$this->_setExpired();
					}
				}
			}
		}
		return true;
	}
	public function activate( $d = array() ) {
		$d['email'] = isset($d['email']) ? trim($d['email']) : '';
		$d['key'] = isset($d['key']) ? trim($d['key']) : '';
		$d['type'] = isset($d['type']) ? trim($d['type']) : '';
		$d['name'] = isset($d['name']) ? trim($d['name']) : '';
		if (!empty($d['email'])) {
			if (!empty($d['key'])) {
				$this->setCredentials($d['email'], $d['key'], $d['type'], $d['name']);

				$resData = $this->_req('activate', array_merge(array(
					'url' => WTBP_SITE_URL,
					'plugin_code' => $this->_getPluginCode(),
				), $this->getCredentials()));

				if (false != $resData) {
					$this->_updateLicenseData( $resData['data']['save_data'] );
					$this->_setActive();
					return true;
				}
			} else {
				$this->pushError(esc_html__('Please enter your License Key', 'woo-product-tables'), 'key');
			}
		} else {
			$this->pushError(esc_html__('Please enter your Email address', 'woo-product-tables'), 'email');
		}
		$this->_removeActive();
		return false;
	}
	private function _updateLicenseData( $saveData ) {
		FrameWtbp::_()->getModule('options')->getModel()->save('license_save_name', $saveData['license_save_name']);
		FrameWtbp::_()->getModule('options')->getModel()->save('license_save_val', $saveData['license_save_val']);
		FrameWtbp::_()->getModule('options')->getModel()->save('license_days_left', $saveData['days_left']);
		FrameWtbp::_()->getModule('options')->getModel()->save('license_last_check', time());
		if (isset($saveData['license_type'])) {
			$this->setLicenseType($saveData['license_type']);
		}
		$this->updateDbTables(true);
		if (isset($saveData['add_data']) && !empty($saveData['add_data'])) {
			$this->_processUpdateDbData( $saveData['add_data'] );
		}
	}
	private function _processUpdateDbData( $addData ) {
		$actionData = explode('=>', trim($addData));
		switch ($actionData[ 0 ]) {
			case 'db_install':	// Only database install for now
				$tblsData = explode('|', $actionData[ 1 ]);
				$cntData = count( $tblsData );
				for ($i = 0; $i < $cntData; $i += 2) {
					$tbl = '@__' . $tblsData[ $i ];
					$data = UtilsWtbp::unserialize( base64_decode($tblsData[$i + 1]) );
					foreach ($data as $uid => $d) {
						InstallerWtbp::installDataByUid($tbl, $uid, $d);
					}
				}
				break;
		}
	}
	private function _setExpired() {
		update_option('_last_expire_' . WTBP_CODE, 1);
		$this->_removeActive();
		if ($this->enbOptimization()) {
			$this->updateDbTables(false);
		}
	}
	public function isExpired() {
		return (int) get_option('_last_expire_' . WTBP_CODE);
	}
	public function isExpiredWC( $data ) {
		$isExpired = !isset($data['expired']) || $data['expired'];
		if ($isExpired) {
			update_option('_last_expire_wc_' . WTBP_CODE, 1);
			$this->_setExpired();
		}
		return $isExpired;
	}
	public function isActive() {
		$option = get_option(FrameWtbp::_()->getModule('options')->get('license_save_name'));
		$license = FrameWtbp::_()->getModule('options')->get('license_save_val');
		$isMainModuleActive = FrameWtbp::_()->getModule(WTBP_PLUG_PRO_MODULE);
		return ( $option && $option == $license && $isMainModuleActive );
	}
	public function _setActive() {
		update_option('_site_transient_update_plugins', ''); // Trigger plugins updates check
		update_option(FrameWtbp::_()->getModule('options')->get('license_save_name'), FrameWtbp::_()->getModule('options')->get('license_save_val'));
		delete_option('_last_expire_' . WTBP_CODE);
	}
	public function _removeActive() {
		$name = FrameWtbp::_()->getModule('options')->get('license_save_name');
		if (!empty($name)) {
			FrameWtbp::_()->getModule('options')->getModel()->save('license_save_name', '');
			delete_option($name);
		}
	}
	public function setCredentials( $email, $key, $type = '', $name = '' ) {
		$this->setLicenseType($type);
		$this->setEmail($email);
		$this->setLicenseKey($key);
		$this->setLicenseName($name);
	}
	public function setLicenseType( $type ) {
		FrameWtbp::_()->getModule('options')->getModel()->save('license_type', $type);
	}
	public function setEmail( $email ) {
		FrameWtbp::_()->getModule('options')->getModel()->save('license_email', base64_encode( $email ));
	}
	public function setLicenseKey( $key ) {
		FrameWtbp::_()->getModule('options')->getModel()->save('license_key', base64_encode( $key ));
	}
	public function setLicenseName( $name ) {
		FrameWtbp::_()->getModule('options')->getModel()->save('license_name', base64_encode( $name ));
	}
	public function getLicenseType( $full = true ) {
		$type = FrameWtbp::_()->getModule('options')->get('license_type');
		return false === $type ? '' : ( $full ? $type : substr($type, 0, 2) );
	}
	public function getEmail() {
		return base64_decode( FrameWtbp::_()->getModule('options')->get('license_email') );
	}
	public function getLicenseKey() {
		return base64_decode( FrameWtbp::_()->getModule('options')->get('license_key') );
	}
	public function getLicenseName() {
		return base64_decode( FrameWtbp::_()->getModule('options')->get('license_name') );
	}
	public function getCredentials() {
		return array(
			'type' => $this->getLicenseType(),
			'email' => $this->getEmail(),
			'key' => $this->getLicenseKey(),
			'name' => $this->getLicenseName(),
		);
	}
	private function _req( $action, $data = array() ) {
		$data = array_merge($data, array(
			'mod' => 'manager',
			'pl' => 'lms',
			'action' => $action,
		));
		$response = wp_remote_post($this->_apiUrl, array(
			'body' => $data,
			'timeout' => 30,
		));
		if (!is_wp_error($response)) {
			$resArr = UtilsWtbp::jsonDecode($response['body']);
			if ( isset($response['body']) && !empty($response['body']) && $resArr ) {
				if (!$resArr['error']) {
					return $resArr;
				} else {
					$this->pushError($resArr['errors']);
				}
			} else {
				$this->pushError(esc_html__('There was a problem with sending request to our autentification server. Please try latter.', 'woo-product-tables'));
			}
		} else {
			$this->pushError( $response->get_error_message() );
		}
		return false;
	}
	private function _initApiUrl() {
		if (empty($this->_apiUrl)) {
			// TODO: Replace this back to production
			$this->_apiUrl = 'https://woobewoo.com/';
		}
	}
	public function enbOptimization() {
		return false;
	}
	public function checkPreDeactivateNotify() {
		$daysLeft = (int) FrameWtbp::_()->getModule('options')->getModel()->get('license_days_left');
		if ($daysLeft > 0 && $daysLeft <= 3) {	// Notify before 3 days
			add_action('admin_notices', array($this, 'showPreDeactivationNotify'));
		}
	}
	public function showPreDeactivationNotify() {
		$daysLeft = (int) FrameWtbp::_()->getModule('options')->getModel()->get('license_days_left');
		$msg = '';
		if (0 == $daysLeft) {
			/* translators: %s: plugin name */
			$msg = esc_html(sprintf(__('License for plugin %s will expire today.', 'woo-product-tables'), WTBP_WP_PLUGIN_NAME));
		} elseif (1 == $daysLeft) {
			/* translators: %s: plugin name */
			$msg = esc_html(sprintf(__('License for plugin %s will expire tomorrow.', 'woo-product-tables'), WTBP_WP_PLUGIN_NAME));
		} else {
			/* translators: %1: plugin name 2: count days */
			$msg = esc_html(sprintf(__('License for plugin %1$s will expire in %2$d days.', 'woo-product-tables'), WTBP_WP_PLUGIN_NAME, $daysLeft));
		}
		echo '<div class="error">' . esc_html($msg) . '</div>';
	}
	public function updateDb() {
		if (!$this->enbOptimization()) {
			return;
		}
		$time = time();
		$lastCheck = (int) get_option('_last_wp_check_imp_' . WTBP_CODE);
		if (!$lastCheck || ( $time - $lastCheck ) >= 5 * 24 * 3600 /** 0/*remove last!!!*/) {
			$this->updateDbTables($this->isActive());
			update_option('_last_wp_check_imp_' . WTBP_CODE, $time);
		}
	}
	public function updateDbTables( $activate ) {
		$active = ( $activate ? 1 : 0 );
		if (function_exists('is_multisite') && is_multisite()) {
			global $wpdb;
			$blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blog_id as $id) {
				if (switch_to_blog($id)) {
					dbWtbp::query('UPDATE @__modules SET active = ' . $active . ' WHERE ex_plug_dir IS NOT NULL AND ex_plug_dir != "" AND code != "license"');
					restore_current_blog();
				}
			}
		} else {
			dbWtbp::query('UPDATE @__modules SET active = ' . $active . ' WHERE ex_plug_dir IS NOT NULL AND ex_plug_dir != "" AND code != "license"');
		}
	}
	private function _getPluginCode() {
		return 'woo_producttables_pro';
	}
	public function getExtendUrl() {
		$license = $this->getCredentials();
		$license['key'] = md5($license['key']);
		$license = urlencode(base64_encode(implode('|', $license)));
		return $this->_apiUrl . '?mod=manager&pl=lms&action=extend&plugin_code=' . $this->_getPluginCode() . '&lic=' . $license;
	}
}
