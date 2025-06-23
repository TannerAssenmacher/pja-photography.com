<?php
class WootablesProControllerWtbp extends ControllerWtbp {

	public function getProductPage() {
		$res = new ResponseWtbp();
		$params = ReqWtbp::get('post');

		$result = FrameWtbp::_()->getModule('wootablepress')->getView()->getProductPage($params);

		if (!empty($result)) {
			$res->addMessage(esc_html__('Done', 'woo-product-tables'));
			$res->setHtml($result['html']);
			$res->recordsFiltered = $result['total'];
			$res->recordsTotal = $result['total'];
			if (isset($params['draw'])) {
				$res->draw = $params['draw'];
			}
		} else {
			$res->addMessage(esc_html__('Products not exist!', 'woo-product-tables'));
		}
		return $res->ajaxExec();
	}
}
