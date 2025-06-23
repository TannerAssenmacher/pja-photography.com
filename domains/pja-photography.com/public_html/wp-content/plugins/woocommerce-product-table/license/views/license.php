<?php
class LicenseViewWtbp extends ViewWtbp {
	public function getTabContent() {
		FrameWtbp::_()->addScript('wtpb.admin.license', $this->getModule()->getModPath() . 'js/admin.license.js');
		FrameWtbp::_()->getModule('templates')->loadJqueryUi();
		$credentials = $this->getModel()->getCredentials();
		if (empty($credentials['key'])) {
			$credentials = array_merge($credentials, $this->getModule()->pluginData);
		}
		$this->assign('credentials', $credentials);
		$this->assign('licenseType', $this->getModule()->licenseType);
		$this->assign('isActive', $this->getModel()->isActive());
		$this->assign('isExpired', $this->getModel()->isExpired());
		$this->assign('extendUrl', $this->getModel()->getExtendUrl());
		return parent::getContent('licenseAdmin');
	}
}
