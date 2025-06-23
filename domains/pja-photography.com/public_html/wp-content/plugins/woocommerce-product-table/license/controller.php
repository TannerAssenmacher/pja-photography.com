<?php
class LicenseControllerWtbp extends ControllerWtbp {
	public function activate() {
		$res = new ResponseWtbp();
		if ($this->getModel()->activate(ReqWtbp::get('post'))) {
			$res->addMessage(esc_html__('Done', 'woo-product-tables'));
		} else {
			$res->pushError ($this->getModel()->getErrors());
		}
		$res->ajaxExec();
	}
	public function dismissNotice() {
		$res = new ResponseWtbp();
		FrameWtbp::_()->getModule('options')->getModel()->save('dismiss_pro_opt', 1);
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			WTBP_USERLEVELS => array(
				WTBP_ADMIN => array('activate', 'dismissNotice')
			),
		);
	}
}
