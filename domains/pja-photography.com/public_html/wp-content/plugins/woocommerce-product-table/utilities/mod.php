<?php
class UtilitiesWtbp extends ModuleWtbp {
	public function init() {
		parent::init();
		DispatcherWtbp::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		DispatcherWtbp::addFilter('addAdminButtons', array($this, 'addAdminButtons'), 10);
		add_action('wp_ajax_importGroup', array($this, 'importGroup'));
		add_action('wp_ajax_nopriv_importGroup', array($this, 'importGroup'));
		if (is_admin()) {
			add_action('admin_footer', array($this, 'appendImportDialog'));
		}
	}
	public function addAdminTab( $tabs ) {
		$tabs[$this->getCode()] = array(
			'label' => esc_html__('Converter', 'woo-product-tables'),
			'callback' => array($this, 'getTabContent'),
			'fa_icon' => 'fa-wrench',
			'sort_order' => 900,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	
	public function addAdminButtons() {
		$this->getView()->showAdminImortExportButtons();
	}
	
	public function appendImportDialog() {
		$regTab = FrameWtbp::_()->getModule('options')->getActiveTab();
		if ('wootablepress' == $regTab) {
			HtmlWtbp::echoEscapedHtml($this->getView()->getImportDialog());
		}
	}
	
	public function importGroup() {
		$this->getController()->importGroup();
	}
}
