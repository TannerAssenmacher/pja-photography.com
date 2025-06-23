<?php
class UtilitiesControllerWtbp extends ControllerWtbp {

	public function convertAttribute() {
		$res     = new ResponseWtbp();
		$updated = $this->getModel()->convertAttribute(ReqWtbp::get('post'));
		if (false === $updated) {
			$res->pushError ($this->getModel()->getErrors());
		} else {
			$res->addMessage(esc_html( ( !empty($updated) ? __('Success!', 'woo-product-tables') . ' ' : '' ) . ( (int) $updated ) . ' ' . __('products were updated', 'woo-product-tables') ) );
		}
		$res->ajaxExec();
	}
	
	public function exportGroup() {
		$res  = new ResponseWtbp();
		$data = $this->getModel()->exportGroup(ReqWtbp::getVar('listIds', 'post'));
		if ($data) {
			$res->addData('tables', $data);
			$res->addMessage(esc_html__('Done', 'woo-product-tables'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$res->ajaxExec();
	}
	
	public function importGroup() {
		$res      = new ResponseWtbp();
		$tables   = ReqWtbp::getVar('import_file', 'files');
		$tables   = isset($tables['tmp_name']) ? file_get_contents($tables['tmp_name']) : '';
		$imported = false;
		if ($tables) {
			$imported = DbWtbp::query($tables);
		}
		if ($imported) {
			$res->addData('tables', $tables);
			$res->addMessage(esc_html__('Done', 'woo-product-tables'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		
		$res->ajaxExec(true);
	}
}
