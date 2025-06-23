<?php
class UtilitiesViewWtbp extends ViewWtbp {
	public function getTabContent() {
		FrameWtbp::_()->addScript('wtpb.admin.utilities', $this->getModule()->getModPath() . 'js/admin.utilities.js');
		FrameWtbp::_()->getModule('templates')->loadJqueryUi();

		$customAttributes  = $this->getModel()->getCustomAttributes();
		$definedAttributes = array();

		$this->assign('customAttributes', $customAttributes);
		$this->assign('definedAttributes', $definedAttributes);
		return parent::getContent('utilitiesAdmin');
	}
	
	public function getImportDialog() {
		return parent::getContent('importDialog');
	}
	
	public function showAdminImortExportButtons() {
		parent::display('partAdminButtonsPro');
	}
}
