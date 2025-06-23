<?php
class UtilitiesModelWtbp extends ModelWtbp {
	private $metaKeyAttributes = '_product_attributes';

	public function getCustomAttributes( $list = true ) {
		$args       = array(
			'post_type' => 'product', 
			'fields' => 'ids', 
			'ignore_sticky_posts' => true,
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'meta_key' => $this->metaKeyAttributes
		);
		$post_ids   = get_posts($args);
		$attributes = array();		
		foreach ($post_ids as $post_id) {		
			$meta = get_post_meta($post_id, $this->metaKeyAttributes, true);
			foreach ($meta as $key => $terms) {
				if (empty($terms['is_taxonomy'])) {
					$values = explode('|', $terms['value']);
					$name   = trim($terms['name']);
					if (isset($attributes[$name])) {
						$attributes[$name] = array_merge($attributes[$name], $values);
					} else {
						$attributes[$name] = $values;
					}
				}
			}
		}
		foreach ($attributes as $name => $values) {
			$values            = array_unique(array_map('trim', $values));
			$attributes[$name] = ( $list ? $name . ' (' . implode('|', $values) . ')' : $values );
		}
		return $attributes;
	}

	public function convertAttribute( $params ) {
		if (empty($params['custom_attribute'])) {
			$this->pushError(esc_html__('Select custom product attribute', 'woo-product-tables'));
			return false;
		}
		if (empty($params['attribute_name'])) {
			$this->pushError(esc_html__('Define taxonomy attribute name', 'woo-product-tables'));
			return false;
		}
		if (empty($params['attribute_slug'])) {
			$this->pushError(esc_html__('Define taxonomy attribute slug', 'woo-product-tables'));
			return false;
		}
		$custom = $params['custom_attribute'];
		$label  = $params['attribute_name'];
		$slug   = sanitize_title($params['attribute_slug']);
		if (empty($slug)) {
			$this->pushError(esc_html__('Enter correct slug', 'woo-product-tables'));
			return false;
		}
		$slug = substr($slug, 0, 28);

		$metaKey    = $this->metaKeyAttributes;
		$attributes = wc_get_attribute_taxonomies();
		$isExists   = false;
		foreach ($attributes as $atts) {
			if ($atts->attribute_name == $slug) {
				$isExists = true;
			}
		}
		$name = wc_attribute_taxonomy_name($slug);

		$args    = array(
			'post_type' => 'product', 
			'fields' => 'ids', 
			'ignore_sticky_posts' => true,
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'meta_key' => $metaKey
		);
		$postIds = get_posts($args);
		$updated = 0;
		foreach ($postIds as $postId) {		
			$meta       = get_post_meta($postId, $metaKey, true);
			$needUpdate	= false;
			foreach ($meta as $key => $terms) {
				if ($custom === $terms['name'] && empty($terms['is_taxonomy'])) {
					$needUpdate   = true;
					$values       = explode('|', $terms['value']);
					$productTerms = array();

					foreach ($values as $term ) {
						$productTerms[] = $term;
					}

					unset($meta[$key]);		
					$meta["{$name}"] = array( 
						'name' 		=> $name,
						'value' 	=> '',
						'position' 	=> $terms['position'], 
						'is_visible' 	=> $terms['is_visible'], 
						'is_variation' 	=> $terms['is_variation'], 
						'is_taxonomy' 	=> 1, 
					);	
				}
			}

			if ($needUpdate) {
				if (!$isExists) {
					$taxonomyId = wc_create_attribute(array('name' => $label, 'slug' => $slug));
					if (is_wp_error($taxonomyId)) {
						$this->pushError(esc_html(__('There was an error somewhere and the taxonomy couldn\'t be create', 'woo-product-tables')));
					} else {
						$isExists = true;
						register_taxonomy($name, array('product'));
					}
				}
				$termTaxonomyIds = wp_set_object_terms($postId, $productTerms, $name, false);
				if (is_wp_error($termTaxonomyIds)) {
					$this->pushError(esc_html(__('There was an error somewhere and the terms couldn\'t be set for postId: ', 'woo-product-tables') . $postId));
				} else {
					update_post_meta($postId, $metaKey, $meta);
					$updated++;			
				}

				$_product = wc_get_product($postId);
				if ($_product->is_type('variable')) {
					$varMeta = 'attribute_' . $slug;
					$varAttr = 'attribute_' . $name;
					foreach ($_product->get_available_variations() as $variation) {
						$varId       = $variation['variation_id'];
						$customValue = get_post_meta($varId, $varMeta, true);
						if (!empty($customValue) && in_array($customValue, $productTerms)) {
							delete_post_meta($varId, $varMeta);
							$attrValue = get_post_meta($varId, $varAttr, true);
							if (empty($attrValue)) {
								update_post_meta($varId, $varAttr, $customValue);
							}
						}
					}
				}
				
			}
		}	
		return $updated;
	}
	
	public function exportGroup( $ids ) {
		if (!is_array($ids)) {
			$ids = array($ids);
		}
		$ids = array_filter(array_map('intval', $ids));
		if (!empty($ids)) {
			if (ob_get_contents()) {
				ob_end_clean();
			}
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="wtbp_export.sql"');
			if (ob_get_contents()) {
				ob_end_clean();
			}
			$delim    = '/*----------------------------------*/';
			$delimEOL = PHP_EOL . $delim . PHP_EOL;
			
			$tablesData = FrameWtbp::_()->getModule('wootablepress')->getModel()->setWhere(array('additionalCondition' => 'id IN (' . implode(',', $ids) . ')'))->getFromTbl();
			if (!empty($tablesData)) {
				$sqlString = 'INSERT INTO `@__tables` ';
				$countData = count($tablesData) - 1;
				foreach ($tablesData as $key => $tableData) {
					$columns = array_keys($tableData);
					if (0 == $key) {
						$sqlString .= '(' . implode(',', $columns) . ') VALUES' . PHP_EOL;
					}
					unset($tableData['id']);
					$sqlString .= "(NULL,'" . implode("','", $tableData) . "')";
					if ($key < $countData) {
						$sqlString .= ',' . PHP_EOL;
					} else {
						$sqlString .= ';' . $delimEOL;
					}
				}
			}
			return $sqlString;
		} else {
			$this->pushError(esc_html__('Invalid ID', 'woo-product-tables'));
		}
		return false;
	}
}
