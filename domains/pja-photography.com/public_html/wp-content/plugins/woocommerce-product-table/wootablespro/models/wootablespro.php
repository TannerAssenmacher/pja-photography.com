<?php
class WootablesProModelWtbp extends ModelWtbp {
	public function __construct() {
		$this->_setTbl('tables');
	}

	public function addAutoProducts( $productId ) {
		$product = wc_get_product($productId);
		$categories = $product->get_category_ids();
		$variations = $product->get_children();
		if (!is_array($categories) && !is_array($variations)) {
			return;
		}
		
		$isCatList = false;
		$isVarList = false;
		$tables = $this->addWhere('auto_add=1')->getFromTbl();
		foreach ($tables as $table) {
			$settings = FrameWtbp::_()->getModule('wootablepress')->unserialize($table['setting_data']);
			if (!isset($settings['settings']) || !isset($settings['settings']['auto_categories_list']) || !isset($settings['settings']['auto_variations_list'])) {
				continue;
			}
			$list = null;
			if (isset($settings['settings']['auto_categories_list']) && !empty($settings['settings']['auto_categories_list'])) {
				$isCatList = true;
				$list = explode(',', $settings['settings']['auto_categories_list']);
			} elseif (isset($settings['settings']['auto_variations_list']) && !empty($settings['settings']['auto_variations_list'])) {
				$isVarList = true;
				$list = explode(',', $settings['settings']['auto_variations_list']);
			}
			if (is_array($list)) {
				$add = false;
				if ($isCatList && 'all' == $list[0]) {
					$add = true;
				} elseif ($isCatList) {
					foreach ($categories as $category) {
						if (in_array($category, $list)) {
							$add = true;
							break;
						}
					}
				} elseif ($isVarList) {
					$add = true;
				}
				$products = isset( $settings['settings']['productids'] ) ? explode( ',', $settings['settings']['productids'] ) : array();
				if ( $add || in_array( $productId, $products ) ) {
					if ( $add ) {
						if ( $isCatList && ! in_array( $productId, $products ) ) {
							$products[] = $productId;
						} elseif ( $isVarList ) {
							$products = array_merge( $products, $variations );
						}
					} else {
						$products = array_diff( $products, array_merge( array( $productId ), $variations ) );
					}
					$settings['settings']['productids'] = implode( ',', $products );
					$this->updateById( array( 'setting_data' => base64_encode( serialize( $settings ) ) ), $table['id'] );
				}
			}
		}
		return;
	}
}
