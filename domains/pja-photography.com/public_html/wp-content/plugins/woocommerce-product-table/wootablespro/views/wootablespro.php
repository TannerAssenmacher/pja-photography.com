<?php
class WootablesProViewWtbp extends ViewWtbp {
	protected $taxonomies = array();
	protected $addToCartText = null;

	public function filterProductIds( $products, $params ) {
		$categoryIds = !empty($params['autoCategories']) ? $params['autoCategories'] : array();
		$variableIds = !empty($params['autoVariables']) ? $params['autoVariables'] : array();
		if (count($categoryIds) > 0 || count($variableIds) > 0) {
			$args = array(
				'post_type' => 'product',
				'ignore_sticky_posts' => true,
				'post_status' => array('publish'),
				'posts_per_page' => -1
			);
			if (count($categoryIds) > 0 && 'all' != $categoryIds[0]) {
				$args['tax_query'] = array(array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => $categoryIds,
					'include_children' => false,
					'operator' => 'IN'
				));
			}
			if (count($variableIds) > 0) {
				$args['post_type'] = 'product_variation';
				if ('all' != $variableIds[0]) {
					$args['post_parent__in'] = $variableIds;
				}
			}
			$postExist = new WP_Query($args);

			foreach ($postExist->posts as $post) {
				$products[] = $post->ID;
			}
			$products = array_unique($products);
		}
		return $products;
	}

	public function dynamicProductsFiltering( $productIds, $settings ) {
		if ( ! $this->getTableSetting( $settings, 'filter_dynamically', false ) ) {
			return $productIds;
		}

		$ajax = false;
		$action = ReqWtbp::getVar( 'action', 'get' );
		if ( 'getProductPage' === $action ) {
			$productId = ReqWtbp::getVar( 'product_id', 'post' );
			if (is_numeric($productId)) {
				$ajax = true;
			}
		}

		if ( is_product_category() || is_product_tag() || is_tax() ) {
			$pageObj = get_queried_object();
		} elseif ( is_product() || $ajax) {
			if ( $ajax ) {
				$_product = wc_get_product( $productId );
			} else {
				$_post    = get_queried_object();
				$_product = wc_get_product( $_post->ID );
			}

			$productType = $_product->get_type();
			if ( 'variable' === $productType || 'variable-subscription' === $productType ) {
				$varIds = array();
				foreach ( $_product->get_available_variations() as $variation ) {
					$varIds[] = $variation['variation_id'];
				}
				if ( empty( $varIds ) ) {
					return array( 'in' => array( 0 ), 'not' => false );
				}
				if ( is_array( $productIds['in'] ) ) {
					return array( 'in' => array_intersect( $productIds['in'], $varIds ), 'not' => false );
				} elseif ( is_array( $productIds['not'] ) ) {
					return array( 'in' => array_diff( $varIds, $productIds['not'] ), 'not' => false );
				} elseif ( $this->getTableSetting( $settings, 'auto_variations_enable', false ) && 'all' === $this->getTableSetting( $settings, 'auto_variations_list', '' ) ) {
					return array( 'in' => $varIds, 'not' => false );
				}
			}

			return array( 'in' => array( 0 ), 'not' => false );
		} else {
			$url   = ReqWtbp::getVar( 'HTTP_REFERER', 'server' );
			$parts = explode( '/', wp_parse_url( $url, PHP_URL_PATH ) );
			$slug  = end( $parts );
			if ( '' === $slug ) {
				$slug = prev( $parts );
			}
			$pageObj = get_term_by( 'slug', $slug, 'product_cat' );
		}

		if ( $pageObj instanceof WP_Term ) {
			$args = array(
				'fields'         => 'ids',
				'posts_per_page' => - 1,
				'post_type'      => array( 'product' ),
				'tax_query'      => array()
			);

			$args['tax_query'][] = array(
				'taxonomy'         => $pageObj->taxonomy,
				'field'            => 'id',
				'terms'            => $pageObj->term_id,
				'include_children' => true
			);

			if ( is_array( $productIds['in'] ) ) {
				$args['post__in'] = $productIds['in'];
			} elseif ( is_array( $productIds['not'] ) ) {
				$args['post__not_in'] = $productIds['not'];
			} elseif ( ! $this->getTableSetting( $settings, 'auto_categories_enable', false ) || 'all' !== $this->getTableSetting( $settings, 'auto_categories_list', '' ) ) {
				return array( 'in' => array( 0 ), 'not' => false );
			}
			$dataExist = new WP_Query( $args );
			$posts     = $dataExist->posts;

			return array( 'in' => ( empty( $posts ) ? array( 0 ) : $posts ), 'not' => false );
		}

		return $productIds;
	}

	public function getLoaderHtml( $html, $settings ) {
		$loaderName = $this->getTableSetting($settings, 'table_loader_icon_name', 'default');
		if ('default' != $loaderName) {
			$loaderColor = esc_attr($this->getTableSetting($settings, 'table_loader_icon_color', 'black'));
			$css = '<style type="text/css">.woobewoo-table-loader{';

			if ('spinner' === $loaderName) {
				$html = '<div class="woobewoo-table-loader spinner"></div>';
				$css .= 'background-color:' . $loaderColor;
			} else {
				$loaderNumber = $this->getTableSetting($settings, 'table_loader_icon_number', '0');
				$html = '<div class="woobewoo-table-loader la-' . esc_attr($loaderName) . ' la-2x">';
				$css .= 'color:' . $loaderColor;
				for ($i = 0; $i < $loaderNumber; $i++) {
					$html .= '<div></div>';
				}
				$html .= '</div>';
			}
			$html = $css . ' !important;}</style>' . $html;
		}
		return $html;
	}

	public function getFilterHtml( $id, $settings = false ) {
		if (!$id) {
			return false;
		}

		$module = FrameWtbp::_()->getModule('wootablepress');
		$orders = $module->getView()->orderColumns;

		$catHtml = '';
		$attrHtml = '';
		if (false == $settings) {
			$table = $module->getModel('wootablepress')->getById($id);
			$tableSettings = FrameWtbp::_()->getModule('wootablepress')->unserialize($table['setting_data']);
		} else {
			$tableSettings = $settings;
		}

		$settings = $this->getTableSetting($tableSettings, 'settings', array());

		$filterEnable = array();
		// filter_category - must be first!
		$filters = array('filter_category', 'filter_attribute', 'filter_tag', 'filter_ctax', 'filter_price');
		foreach ($filters as $filter) {
			if ($this->getTableSetting($settings, $filter, false)) {
				$filterEnable[$filter] = true;
			}
		}

		if (count($filterEnable) == 0) {
			return false;
		}

		$productIds = $this->getTableSetting($settings, 'productids', '');
		if (!empty($productIds) && !is_array($productIds)) {
			$productIds = explode(',', $productIds);
		}

		$postStatuses = array('publish');
		if ($this->getTableSetting($settings, 'show_private', false)) {
			$postStatuses[] = 'private';
		}

		$prArgs = array(
			'post__in' => $productIds,
			'post_type' => array('product', 'product_variation'),
			'ignore_sticky_posts' => true,
			'post_status' => $postStatuses,
			'posts_per_page' => -1,
			'fields' => 'id=>parent'
		);
		$dataExist = new WP_Query($prArgs);
		$postExist = $dataExist->posts;

		if (!$postExist) {
			return false;
		}

		$productIds = array();
		foreach ($postExist as $key => $post) {
			if (is_object($post)) {
				$productIds[] = empty($post->post_parent) ? $post->ID : $post->post_parent;
			} else {
				$productIds[] = empty($post) ? $key : $post;
			}
		}
		$productIds = array_unique($productIds);

		$categoryMain = isset($filterEnable['filter_category']) && count($filterEnable) > 1 && $this->getTableSetting($settings, 'filter_category_relations', false);
		$relations = array();

		foreach ($filterEnable as $filter => $enable) {
			switch ($filter) {
				case 'filter_category':
					if ($this->getTableSetting($settings, 'filter_category_hide', false)) {
						$list = 'categories';
					} else {
						$list = '';
						foreach ($orders as $column) {
							if ('categories' == $column['slug']) {
								$list = $column['slug'];
								break;
							}
						}
					}
					if (empty($list)) {
						unset($filterEnable[$filter]);
					} else {
						$isUseCategories = $this->getTableSetting($settings, 'filter_category_use_selected', false);
						$args = array(
							'taxonomy' => 'product_cat',
							'hide_empty' => true,
							'object_ids' => $productIds,
							'fields' => 'ids'
						);

						if ( $isUseCategories ) {
							$filterCategorySelected = $this->getTableSetting( $settings, 'filter_category_selected', '' );
							$args['include']        = $filterCategorySelected;
						}

						$neededCategories = get_terms('product_cat', $args);
						
						$args = array(
							'hide_empty' => false,
							'orderby' => 'name',
							'order' => 'asc',
						);

						if ( $isUseCategories && count( $filterCategorySelected ) === 1 ) {
							$args['parent'] = $filterCategorySelected[0];
						}

						$categoryArray = $this->getTaxonomyHierarchy('product_cat', $args, $neededCategories);

						if (!empty($categoryArray)) {
							if ($isUseCategories) {
								$categoryOrder = $this->getTableSetting($settings, 'filter_category_selected_order', '');
								if (!empty($categoryOrder)) {
									$categoryOrder = explode(',', $categoryOrder);
									$categoryOrder = array_intersect($categoryOrder, $this->getTableSetting($settings, 'filter_category_selected', array()));
									$categoryArray = array_replace(array_flip($categoryOrder), $categoryArray);
									$categoryArray = array_filter($categoryArray, function( $item ) {
										return is_object($item);
									});
								}
							}
							$wrapperStart = '<select data-type="categories" data-ids="1" data-column-keys="' . esc_attr($list) . '" data-tax-key="product_cat" data-children="' . esc_attr($this->getTableSetting($settings, 'filter_category_children', 0)) . '"';

							$filterCategoryTitle = $this->getTableSetting($settings, 'filter_category_title', '');
							if ( ! $filterCategoryTitle ) {
								$filterCategoryTitle = esc_attr__('Category', 'woo-product-tables');
							}
							if ($this->getTableSetting($settings, 'filter_category_type', 'dropdown') == 'multi') {
								$wrapperStart .= ' multiple="multiple" data-placeholder="' . $filterCategoryTitle . '" data-logic="' . esc_attr($this->getTableSetting($settings, 'filter_category_logic', 'or')) . '"';
								$catHtml = '';
							} else {
								$catHtml = '<option value="">' . $filterCategoryTitle . '</option>';
							}
							$wrapperStart .= '>';
							$wrapperEnd = '</select>';

							$list = array();
							$catHtml .= $this->generateTaxonomyOptionsHtml($categoryArray, '', $list, 2);

							$isCategoryInnerFilter = $this->getTableSetting($settings, 'filter_category', false) && $this->getTableSetting($settings, 'filter_category_inner_table', false);
							if ($isCategoryInnerFilter) {
								$catHtml .= '<option class="wtbpVisibilityHidden wtbpInnerFilter" value="0"></option>';
							}

							$catHtml =  $wrapperStart . $catHtml . $wrapperEnd;
							if ($categoryMain) {
								$productCatIds = array();
								$prArgs['fields'] = 'ids';
								$prArgs['tax_query'][] = array(
									'taxonomy' => 'product_cat',
									'field' => 'id',
									'operator' => 'IN',
									'include_children' => true
								);
								foreach ($list as $catId => $n) {
									$relations[$catId] = array();
									$children = get_term_children($catId, 'product_cat');
									if (is_array($children)) {
										array_push($children, $catId);
									} else {
										$children = array($catId);
									}
									$prArgs['tax_query'][0]['terms'] = $children;

									$dataExist = new WP_Query($prArgs);
									if ($dataExist->posts) {
										$productCatIds[$catId] = $dataExist->posts;
									}
								}
							}
						}
						wp_reset_postdata();
					}
					break;

				case 'filter_attribute':
					$attributeIds = $this->getTableSetting($settings, 'filter_attribute_selected', array());
					if (!is_array($attributeIds)) {
						$attributeIds = (array) $attributeIds;
					}
					$attributeOrder = $this->getTableSetting($settings, 'filter_attribute_selected_order', '');
					if (!empty($attributeOrder)) {
						$attributeOrder = explode(',', $attributeOrder);
						$attributeIds = array_intersect($attributeOrder, $attributeIds);
					}
					$attributeHideByFilterEnabled  = $this->getTableSetting($settings, 'filter_attribute_hide', false);
					$attributeOrderByCustom = $this->getTableSetting($settings, 'filter_attribute_order_custom', false);
					$isMulti  = $this->getTableSetting($settings, 'filter_attribute_type', 'dropdown') == 'multi';
					$selectAttrs = $isMulti ?  'data-logic="' . esc_attr($this->getTableSetting($settings, 'filter_attribute_logic', 'or')) . '"' : 'data-specific="' . esc_attr($this->getTableSetting($settings, 'filter_attribute_specific', '0')) . '"';
					$filterEnable[$filter] = false;
					$allAttributes = wc_get_attribute_taxonomies();

					foreach ($attributeIds as $attributeId) {

						$list = array();
						if ($attributeHideByFilterEnabled) {
							$list['attribute-' . $attributeId] = 0;
						}

						$slugs = array('attribute', 'attribute-' . $attributeId);
						
						foreach ($orders as $column) {
							if (in_array($column['slug'], $slugs)) {
								$list[$column['slug']] = 1;
							}
						}

						if (count($list) > 0) {
							$filterEnable[$filter] = true;
							$attributeLabel = '';
							$attributeTaxonomy = '';

							foreach ($allAttributes as $attribute) {
								if ($attribute->attribute_id === $attributeId) {
									$attributeLabel = $attribute->attribute_label;
									$attributeLabel = preg_replace('/{:.*?}/', '', $attributeLabel);
									$attributeTaxonomy = 'pa_' . $attribute->attribute_name;
									break;
								}
							}
							$args = array(
								'taxonomy' => $attributeTaxonomy,
								'hide_empty' => true,
								'object_ids' => $productIds
							);
							$allValues = get_terms($attributeTaxonomy, $args); 

							if (!$attributeOrderByCustom) {
								usort($allValues, array($this, 'cmp'));
							}

							if (!empty($allValues)) {
								$filterAttributeTitle = $this->getTableSetting($settings, 'filter_attribute_title', '');
								if (!$filterAttributeTitle) {
									$filterAttributeTitle = esc_attr($attributeLabel);
								}
								$attrHtml .= '<select data-type="attribute" data-column-keys="' . esc_attr(implode(',', array_keys($list))) . '" data-tax-key="' . esc_attr($attributeTaxonomy) . '" ' . $selectAttrs;
								if ($isMulti) {
									$attrHtml .= ' multiple="multiple" data-placeholder="' . $filterAttributeTitle . '"';
								}

								$attrHtml .= '>' . 
									( $isMulti ? '' : '<option value="">' . $filterAttributeTitle . '</option>' ) . 
									$this->generateTaxonomyOptionsHtml($allValues) .
									'</select>';
								if (!empty($relations)) {
									foreach ($relations as $catId => $d) {
										if (empty($productCatIds[$catId])) {
											continue;
										}
										$args['object_ids'] = $productCatIds[$catId];
										$terms = get_terms($attributeTaxonomy, $args);
										$values = array();
										foreach ($terms as $term) {
											$values[] = $term->term_taxonomy_id;
										}
										$relations[$catId][$attributeTaxonomy] = $values;
									}
								}
							}
						}
					}
					break;

				case 'filter_tag':
					$isUseTags = $this->getTableSetting($settings, 'filter_tag_use_selected', false);
					if ($this->getTableSetting($settings, 'filter_tag_hide', false)) {
						$list = 'tags';
					} else {
						$list = '';
						foreach ($orders as $column) {
							if ('tags' == $column['slug']) {
								$list = $column['slug'];
								break;
							}
						}
					}
					if (empty($list)) {
						unset($filterEnable[$filter]);
					} else {
						$args = array(
							'taxonomy' => 'product_tag',
							'hide_empty' => true,
							'object_ids' => $productIds,
							'fields' => 'ids'
						);
						if ($isUseTags) {
							$args['include'] = $this->getTableSetting($settings, 'filter_tag_selected', '');
						}
						$neededTags = get_terms('product_tag', $args);

						$args = array(
							'hide_empty' => false,
							'orderby' => 'name',
							'order' => 'asc',
						);

						$tagArray = $this->getTaxonomyHierarchy('product_tag', $args, $neededTags);
						if (!empty($tagArray)) {
							if ($isUseTags) {
								$tagOrder = $this->getTableSetting($settings, 'filter_tag_selected_order', '');
								if (!empty($tagOrder)) {
									$tagOrder = explode(',', $tagOrder);
									$tagOrder = array_intersect($tagOrder, $this->getTableSetting($settings, 'filter_tag_selected', array()));
									$tagArray = array_replace(array_flip($tagOrder), $tagArray);
									$tagArray = array_filter($tagArray, function( $item ) {
										return is_object($item);
									});
								}
							}
							$filterTagTitle = $this->getTableSetting($settings, 'filter_tag_title', '');
							if ( ! $filterTagTitle ) {
								$filterTagTitle = esc_attr__('Tag', 'woo-product-tables');
							}
							$attrHtml .= '<select data-type="tags" data-column-keys="' . esc_attr($list) . '" data-tax-key="product_tag"';

							if ($this->getTableSetting($settings, 'filter_tag_type', 'dropdown') == 'multi') {
								$attrHtml .=
									' multiple="multiple" data-placeholder="' . $filterTagTitle . 
									'" data-logic="' . esc_attr($this->getTableSetting($settings, 'filter_tag_logic', 'or')) . '"';
								$tagHtml = '';
							} else {
								$tagHtml = '<option value="">' . $filterTagTitle . '</option>';
							}

							$attrHtml .= '>' . $tagHtml . $this->generateTaxonomyOptionsHtml($tagArray) . '</select>';

							if (!empty($relations)) {
								foreach ($relations as $catId => $d) {
									if (empty($productCatIds[$catId])) {
										continue;
									}
									$args['object_ids'] = $productCatIds[$catId];
									$terms = get_terms('product_tag', $args);
									$values = array();
									foreach ($terms as $term) {
										$values[] = $term->term_taxonomy_id;
									}
									$relations[$catId]['product_tag'] = $values;
								}
							}
						}
					}
					break;

				case 'filter_ctax':
					$cTaxSlugs = $this->getTableSetting($settings, 'filter_ctax_selected', array());
					$cTaxHideByFilterEnabled  = $this->getTableSetting($settings, 'filter_ctax_hide', false);
					$isMulti = $this->getTableSetting($settings, 'filter_ctax_type', 'dropdown') == 'multi';
					$logic = $this->getTableSetting($settings, 'filter_ctax_logic', 'or');
					$filterEnable[$filter] = false;
					$module = $this->getModule();
					$allCtax = $module->addFullColumnList(array(), true);
					$isSsp = $this->getTableSetting($settings, 'pagination', false) && $this->getTableSetting($settings, 'pagination_ssp', false);

					$ctaxPrefix = $module->ctax_prefix . '-';
					$acfPrefix = $module->acf_prefix . '-';
					foreach ($cTaxSlugs as $slug) {
						if (!isset($allCtax[$slug])) {
							continue;
						}

						$filterSlug = '';
						foreach ($orders as $column) {
							if ($column['slug'] == $slug) {
								$filterSlug = $column['sub_slug'];
								break;
							}
						}

						$isCtax = strpos($slug, $ctaxPrefix) === 0;
						$curPrefix = $isCtax ? $module->ctax_prefix : $module->acf_prefix;
						if (empty($filterSlug) && $cTaxHideByFilterEnabled) {

							$filterSlug = str_replace($curPrefix . '-', '', $slug);
							$column = array(
								'slug' => $slug,
								'main_slug' => $curPrefix,
								'sub_slug' => $filterSlug
							);
						}

						if (!empty($filterSlug)) {
							$filterEnable[$filter] = true;

							if ($isCtax) {
								$args = array(
									'taxonomy' => $filterSlug,
									'hide_empty' => true,
									'object_ids' => $productIds
								);
								$neededTaxs = get_terms($filterSlug, $args);
							} else {
								$neededMeta = array();
								foreach ($productIds as $productId) {
									$acf =
										FrameWtbp::_()
											->getModule('wootablespro')
											->getModel('acf')
											->initGetAcf($filterSlug, $productId, $column);

									$acfValueList = $acf->getAcfFilterView();

									if (!empty($acfValueList)) {
										foreach ($acfValueList as $acfValue) {
											$neededMeta[$acfValue][] = $productId;
										}
									}
								}
							}
							if ($isCtax && !empty($neededTaxs)) {
								$filterCtaxTitle = $this->getTableSetting($settings, 'filter_ctax_title', '');
								if (!$filterCtaxTitle) {
									$filterCtaxTitle = esc_attr($allCtax[$slug]);
								}

								$queryType = 'taxonomy';

								$attrHtml .= 
									'<select data-type="' . $curPrefix .
										'" data-query-type="' . $queryType .
										'" data-column-keys="' . esc_attr($column['slug']) .
										'" data-tax-key="' . esc_attr($filterSlug) . '"';

								if ($isMulti) {
									$attrHtml .= ' multiple="multiple" data-placeholder="' . $filterCtaxTitle . '" data-logic="' . esc_attr($logic) . '">';
								} else {
									$attrHtml .= '><option value="">' . $filterCtaxTitle . '</option>';
								}
								if ($isCtax) {
									$attrHtml .= $this->generateTaxonomyOptionsHtml($neededTaxs);
								}
								$attrHtml .= '</select>';
							}
							if (!empty($neededMeta)) {
								ksort( $neededMeta, SORT_NATURAL );
								$filterCtaxTitle = $this->getTableSetting($settings, 'filter_ctax_title', '');
								if (!$filterCtaxTitle) {
									$filterCtaxTitle = esc_attr($allCtax[$slug]);
								}

								$metaQueryTypes = $this->getModule()->getFilterMetaQueryTypes();
								if ( in_array($curPrefix, $metaQueryTypes) ) {
									$queryType = 'meta';
								}

								$attrHtml .= 
									'<select data-type="' . $curPrefix .
										'" data-query-type="' . $queryType .
										'" data-column-keys="' . esc_attr($column['slug']) .
										'" data-meta-key="' . $filterCtaxTitle .
										'" data-tax-key="' . esc_attr($filterSlug) . '"';

								if ($isMulti) {
									$attrHtml .= ' multiple="multiple" data-placeholder="' . $filterCtaxTitle . '" data-logic="' . esc_attr($logic) . '">';
								} else {
									$attrHtml .= '><option value="">' . $filterCtaxTitle . '</option>';
								}

								foreach ($neededMeta as $acfValue => $productIdList) {
									if (!empty($acfValue)) {
										$dataProductIdList = '';
										if ($isSsp) {
											$productIdList = implode(', ', $productIdList);
											$dataProductIdList = ' data-product-id-list="' . $productIdList . '" ';
										}
										$attrHtml .= 
											'<option ' .
												'value="' . esc_attr($acfValue) . '"' .
												$dataProductIdList .
											'>' .
												wp_trim_words( esc_attr($acfValue), 10 ) .
											'</option>';
									}
								}
								$attrHtml .= '</select>';
							}
							wp_reset_postdata();
						}
					}
					break;

				case 'filter_price':
					if ($this->getTableSetting($settings, 'filter_price_hide', false)) {
						$list = 'price';
					} else {
						$list = '';
						foreach ($orders as $column) {
							if ('price' == $column['slug']) {
								$list = $column['slug'];
								break;
							}
						}
					}
					if (empty($list)) {
						unset($filterEnable[$filter]);
					} else {
						$setType = $this->getTableSetting($settings, 'filter_range_type', 'auto');
						if ('manual' == $setType) {
							$rangeList = array_chunk(explode(',', $this->getTableSetting($settings, 'filter_range_list', '')), 2);
						} else {
							$minPrice = $this->getTableSetting($settings, 'filter_range_min', false, true, false, true);
							$maxPrice = $this->getTableSetting($settings, 'filter_range_max', false, true, false, true);
							if (false === $minPrice || false === $maxPrice) {
								global $wpdb;
								$sql = "SELECT min( FLOOR( price_meta.meta_value ) ) as wtbpMinPrice, max( CEILING( price_meta.meta_value ) ) as wtbpMaxPrice FROM {$wpdb->posts} " .
									" LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " .
									" WHERE {$wpdb->posts}.post_type IN ('" . implode("','", array_map('esc_sql', apply_filters('woocommerce_price_filter_post_type', array('product', 'product_variation')))) . "')" .
									" AND {$wpdb->posts}.post_status IN ('" . implode("','", $postStatuses) . "') " .
									" AND price_meta.meta_key IN ('" . implode("','", array_map('esc_sql', apply_filters('woocommerce_price_filter_meta_keys', array('_price')))) . "')" .
									" AND price_meta.meta_value > '' AND {$wpdb->posts}.ID IN ('" . implode("','", $productIds) . "')";
								$wpdb->wtbp_prepared_query = $sql;
								$price = $wpdb->get_row($wpdb->wtbp_prepared_query);
								$minPrice = $price->wtbpMinPrice;
								$maxPrice = $price->wtbpMaxPrice;
							}
							if ('0' === $minPrice) {
								$minPrice = '0.01';
							}
							$step = $this->getTableSetting($settings, 'filter_range_step', 20, true);

							$priceRange = $maxPrice - $minPrice;
							$countElements = ceil($priceRange / $step);
							if ($countElements > 100) {
								$step = ceil($priceRange / 1000) * 10;
								$countElements = ceil($priceRange / $step);
							}

							$rangeList = array();
							$priceTempOld = 0;
							for ($i = 0; $i < $countElements; $i++) {
								if (0 === $i) {
									$priceTemp = $minPrice + $step;
									$rangeList[$i] = array($minPrice, $priceTemp - 0.01);
									$priceTempOld = $priceTemp;
								} else if ( ( $priceTempOld + $step ) < $maxPrice ) {
									$priceTemp = $priceTempOld + $step;
									$rangeList[$i] = array($priceTempOld, $priceTemp - 0.01);
									$priceTempOld = $priceTemp;
								} else {
									$rangeList[$i] = array($priceTempOld, $maxPrice);
								}
							}
						}

						if (!empty($rangeList)) {
							$attrHtml .= '<select data-type="price" data-column-keys="' . esc_attr($list) . '" data-tax-key="_price"';
							$filterPriceTitle = $this->getTableSetting($settings, 'filter_price_title', '');
							if ( ! $filterPriceTitle ) {
								$filterPriceTitle = esc_attr__('Price', 'woo-product-tables');
							}
							if ($this->getTableSetting($settings, 'filter_price_type', 'dropdown') == 'multi') {
								$attrHtml .= ' multiple="multiple" data-placeholder="' . $filterPriceTitle . '" data-logic="or"';
								$priceHtml = '';
							} else {
								$priceHtml = '<option value="">' . $filterPriceTitle . '</option>';
							}
							$attrHtml .= '>';
							foreach ($rangeList as $range) {
								if (isset($range['1']) && isset($range['0'])) {
									$priceHtml .= '<option value="' . esc_attr($range[0] . ',' . $range[1]) . '">' . wc_price($range[0]) . ' - ' . wc_price($range[1]) . '</option>';
								}
							}

							$attrHtml .= $priceHtml . '</select>';
						}
					}
					break;
			}
		}
		foreach ($filters as $filter) {
			if (isset($filterEnable[$filter]) && !$filterEnable[$filter]) {
				unset($filterEnable[$filter]);
			}
		}
		
		if (count($filterEnable) > 0) {
			$filterText = $this->getTableSetting($settings, 'filter_text', 'Filter: ');
			$resetText = $this->getTableSetting($settings, 'reset_text', 'Reset ');
			$resetHtml = '<a href="#" class="wtbpResetFilter"><i class="fa fa-fw fa-undo"></i>' . esc_html($resetText) . '</a>';
			$wrapperBlockStart = '<div class="wtbpFiltersWrapper">' . esc_html($filterText);
			$wrapperBlockEnd = '</div>';

			$settingCategoryPosition = $this->getTableSetting($settings, 'filter_category_position', 'before');
			$html = $wrapperBlockStart;
			if ('after' === $settingCategoryPosition) {
				$html .= $attrHtml;
				$html .= $catHtml;
			} else {
				$html .= $catHtml;
				$html .= $attrHtml;
			}
			$html .= $resetHtml;
			if ($categoryMain) {
				$html .= '<input type="hidden" name="wtbpFilterRelations" value="' . esc_attr(htmlspecialchars(json_encode($relations), ENT_QUOTES, 'UTF-8')) . '">';
			}
			$html .= $wrapperBlockEnd;
			return $html;
		}
		return false;
	}

	public function cmp( $a, $b ) {
		return strcmp($a->name, $b->name);
	}

	public function getTaxonomyHierarchy( $taxonomy, $argsIn, $ids = array() ) {
		$args = array(
			'orderby' => !empty($argsIn['orderby']) ? $argsIn['orderby'] : 'name',
			'order' => !empty($argsIn['order']) ? $argsIn['order'] : 'asc',
			'hide_empty' => $argsIn['hide_empty'],
		);
		$args['parent'] = !empty($argsIn['parent']) ? $argsIn['parent'] : 0;

		$terms = get_terms($taxonomy, $args);
		$children = array();
		foreach ($terms as $term) {
			if (empty($argsIn['only_parent'])) {
				if (!empty($term->term_id)) {
					$termId = $term->term_id;
					if (!empty($ids) && !in_array($termId, $ids)) {
						$args = array(
							'hide_empty' => false,
							'child_of' => $term->term_id,
							'fields' => 'ids'
						);
						$childs = get_terms($taxonomy, $args);
						$found = false;
						foreach (get_terms($taxonomy, $args) as $key => $id) {
							if (in_array($id, $ids)) {
								$found = true;
								break;
							}
						}
						if (!$found) {
							continue;
						}
					}

					$args = array(
						'orderby' => 'name',
						'order' => 'asc',
						'hide_empty' => $argsIn['hide_empty'],
						'parent' => $term->term_id,
					);
					$term->children = $this->getTaxonomyHierarchy($taxonomy, $args, $ids);
				}
			}
			$children[$term->term_id] = $term;
		}
		return $children;
	}

	private function generateTaxonomyOptionsHtml( $terms, $pre = '', &$list = array(), $valueId = false ) {
		$html = '';
		$existTaxonomy = array();
		foreach ($terms as $term) {
			if (!in_array($term->name, $existTaxonomy)) {
				$id = ( $valueId && 2 === $valueId ? $term->term_id : $term->term_taxonomy_id );
				$name = $term->name;
				$html .= '<option value="' . esc_attr($valueId ? $id : $name) . '" data-id="' . esc_attr($id) . '">' . $pre . esc_html($name) . '</option>';
				$list[$id] = $name;
				if (!empty($term->children)) {
					$html .= $this->generateTaxonomyOptionsHtml($term->children, $pre . '&nbsp;&nbsp;&nbsp;', $list, $valueId);
				}
				$existTaxonomy[] = $name;
			}
		}
		return $html;
	}

	public function getTermsForTaxonomy( $taxonomy ) {
		if (!isset($this->taxonomies[$taxonomy])) {
			$terms = taxonomy_exists($taxonomy) ? get_terms($taxonomy) : array();
			$this->taxonomies[$taxonomy]['all'] = ( $terms && ! is_wp_error($terms) ? $terms : array() );
		}

		return $this->taxonomies[$taxonomy]['all'];
	}
	public function getTermNameBySlug( $taxonomy, $slug ) {
		if (empty($slug)) {
			return '';
		}
		$terms = $this->getTermsForTaxonomy($taxonomy);	
		if (!isset($terms['slug'])) {
			$slugs = array();
			foreach ($terms as $term) {
				$slugs[$term->slug] = $term->name;
			}
			$this->taxonomies[$taxonomy]['slug'] = $slugs;
			return $this->getTableSetting($slugs, $slug, '');
		}
		return $this->getTableSetting($terms, $slug, '');
	}

	public function getColumnContent( $data, $params ) {
		$frontend = $params['frontend'];
		$settings = $params['settings'];
		$_product = $params['product'];

		$id = $_product->get_id();
		$mainId = empty($params['mainId']) ? $id : $params['mainId'];

		$column = $params['column'];
		$slug = $column['slug'];
		$taxonomies = $this->taxonomies;

		$imgSize = $params['imgSize'];
		switch ($column['main_slug']) {
			case 'attribute':
				$isFilterAttribute = $this->getTableSetting($settings, 'filter_attribute', false);
				$isAttrIntegerVal = $this->getTableSetting($column, 'product_attribute_integer_value', false);
				$hideInvisible = $this->getTableSetting($column, 'hide_invisible_attribute', false);
				$filteredStr = '';
				$separator = $this->getTableSetting($column, 'product_attribute_new_line', false) ? '<br />' : ', ';
				if (isset($column['sub_slug'])) {
					if ($_product->is_type('variable')) {
						$attributes = $_product->get_attributes();
						$attrObj = isset($attributes[$column['sub_slug']]) ? $attributes[$column['sub_slug']] : false;
						if ( $attrObj && ( ! $hideInvisible || $attrObj->get_visible() ) ) {
							if ($attrObj->get_variation()) {
								$inStocks = array();
								foreach ($_product->get_available_variations() as $variation) {
									if ($variation['variation_is_visible'] && $variation['is_in_stock']) {
										$taxonomy = $column['sub_slug'];
										$key = 'attribute_' . $taxonomy;

										if (isset($variation['attributes']) && isset($variation['attributes'][$key])) {
											$termSlug = $variation['attributes'][$key];
											if (!empty($termSlug)) {
												$value = $this->getTermNameBySlug($taxonomy, $termSlug);
												$inStocks[] = empty($value) ? $termSlug : $value;
											}
										}
									}
								}
								$data[$slug] = implode($separator, array_unique($inStocks));
								if ($isFilterAttribute) {
									$filteredStr = implode(',', array_unique($inStocks));
								}
							} else {
								$termSlug = $_product->get_attribute($column['sub_slug']);
								$value = $this->getTermNameBySlug($column['sub_slug'], $termSlug);
								$data[$slug] = empty($value) ? $termSlug : $value;
								if ($isFilterAttribute) {
									$filteredStr = $data[$slug];
								}
							}
						}
						
					} else {
						$termSlug = $_product->get_attribute($column['sub_slug']);

						$fromMain = false;
						if (empty($termSlug) && $mainId != $id) {
							$mainProduct = wc_get_product($mainId);
							$termSlug = $mainProduct->get_attribute($column['sub_slug']);
							$fromMain = true;
						}

						if ( $hideInvisible && ! empty( $termSlug ) ) {
							$attributes = $fromMain ? $mainProduct->get_attributes() : $_product->get_attributes();
							if ( ! isset( $attributes[ $column['sub_slug'] ] ) || ( is_object( $attributes[ $column['sub_slug'] ] ) && ! $attributes[ $column['sub_slug'] ]->get_visible() ) ) {
								$termSlug = '';
							}
						}
						
						$value = $this->getTermNameBySlug($column['sub_slug'], $termSlug);
						$data[$slug] = empty($value) ? $termSlug : $value;
						if ($isFilterAttribute) {
							$filteredStr = $data[$slug];
						}
					}
				} else {
					$data[$slug] = $this->getProductAttribute($_product, $separator, $hideInvisible);
					if ($isFilterAttribute) {
						$filteredStr = $this->getProductAttribute($_product, ',', $hideInvisible);
					}
				}
				if ( ! empty( $filteredStr ) || '0' === $filteredStr ) {
					$data[ $slug ] = array( 0 => $data[ $slug ], 2 => $filteredStr );
				}
				if ($isAttrIntegerVal) {
					$data[$slug] =
					array(
						$data[$slug],
						intval(preg_replace('/[^0-9]/', '', $data[$slug])),
						$filteredStr
					);
				}
				break;
			case 'tags':
				$terms = false;
				$isFilterTags = $this->getTableSetting($settings, 'filter_tag', false);
				$tagsSeparator = $this->getTableSetting($column, 'product_tag_new_line', false) ? '<br />' : ', ';
				if ($this->getTableSetting($column, 'product_tag_link', false)) {
					$tags = get_the_term_list($mainId, 'product_tag', '', $tagsSeparator, '');
					if (!$frontend || $this->getTableSetting($column, 'product_tag_link_blank', false)) {
						$tags = str_ireplace('<a', '<a target="_blank"', $tags);
					}
				} else {
					$terms = get_the_terms($mainId, 'product_tag');
					$tags = '';
					
					if (!empty($terms)) {
						$first = true;
						foreach ($terms as $term) {
							if ($first) {
								$first = false;
							} else {
								$tags .= $tagsSeparator;
							}
							$tags .= $term->name;
						}
					}
				}
				if ($isFilterTags) {
					if (false === $terms) {
						$terms = get_the_terms($mainId, 'product_tag');
					}
					$tagsSearch = '';
					if (!empty($terms)) {
						$first = true;
						foreach ($terms as $term) {
							if ($first) {
								$first = false;
							} else {
								$tagsSearch .= ',';
							}
							$tagsSearch .= $term->name;
						}
					}
					
					$data[$slug] = array($tags, 2 => $tagsSearch);
				} else {
					$data[$slug] = $tags;
				}
				break;
			case 'ctax':
				if ($this->getTableSetting($column, 'product_ctax_link', false)) {
					$tax = get_the_term_list($id, $column['sub_slug'], '', ', ', '');
					if ( false != $tax && ( !$frontend || $this->getTableSetting($column, 'product_ctax_link_blank', false) ) ) {
						$tax = str_ireplace('<a', '<a target="_blank"', $tax);
					}
				} else {
					$terms = get_the_terms($id, $column['sub_slug']);
					$tax = '';
					if (!empty($terms)) {
						foreach ($terms as $term) {
							$tax .= $term->name . ', ';
						}
						$tax = substr($tax, 0, -2);
					}
				}
				$data[$slug] = false === $tax ? '' : $tax;
				break;
			case 'acf':
				$acf =
					FrameWtbp::_()
						->getModule('wootablespro')
						->getModel('acf')
						->initGetAcf($column['sub_slug'], $id, $column);

				$isFieldAddToCart = $this->getTableSetting($column, 'acf_text_input');
				$acfValueList = array();
				if ( $isFieldAddToCart ) {
					$acfValueList = $acf->getAcfTableAddToCartView();
				} else {
					if ($acf->isTypeGrouped) {

						$groupList = $acf->getFieldGroupList();
						foreach ($groupList as $fieldDataList) {

							$fieldKey = empty($fieldDataList['settings']['key']) ? $fieldDataList['key'] : $fieldDataList['settings']['key'];
							$fieldName =  empty($fieldDataList['settings']['label']) ? $fieldDataList['label'] : $fieldDataList['settings']['label'];

							$columnSubfield = array(
								'slug'          =>  $acf->acfPrefix . '-' . $fieldKey,
								'original_name' => $fieldName,
								'display_name'  => '',
								'main_slug'     => $acf->acfPrefix,
								'sub_slug'      => $fieldKey,
							);

							$acfSubfield =
								FrameWtbp::_()
									->getModule('wootablespro')
									->getModel('acf')
									->initGetAcf($fieldKey, $id, $columnSubfield);

							if (!empty($fieldDataList['value'])) {
								$acfSubfield->field = $fieldDataList['value'];
							}

							$acfChildFieldValue = $acfSubfield->getAcfTableView();

							if (!empty($acfChildFieldValue[0])) {
								$acfValueChildList[] = $acfSubfield->formatChildField($acfChildFieldValue[0]);
							}
						}
						if (!empty($acfValueChildList)) {
							$acfValueList[] = implode($acfValueChildList);
						}
					} else {
						$acfValueList = $acf->getAcfTableView();
					}
				}
				$data[$slug] = $acfValueList;
				break;
			case 'sales':
				$data[$slug] = get_post_meta($id, 'total_sales', true);
				break;
			case 'yith':
				if ( is_plugin_active( 'yith-woocommerce-quick-view-premium/init.php' ) ) {
					$data[ $slug ] = YITH_WCQV_Frontend()->yith_add_quick_view_button( $id, $this->getTableSetting( $column, 'button_label', '' ), '', true );
				} elseif ( is_plugin_active( 'yith-woocommerce-quick-view/init.php' ) ) {
					$data[ $slug ] = YITH_WCQV_Frontend()->yith_add_quick_view_button( $id, $this->getTableSetting( $column, 'button_label', '' ), true );
				}
				break;
			case 'xoo':
				$data[ $slug ] = $this->xoo_qv_button($id);
				break;
			case 'vendor':
				$post = get_post($_product->get_id());
				$vendor_id = $post->post_author;
				$vendor_shop_link = WCV_Vendors::get_vendor_shop_page($vendor_id);
				$shop_name = get_user_meta( $vendor_id, 'pv_shop_name', true );
				if (empty($shop_name)) {
					$shop_name = get_the_author_meta( 'display_name', $vendor_id );
				}
				$data[$slug] = $shop_name;
				break;
			case 'weight':
				$weight_unit = get_option('woocommerce_weight_unit');
				$data[$slug] = get_post_meta($id, '_weight', true) . ' ' . $weight_unit;
				break;
			case 'dimensions':
				$dimensions = '';
				if ($_product->has_dimensions()) {
					$dimensions = wc_format_dimensions($_product->get_dimensions(false));
				}
				$data[$slug] = $dimensions;
				break;
			default:
				break;
		}
		return $data;
	}

	public function getProductAttribute( $_product, $separator = ', ', $hideInvisible = false) {
		$attributes = $_product->get_attributes();
		$attrStr = '';
		if ('product' == $_product->post_type) {
			foreach ($attributes as $attribute) {
				if ( $hideInvisible && !$attribute->get_visible() ) {
					continue;
				}
				$name = $attribute->get_name();
				$tax_terms = array();
				if ($attribute->is_taxonomy()) {
					$terms = get_the_terms($_product->get_id(), $name);
					if (!empty($terms[0]) && $terms[0]->taxonomy) {
						$tax = $terms[0]->taxonomy;
						$tax_object = get_taxonomy($tax);
						if (isset ($tax_object->labels->singular_name)) {
							$tax_label = $tax_object->labels->singular_name;
						} elseif (isset($tax_object->label)) {
							$tax_label = $tax_object->label;
							// Trim label prefix since WC 3.0
							if (0 === strpos($tax_label, 'Product ')) {
								$tax_label = substr($tax_label, 8);
							}
						}
						$attrStr .= $tax_label . ': ';
						foreach ($terms as $term) {
							$single_term = esc_html($term->name);
							array_push($tax_terms, $single_term);
						}
					}
				} else {
					$attrStr .= $name . ': ';
					$terms = $attribute->get_options();
					foreach ($terms as $term) {
						array_push($tax_terms, esc_html($term));
					}
				}
				$attrStr .= implode($separator, $tax_terms) . '<br />';
			}
		} else {
			foreach ($attributes as $attribute => $slug) {
				$value = $this->getTermNameBySlug($attribute, $slug);
				$attrStr .= wc_attribute_label($attribute, $_product) . ': ';
				$attrStr .= ( empty($value) ? $slug : $value ) . '<br />';
			}
		}
		return $attrStr;
	}

	public function addEditAdminSettings( $part, $params ) {
		foreach ($params as $param => $data) {
			$this->assign($param, $data);
		}
		$this->assign('image_path', FrameWtbp::_()->getModule('wootablepress')->getModPath());
		parent::display($part . 'Pro');
	}

	public function getCustomStyles( $css, $tableIdView, $settings ) {
		$customStyles = $this->getTableSetting($settings, 'use_custom_styles', false);
		$cartStyles = $this->getTableSetting($settings, 'use_cart_styles', false);

		if (!$customStyles && !$cartStyles) {
			return $css;
		}
		$module = $this->getModule();

		$standartFonts = $module->getStandardFontsList();
		$defaultFont = $module->defaultFont;

		$stylesCss = array();
		$fonts = array();
		$tableSelector = '#' . $tableIdView;
		$important = ' !important';

		// custom table styles
		if ($customStyles) {

			$headerFixed = $this->getTableSetting($settings, 'header_fixed', '0');

			$styles = $this->getTableSetting($settings, 'styles', array());
			if (count($styles) > 0) {

				$wrapperSelector = $tableSelector . '_wrapper';
				$searchSelector = $tableSelector . '_filter input, ' . $wrapperSelector . ' .wtbpColumnsSearchWrapper .search-column';

				$stylesCss[$wrapperSelector . ' table']['border-collapse'] = 'collapse';
				if ($headerFixed) {
					$fHeaderSelector = 'table.fixedHeader-floating[aria-describedby="' . $tableIdView . '_info"]';
					$stylesCss[$fHeaderSelector]['border-collapse'] = 'collapse';
				}

				$color = $this->getTableSetting($styles, 'external_border_color', '');
				$width = $this->getTableSetting($styles, 'external_border_width', '');
				if (!empty($color) && !empty($width)) {
					$border = $width . 'px solid ' . $color . $important;
					$stylesCss[$tableSelector]['border'] = $border;
					$stylesCss[$wrapperSelector . ' .dataTables_scroll']['border'] = $border;
					$stylesCss[$wrapperSelector . ' .DTFC_ScrollWrapper']['border'] = $border;
					$stylesCss[$wrapperSelector . ' .DTFC_ScrollWrapper .dataTables_scroll']['border'] = 'none' . $important;
					$stylesCss[$wrapperSelector . ' .dataTables_scrollBody table']['border'] = 'none' . $important;
				}

				$color = $this->getTableSetting($styles, 'header_border_color', '');
				$width = $this->getTableSetting($styles, 'header_border_width', '');
				if (!empty($color) && !empty($width)) {
					$border = $width . 'px solid ' . $color . $important;
					$stylesCss[$wrapperSelector . ' th']['border'] = $border;
					$stylesCss[$wrapperSelector . ' th']['border-left'] = 'none' . $important;
					$stylesCss[$wrapperSelector . ' th:first-child']['border-left'] = $border;
					$stylesCss[$wrapperSelector . ' .dataTables_scrollBody th']['border-bottom'] = 'none' . $important;
					$stylesCss[$wrapperSelector . ' .dataTables_scrollBody th']['border-top'] = 'none' . $important;
					$stylesCss[$wrapperSelector . ' .child table']['border-collapse'] = 'collapse';
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' th']['border'] = $border;
						$stylesCss[$fHeaderSelector . ' .child table']['border-collapse'] = 'collapse';
						$stylesCss[$fHeaderSelector . ' th']['border-left'] = 'none' . $important;
						$stylesCss[$fHeaderSelector . ' th:first-child']['border-left'] = $border;
					}
				}

				$color = $this->getTableSetting($styles, 'row_border_color', '');
				$width = $this->getTableSetting($styles, 'row_border_width', '');
				if (!empty($color) && !empty($width)) {
					$border = $width . 'px solid ' . $color;
					$stylesCss[$wrapperSelector . ' td']['border-top'] = $border;
					$stylesCss[$wrapperSelector . ' tbody tr:first-child td']['border-top'] = 'none';
					$stylesCss[$wrapperSelector . ' tbody tr:last-child td']['border-bottom'] = $border;
					$stylesCss[$wrapperSelector . ' .child table']['border-collapse'] = 'collapse';
				}

				$color = $this->getTableSetting($styles, 'column_border_color', '');
				$width = $this->getTableSetting($styles, 'column_border_width', '');
				if (!empty($color) && !empty($width)) {
					$border = $width . 'px solid ' . $color;
					$stylesCss[$wrapperSelector . ' td']['border-left'] = $border;
					$stylesCss[$wrapperSelector . ' td']['border-right'] = $border;
				}

				$color = $this->getTableSetting($styles, 'header_bg_color', '');
				if (!empty($color)) {
					$value = $color . $important;
					$stylesCss[$wrapperSelector . ' th']['background-color'] = $value;
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' th']['background-color'] = $value;
					}
				}

				$font = $this->getTableSetting($styles, 'header_font_family', '');
				if (!empty($font) && $font != $defaultFont) {
					$value = '"' . $font . '"';
					$stylesCss[$wrapperSelector . ' th']['font-family'] = $value;
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' th']['font-family'] = $value;
					}
					if (!in_array($font, $standartFonts)) {
						$fonts[str_replace(' ', '+', $font)] = $font;
					}
				}
				$color = $this->getTableSetting($styles, 'header_font_color', '');
				if (!empty($color)) {
					$stylesCss[$wrapperSelector . ' th']['color'] = $color;
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' th']['color'] = $color;
					}
				}
				$size = $this->getTableSetting($styles, 'header_font_size', '');
				if (!empty($size)) {
					$value = $size . 'px';
					$stylesCss[$wrapperSelector . ' th']['font-size'] = $value;
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' th']['font-size'] = $value;
					}
				}

				$color = $this->getTableSetting($styles, 'cell_bg_color', '');
				if (!empty($color)) {
					$stylesCss[$wrapperSelector . ' tbody tr']['background-color'] = $color;
					$stylesCss[$wrapperSelector . ' tbody td']['background-color'] = 'inherit';

					$even = $this->getTableSetting($styles, 'cell_color_even', '');
					if (!empty($even)) {
						$stylesCss[$wrapperSelector . ' table.stripe tbody tr.even']['background-color'] = $even;
						$stylesCss[$wrapperSelector . ' table.stripe.order-column tbody tr > .sorting_1']['background-color'] = $even;
					}

					$hover = $this->getTableSetting($styles, 'cell_color_hover', '');
					if (!empty($hover)) {
						$stylesCss[$wrapperSelector . ' table.hover tbody tr:hover']['background-color'] = $hover;
						$stylesCss[$wrapperSelector . ' table.stripe.order-column tbody tr.even > .sorting_1']['background-color'] = $hover;
					}

					$order = $this->getTableSetting($styles, 'cell_color_order', '');
					if (!empty($order)) {
						$stylesCss[$wrapperSelector . ' table.order-column tbody tr > .sorting_1']['background-color'] = $even;
						$stylesCss[$wrapperSelector . ' table.hover.order-column tbody tr:hover > .sorting_1']['background-color'] = $order;
					}
				}

				$font = $this->getTableSetting($styles, 'cell_font_family', '');
				if (!empty($font) && $font != $defaultFont) {
					$stylesCss[$wrapperSelector . ' td']['font-family'] = '"' . $font . '"';
					if (!in_array($font, $standartFonts)) {
						$fonts[str_replace(' ', '+', $font)] = $font;
					}
				}
				$color = $this->getTableSetting($styles, 'cell_font_color', '');
				if (!empty($color)) {
					$stylesCss[$wrapperSelector . ' td']['color'] = $color;
				}
				$size = $this->getTableSetting($styles, 'cell_font_size', '');
				if (!empty($size)) {
					$stylesCss[$wrapperSelector . ' td']['font-size'] = $size . 'px';
				}

				$top = $this->getTableSetting($styles, 'cell_padding_top', '', false, false, true);
				if ('' !== $top) {
					$stylesCss[$wrapperSelector . ' td']['padding-top'] = $top . 'px' . $important;
				}
				$right = $this->getTableSetting($styles, 'cell_padding_right', '', false, false, true);
				if ('' !== $right) {
					$stylesCss[$wrapperSelector . ' td']['padding-right'] = $right . 'px' . $important;
				}
				$bottom = $this->getTableSetting($styles, 'cell_padding_bottom', '', false, false, true);
				if ('' !== $bottom) {
					$stylesCss[$wrapperSelector . ' td']['padding-bottom'] = $bottom . 'px' . $important;
				}
				$left = $this->getTableSetting($styles, 'cell_padding_left', '', false, false, true);
				if ('' !== $left) {
					$stylesCss[$wrapperSelector . ' td']['padding-left'] = $left . 'px' . $important;
				}

				$color = $this->getTableSetting($styles, 'search_bg_color', '');
				if (!empty($color)) {
					$stylesCss[$searchSelector]['background-color'] = $color . $important;
				}
				$color = $this->getTableSetting($styles, 'search_font_color', '');
				if (!empty($color)) {
					$stylesCss[$searchSelector]['color'] = $color . $important;
				}
				$color = $this->getTableSetting($styles, 'search_border_color', '');
				if (!empty($color)) {
					$stylesCss[$searchSelector]['border'] = '1px solid ' . $color . $important;
				}

				if ($this->getTableSetting($styles, 'fixed_layout', '0')) {
					$stylesCss[$tableSelector]['table-layout'] = 'fixed' . $important;
					$stylesCss[$tableSelector]['overflow-wrap'] = 'break-word';
					$stylesCss[$wrapperSelector . ' .dataTables_scroll table']['table-layout'] = 'fixed' . $important;
					$stylesCss[$wrapperSelector . ' .dataTables_scroll table']['overflow-wrap'] = 'break-word';
				}

				$align = $this->getTableSetting($styles, 'vertical_alignment', '');
				if (!empty($align)) {
					$stylesCss[$tableSelector . ' th, ' . $tableSelector . ' td, ' . $tableSelector . '.wtbpVarAttributes']['vertical-align'] = $align;
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' th']['vertical-align'] = $align;
					}
				}

				$align = $this->getTableSetting($styles, 'horizontal_alignment', '');
				if (!empty($align)) {
					$stylesCss[$tableSelector . ' td']['text-align'] = $align;
					$thumbSelector = $tableSelector . ' .thumbnail img, ' . $tableSelector . ' .wtbpAddToCartWrapper';
					if ('left' == $align) {
						$stylesCss[$thumbSelector]['margin-right'] = 'auto';
						$stylesCss[$thumbSelector]['margin-left'] = '0';
					} else if ('right' == $align) {
						$stylesCss[$thumbSelector]['margin-left'] = 'auto';
						$stylesCss[$thumbSelector]['margin-right'] = '0';
					} else if ('center' == $align) {
						$stylesCss[$thumbSelector]['margin-left'] = 'auto';
						$stylesCss[$thumbSelector]['margin-right'] = 'auto';
					}
				}

				$align = $this->getTableSetting($styles, 'header_hor_alignment', '');
				if (!empty($align)) {
					$stylesCss[$wrapperSelector . ' th']['text-align'] = $align;
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' th']['text-align'] = $align;
					}
				}

				$align = $this->getTableSetting($styles, 'pagination_position', '');
				if (!empty($align)) {
					$stylesCss[$wrapperSelector . ' .dataTables_paginate']['text-align'] = $align;
					$stylesCss[$wrapperSelector . ' .dataTables_paginate']['float'] = 'none';
				}

				if ($this->getTableSetting($styles, 'show_sort_hover', '0')) {
					$value = 'url("' . WTBP_PLUGINS_URL . '/' . WTBP_PLUG_NAME . '/modules/wootablepress/images/sort_both.png")';
					$stylesCss[$wrapperSelector . ' table .sorting']['background-image'] = 'none';
					$stylesCss[$wrapperSelector . ' table th.sorting:hover']['background-image'] = $value;
					if ($headerFixed) {
						$stylesCss[$fHeaderSelector . ' .sorting']['background-image'] = 'none';
						$stylesCss[$fHeaderSelector . ' th.sorting:hover']['background-image'] = $value;
					}
				}

				if ($this->getTableSetting($styles, 'filter_select_flixible', '0')) {
					$stylesCss[$wrapperSelector . ' .wtbpFiltersWrapper select']['max-width'] = '90%';
				}

				$widthUnit = $this->getTableSetting($styles, 'column_popup_width_unit', '%');

				$width = $this->getTableSetting($styles, 'column_popup_width', '80');
				if (!empty($widthUnit) && !empty($width)) {
					$stylesCss['.wtbpModalContent']['width'] = $width . $widthUnit;
					$stylesCss['.wtbpModalContent.wtbpModalContentForVariations']['max-width'] = '600px';
				}
			}
		}

		// custom styles for button Add to cart
		if ($cartStyles) {
			$found = false;
			$styles = $this->getTableSetting($settings, 'cart_styles', array());
			if (count($styles) > 0) {
				
				$orders = FrameWtbp::_()->getModule('wootablepress')->getView()->orderColumns;
				$buyColumn = 'add_to_cart';
				$found = false;
				foreach ($orders as $column) {
					if ( $column['slug'] === $buyColumn || 'thumbnail' === $column['slug'] ) {
						$found = true;
						if ( ! empty( $styles['buttons_in_a_row'] ) ) {
							$tdSelector                                                     = $tableSelector . ' .add_to_cart';
							$stylesCss[ $tdSelector ]['white-space']                        = 'nowrap';
							$stylesCss[ $tdSelector ]['display']                            = 'flex';
							$stylesCss[ $tdSelector ]['flex-direction']                     = 'row';
							$stylesCss[ $tdSelector ]['align-items']                        = 'center';
							$stylesCss[ $tableSelector . ' .dtr-data div' ]['display']      = 'inline-flex';
							$stylesCss[ $tableSelector . ' .wtbpVarPrice' ]['margin-left']  = '0.5em';
							$stylesCss[ $tdSelector . ' .wtbpVarAttributes' ]['display']    = 'inline-flex';
							$stylesCss[ $tdSelector . ' .wtbpAddToCartWrapper' ]['display'] = 'inline-flex';
							$stylesCss[ $tdSelector . ' .wtbpVarAttributes' ]['flex-flow']  = 'nowrap';
						}
						break;
					}
					if (!empty($column['add_cart_button'])) {
						$found = true;
						break;
					}
				}
			}
			if ($found) {
				$buttonSelector = $tableSelector . ' .wtbpAddToCartWrapper .button';
				$buttonHover = $buttonSelector . ':hover';
				$effects = array('' => $buttonSelector, '_hover' => $buttonHover);
				$stylesCss[$buttonSelector]['overflow'] = 'hidden' . $important;

				// text font
				$font = $this->getTableSetting($styles, 'font_family', '');
				if (!empty($font) && $font != $defaultFont) {
					$value = '"' . $font . '"';
					$stylesCss[$buttonSelector]['font-family'] = $value . $important;
					if (!in_array($font, $fonts) && !in_array($font, $standartFonts)) {
						$fonts[str_replace(' ', '+', $font)] = $font;
					}
				}
				$size = $this->getTableSetting($styles, 'font_size', '', true, false, true);
				if (!empty($size)) {
					$stylesCss[$buttonSelector]['font-size'] = $size . 'px' . $important;
				}

				foreach ($effects as $effect => $selector) {
					$color = $this->getTableSetting($styles, 'font_color' . $effect, '');
					if (!empty($color)) {
						$stylesCss[$selector]['color'] = $color . $important;
					}
					$weight = $this->getTableSetting($styles, 'font_weight' . $effect, '');
					if ('n' == $weight) {
						$stylesCss[$selector]['font-weight'] = 'normal' . $important;
					}
					if ( 'b' == $weight || 'bi' == $weight ) {
						$stylesCss[$selector]['font-weight'] = 'bold' . $important;
					}
					if ( 'i' == $weight || 'bi' == $weight ) {
						$stylesCss[$selector]['font-style'] = 'italic' . $important;
						if ( 'i' == $weight ) {
							$stylesCss[$selector]['font-weight'] = 'normal' . $important;
						}
					}
				}

				// text shadow
				$x = $this->getTableSetting($styles, 'text_shadow_x', '', true, false, true);
				$y = $this->getTableSetting($styles, 'text_shadow_y', '', true, false, true);
				if ( '' !== $x && '' !== $y ) {
					$value = $x . 'px ' . $y . 'px';
					$blur = $this->getTableSetting($styles, 'text_shadow_blur', '', true, false, true);
					if ('' !== $blur) {
						$value .= ' ' . $blur . 'px';
					}
					$color = $this->getTableSetting($styles, 'text_shadow_color', '');
					if (!empty($color)) {
						$value .= ' ' . $color;
					}
					$stylesCss[$buttonSelector]['text-shadow'] = $value . $important;
				}

				// padding
				$top = $this->getTableSetting($styles, 'padding_top', '', true, false, true);
				if ('' != $top) {
					$stylesCss[$buttonSelector]['padding-top'] = $top . 'px' . $important;
				}
				$right = $this->getTableSetting($styles, 'padding_right', '', true, false, true);
				if ('' != $right) {
					$stylesCss[$buttonSelector]['padding-right'] = $right . 'px' . $important;
				}
				$bottom = $this->getTableSetting($styles, 'padding_bottom', '', true, false, true);
				if ('' != $bottom) {
					$stylesCss[$buttonSelector]['padding-bottom'] = $bottom . 'px' . $important;
				}
				$left = $this->getTableSetting($styles, 'padding_left', '', true, false, true);
				if ('' != $left) {
					$stylesCss[$buttonSelector]['padding-left'] = $left . 'px' . $important;
				}

				// button size
				$bWidth = $this->getTableSetting($styles, 'button_width', '', true);
				if (!empty($bWidth)) {
					$stylesCss[$buttonSelector]['width'] = $bWidth . 'px' . $important;
					$stylesCss[$buttonSelector]['overflow'] = 'hidden' . $important;
				}
				$bHeight = $this->getTableSetting($styles, 'button_height', '', true);
				if (!empty($bHeight)) {
					$stylesCss[$buttonSelector]['height'] = $bHeight . 'px' . $important;
					if ( '' == $top && '' == $bottom ) {
						$stylesCss[$buttonSelector]['line-height'] = $bHeight . 'px' . $important;
						$stylesCss[$buttonSelector]['padding-top'] = '0' . $important;
						$stylesCss[$buttonSelector]['padding-bottom'] = '0' . $important;
					}
				}

				// radius coners
				$radius = $this->getTableSetting($styles, 'radius', '', true, false, true);
				if ('' !== $radius) {
					$stylesCss[$buttonSelector]['border-radius'] = $radius . $this->getTableSetting($styles, 'radius_unit', 'px') . $important;
				}

				// borders
				foreach ($effects as $effect => $selector) {
					$color = $this->getTableSetting($styles, 'button_border_color' . $effect, '');
					$width = $this->getTableSetting($styles, 'button_border_top' . $effect, '', true, false, true);
					if ('' !== $width) {
						$stylesCss[$selector]['border-top'] = $width . 'px solid ' . $color . $important;
					}
					$width = $this->getTableSetting($styles, 'button_border_right' . $effect, '', true, false, true);
					if ('' !== $width) {
						$stylesCss[$selector]['border-right'] = $width . 'px solid ' . $color . $important;
					}
					$width = $this->getTableSetting($styles, 'button_border_bottom' . $effect, '', true, false, true);
					if ('' !== $width) {
						$stylesCss[$selector]['border-bottom'] = $width . 'px solid ' . $color . $important;
					}
					$width = $this->getTableSetting($styles, 'button_border_left' . $effect, '', true, false, true);
					if ('' !== $width) {
						$stylesCss[$selector]['border-left'] = $width . 'px solid ' . $color . $important;
					}
				}

				// button shadow
				$x = $this->getTableSetting($styles, 'button_shadow_x', '', true, false, true);
				$y = $this->getTableSetting($styles, 'button_shadow_y', '', true, false, true);
				if ( '' !== $x && '' !== $y ) {
					$value = $x . 'px ' . $y . 'px';
					$blur = $this->getTableSetting($styles, 'button_shadow_blur', '', true, false, true);
					if ('' !== $blur) {
						$value .= ' ' . $blur . 'px';
					}
					$spread = $this->getTableSetting($styles, 'button_shadow_spread', '', true, false, true);
					if ('' !== $spread) {
						$value .= ' ' . $spread . 'px';
					}
					$color = $this->getTableSetting($styles, 'button_shadow_color', '');
					if (!empty($color)) {
						$value .= ' ' . $color;
					}
					$stylesCss[$buttonSelector]['box-shadow'] = $value . $important;
					$stylesCss['#wtbpPreviewTable .add_to_cart_inline']['overflow'] = 'visible' . $important;
				}

				// button background
				foreach ($effects as $effect => $selector) {
					$bgType = $this->getTableSetting($styles, 'background' . $effect, '');
					if (!empty($bgType)) {
						if ('unicolored' == $bgType) {
							$color = $this->getTableSetting($styles, 'button_color' . $effect, '');
							if (!empty($color)) {
								$stylesCss[$selector]['background'] = $color . $important;
							}
						} else {
							$color1 = $this->getTableSetting($styles, 'bg_color1' . $effect, '');
							$color2 = $this->getTableSetting($styles, 'bg_color2' . $effect, '');
							if (!empty($color1)) {
								$stylesCss[$selector]['background'] = $color1; // for Old browsers
								if (!empty($color2)) {
									switch ($bgType) {
										case 'bicolored':
											$value = 'linear-gradient( to bottom, ' . $color1 . ' 50%, ' . $color2 . ' 50% )';
											break;
										case 'gradient':
											$value = 'linear-gradient( to bottom, ' . $color1 . ', ' . $color2 . ')'; 
											break;
										case 'pyramid':
											$value = 'linear-gradient( to bottom, ' . $color1 . ' 0%, ' . $color2 . ' 50%, ' . $color1 . ' 100% )'; 
											break;
										default:
											$value = '';
											break;
									}
									if (!empty($value)) {
										$stylesCss[$selector]['background'] = '-webkit-' . $value . $important;
										$stylesCss[$selector]['background'] = '-moz-' . $value . $important;
										$stylesCss[$selector]['background'] = '-o-' . $value . $important;
										$stylesCss[$selector]['background'] = $value . $important;
									}
								}
							}
						}
					}
				}
			}
		}
		$customCSS = '';

		foreach ($fonts as $key => $value) {
			$customCSS .= '@import url("//fonts.googleapis.com/css?family=' . $key . '");';
		}

		foreach ($stylesCss as $selector => $rules) {
			$customCSS .= $selector . ' {';
			foreach ($rules as $key => $value) {
				$customCSS .= $key . ': ' . $value . ';';
			}
			$customCSS .= '} ';
		}

		return $customCSS . $css;
	}

	/**
	 * Combyne wp_query arguments for filtering, sorting and seearching in SSP table mod
	 *
	 * @param array $settings Table settings
	 * @param array $args initial wp_query args
	 * @param int $page
	 *
	 * @return array
	 */
	public function setSSPQueryFilters( $settings, $args, $page ) {
		$module = FrameWtbp::_()->getModule('wootablepress');
		$orders = $module->getView()->orderColumns;

		$args['posts_per_page'] = $page['length'];
		$args['offset'] = $page['start'];
		$args['wtbp_ssp'] = true;
		if (!empty($page['filters'])) {
			foreach ($page['filters'] as $filterSlug => $filterData) {
				if ( ! empty($filterData['filterParam']) && ! empty($filterSlug) ) {
					if ('meta' == $filterData['queryType']) {
						$args['wtbp_meta_query'][] = array(
							'meta'       => $filterSlug,
							'meta_key'   => $filterData['dataMetaKey'],
							'meta_value' => $filterData['filterParam'],
							'data_type'  => $filterData['dataType'],
							'logic'      => $this->getTableSetting($settings, 'filter_ctax_logic', 'or'),
						);
					} else {
						$filterParam = array();
						foreach ($filterData['filterParam'] as $terms) {
							$filterParam[] = stripslashes( $terms['filterValue'] );
						}
						$filterData['filterParam'] = $filterParam;
						$args['wtbp_tax_query'][] = array(
							'taxonomy'         => $filterSlug,
							'field'            => 'name',
							'terms'            => $filterData['filterParam'],
							'is_ids'           => $this->getTableSetting($filterData, 'isIds', 0),
							'logic'            => empty($page['logic']) ? 'or' : $this->getTableSetting($page['logic'], $filterSlug, 'or'),
							'include_children' => empty($page['children']) ? 0 : (int) $this->getTableSetting($page['children'], $filterSlug, 0)
						);
					}
				}
			}
		}

		if (!empty($page['search']['value'])) {
			$args['wtbp_search_main'] = array();
			$args['meta_query'] = array('relation' => 'AND');
			$keyword = $page['search']['value'];

			foreach ($orders as $order) {
				switch ($order['main_slug']) {
					case 'product_title':
						$args['wtbp_search_main'][] = array('type' => 'field', 'key' => 'post_title', 'value' => $keyword);
						break;
					case 'description':
						$args['wtbp_search_main'][] = array('type' => 'field', 'key' => 'post_content', 'value' => $keyword);
						break;
					case 'short_description':
						$args['wtbp_search_main'][] = array('type' => 'field', 'key' => 'post_excerpt', 'value' => $keyword);
						break;
					case 'product_link':
						$args['wtbp_search_main'][] = array('type' => 'field', 'key' => 'guid', 'value' => $keyword);
						break;
					case 'categories':
						$args['wtbp_search_main'][] = array('type' => 'tax', 'key' => 'product_cat', 'value' => $keyword);
						break;
					case 'tags':
						$args['wtbp_search_main'][] = array('type' => 'tax', 'key' => 'product_tag', 'value' => $keyword);
						break;
					case 'attribute':
						$args['wtbp_search_main'][] = array('type' => 'tax', 'key' => $order['sub_slug'], 'value' => $keyword);
						break;
					case 'ctax':
						$args['wtbp_search_main'][] = array('type' => 'tax', 'key' => $order['sub_slug'], 'value' => $keyword);
						break;
					case 'weight':
						$args['wtbp_search_main'][] = array('type' => 'meta', 'key' => '_weight', 'value' => $keyword);
						break;
					case 'sku':
						$args['wtbp_search_main'][] = array('type' => 'meta', 'key' => '_sku', 'value' => $keyword);
						break;
					case 'price':
						$args['wtbp_search_main'][] = array('type' => 'meta', 'key' => '_price', 'value' => $keyword);
						break;
					default:
				}
			}
		}

		if (!empty($page['columns'])) {
			$args['wtbp_search'] = array();
			$args['meta_query'] = array('relation' => 'AND');
			foreach ($page['columns'] as $column) {
				if (!empty($column['name']) && !empty($column['search']['value'])) {
					$slug = $column['name'];
					$keyword = $column['search']['value'];
					$multyKeyword = explode('|', $keyword);
					$multyKeyword = array_map('trim', $multyKeyword);

					foreach ($orders as $order) {
						if ($order['slug'] == $slug) {
							switch ($order['main_slug']) {
								case 'product_title':
									$args['wtbp_search'][] = array('type' => 'field', 'key' => 'post_title', 'value' => $multyKeyword);
									break;
								case 'description':
									$args['wtbp_search'][] = array('type' => 'field', 'key' => 'post_content', 'value' => $multyKeyword);
									break;
								case 'short_description':
									$args['wtbp_search'][] = array('type' => 'field', 'key' => 'post_excerpt', 'value' => $multyKeyword);
									break;
								case 'product_link':
									$args['wtbp_search'][] = array('type' => 'field', 'key' => 'guid', 'value' => $multyKeyword);
									break;
								case 'date':
									$dateFormat = $this->getTableSetting($settings, 'date_formats', false);
									$timeFormat = $this->getTableSetting($settings, 'time_formats', false);
									$mysqlDFormats = array('d' => '%d', 'm' => '%m', 'Y' => '%Y');
									$mysqlTFormats = array('h' => '%k', 'i' => '%i', 's' => '%s', 'a' => '%p', 'H' => '%h');
									$format = '';
									if ($dateFormat) {
										$format = str_replace(array_keys($mysqlDFormats), array_values($mysqlDFormats), $dateFormat);
									}
									if ($timeFormat) {
										if (!empty($format)) {
											$format .= ' ';
										}
										$format .= str_replace(array_keys($mysqlTFormats), array_values($mysqlTFormats), $timeFormat);
									}
									$args['wtbp_search'][] = array('type' => 'field', 'key' => "DATE_FORMAT(post_date,'" . $format . "')", 'value' => $multyKeyword);
									break;
								case 'sku':
									$args['meta_query'][] = $this->buildFieldMetaQueryForSspSearch('_sku', $multyKeyword);
									break;
								case 'stock':
									$args['meta_query'][] = $this->buildFieldMetaQueryForSspSearch('_stock_status', $multyKeyword);
									break;
								case 'reviews':
									$args['meta_query'][] = $this->buildFieldMetaQueryForSspSearch('_wc_average_rating', $multyKeyword);
									break;
								case 'price':
									$args['meta_query'][] = $this->buildFieldMetaQueryForSspSearch('_price', $multyKeyword);
									break;
								case 'categories':
									$args['wtbp_search'][] = array('type' => 'tax', 'key' => 'product_cat', 'value' => $multyKeyword);
									break;
								case 'tags':
									$args['wtbp_search'][] = array('type' => 'tax', 'key' => 'product_tag', 'value' => $multyKeyword);
									break;
								case 'attribute':
									$args['wtbp_search'][] = array('type' => 'tax', 'key' => $order['sub_slug'], 'value' => $multyKeyword);
									break;
								case 'ctax':
									$args['wtbp_search'][] = array('type' => 'tax', 'key' => $order['sub_slug'], 'value' => $multyKeyword);
									break;
								case 'acf':
									$args['meta_query'][] = $this->buildFieldMetaQueryForSspSearch($order['sub_slug'], $multyKeyword);
									break;
								case 'weight':
									$args['meta_query'][] = $this->buildFieldMetaQueryForSspSearch('_weight', $multyKeyword);
									break;
								case 'vendor':
									$args['wtbp_search'][] = array('type' => 'user', 'key' => 'user', 'value' => $multyKeyword);
									break;
								default:
							}
						}
					}
				}
			}
		}

		if (!empty($page['sortCol']) && !empty($page['order']['0']['dir'])) {
			$direction = $page['order']['0']['dir'];
			switch ($page['sortCol']['main_slug']) {
				case 'product_title':
					$args['orderby'] = array('title' => $direction, 'ID' => 'ASC');
					break;
				case 'date':
					$args['orderby'] = array('date' => $direction, 'ID' => 'ASC');
					break;
				case 'stock':
					$args['meta_key'] = '_stock_status';
					$args['orderby'] = array('meta_value' => $direction, 'ID' => 'ASC');
					break;
				case 'sku':
					$args['meta_key'] = '_sku';
					$args['orderby'] = array('meta_value' => $direction, 'ID' => 'ASC');
					break;
				case 'categories':
					$args['orderby'] = 'wtbp-product_cat';
					break;
				case 'reviews':
					$args['meta_key'] = '_wc_average_rating';
					$args['orderby']  = array('meta_value_num' => $direction, 'ID' => 'ASC');
					break;
				case 'price':
					$args['meta_key'] = '_price';
					$args['orderby'] = array('meta_value_num' => $direction, 'ID' => 'ASC');
					break;
				case 'sales':
					$args['meta_key'] = 'total_sales';
					$args['orderby'] = array('meta_value_num' => $direction, 'ID' => 'ASC');
					break;
				case 'acf':
					$args['orderby'] = $page['sortCol']['slug'];
					break;
				default:
					$args['orderby'] = 'wtbp-' . ( isset($page['sortCol']['sub_slug']) ? $page['sortCol']['sub_slug'] : $page['sortCol']['slug'] );
			}
			$args['order'] = $page['order']['0']['dir'];
		}
		
		add_filter('posts_clauses', array($this, 'setClausesTaxOrder'), 10, 2);

		return $args;
	}
	
	private function buildFieldMetaQueryForSspSearch( $key, $values ) {
		$values = !is_array($values) ? array($values) : $values;
		$fieldMetaQuery = array();
		if (count($values) > 1) {
			$fieldMetaQuery['relation'] = 'OR';
		}
		foreach ($values as $value) {
			array_push($fieldMetaQuery, array(
				'key' => $key,
				'compare' => 'LIKE',
				'value' => $value
			));
		}

		return $fieldMetaQuery;
	}

	public function setLazyLoadQueryFilters( $args, $settings, $page ) {
		if ( $this->getTableSetting($settings, 'pagination', false) ) {
			return $args;
		}
		if ( $this->getTableSetting($settings, 'pagination', false) && $this->getTableSetting($settings, 'pagination_ssp', false) ) {
			return $args;
		}
		if ( !$this->getTableSetting($settings, 'lazy_load', false) ) {
			return $args;
		}
		
		$args['posts_per_page'] = $this->getTableSetting($settings, 'lazy_load_length', false);
		
		return $args;
	}

	public function removeSSPQueryFilters() {
		remove_filter('posts_clauses', array($this, 'setClausesTaxOrder'), 10, 2);
	}

	public function setClausesTaxOrder( $clauses, $wp_query ) {
		if (!isset($wp_query->query['wtbp_ssp'])) {
			return $clauses;
		}
		global $wpdb;
		$i = 0;

		if (isset($wp_query->query['wtbp_tax_query'])) {
			foreach ($wp_query->query['wtbp_tax_query'] as $tax) {
				$i++;
				$taxonomy = $tax['taxonomy'];
				$name = $tax['terms'];
				$logicAnd = ( 'and' == $tax['logic'] );
				$isPrice = ( '_price' == $taxonomy );
				if (!$isPrice) {
					$list = $this->getTaxonomyTermsByName($taxonomy, $name, false, $logicAnd, $tax['include_children'], $tax['is_ids']);
					if (!$logicAnd && empty($list)) {
						$list = '0';
					}
				}
				$isAttr = strpos($taxonomy, 'pa_') === 0;
				$forParent = ( 'product_cat' == $taxonomy || 'product_tag' == $taxonomy );
				$names = is_array($name) ? $name : array($name);

				if ($isAttr) {
					if ($logicAnd) {
						$l = 0;
						$whereRel = '';
						$whereMeta = '';
						foreach ($list as $value => $term) {
							$l++;
							$n = $i . '_' . $l;
							$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->term_relationships} AS wtbp_rel" . $n . ' ON (wtbp_rel' . $n . ".object_id={$wpdb->posts}.ID and wtbp_rel" . $n . '.term_taxonomy_id=' . $term . ')';
							$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->postmeta} AS wtbp_meta" . $n . " ON ({$wpdb->posts}.ID = wtbp_meta" . $n . '.post_id AND wtbp_meta' . $n . ".meta_key='attribute_" . $taxonomy . "' AND wtbp_meta" . $n . ".meta_value='" . $value . "')";
							$whereRel .= ( empty($whereRel) ? '' : ' AND ' ) . 'wtbp_rel' . $n . '.object_id is not NULL';
							$whereMeta .= ( empty($whereMeta) ? '' : ' AND ' ) . 'wtbp_meta' . $n . '.post_id is not NULL';
						}
						$clauses['where'] = ' AND ((' . $whereRel . ') OR (' . $whereMeta . '))';
					} else {
						$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->term_relationships} AS wtbp_rel" . $i . ' ON (wtbp_rel' . $i . ".object_id={$wpdb->posts}.ID and wtbp_rel" . $i . '.term_taxonomy_id IN (' . $list . '))';
						$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->postmeta} AS wtbp_meta" . $i . " ON ({$wpdb->posts}.ID = wtbp_meta" . $i . '.post_id AND wtbp_meta' . $i . ".meta_key='attribute_" . $taxonomy . "' AND wtbp_meta" . $i . ".meta_value IN ('" . implode("','", $names) . "'))";
						$clauses['where'] .= ' AND (wtbp_rel' . $i . '.object_id is not NULL OR wtbp_meta' . $i . '.post_id is not NULL)';
					}
				} else if ($isPrice) {
					$clauses['join'] .= " INNER JOIN {$wpdb->postmeta} AS wtbp_meta" . $i . " ON ({$wpdb->posts}.ID = wtbp_meta" . $i . '.post_id AND wtbp_meta' . $i . ".meta_key='_price')";
					$where = '';
					foreach ($names as $value) {
						$minMax = explode(',', $value);
						if (count($minMax) == 2) {
							$where .= ( empty($where) ? '' : ' OR ' ) . '(CAST(wtbp_meta' . $i . ".meta_value AS DECIMAL) between '" . ( (float) $minMax[0] ) . "' AND '" . ( (float) $minMax[1] ) . "')";
						}
					}
					$clauses['where'] .= ' AND (' . $where . ')';
				} else {
					if ($logicAnd) {
						$l = 0;
						foreach ($list as $value) {
							$l++;
							$n = $i . '_' . $l;
							$clauses['join'] .= " INNER JOIN {$wpdb->term_relationships} AS wtbp_rel" . $n . ' ON (wtbp_rel' . $n . '.object_id=' .
								( $forParent ? "IF(post_type='product',{$wpdb->posts}.ID,{$wpdb->posts}.post_parent)" : "{$wpdb->posts}.ID" ) . ' and wtbp_rel' . $n . '.term_taxonomy_id=' . $value . ')';
						}
					} else {
						$clauses['join'] .= " INNER JOIN {$wpdb->term_relationships} AS wtbp_rel" . $i . ' ON (wtbp_rel' . $i . '.object_id=' .
							( $forParent ? "IF(post_type='product',{$wpdb->posts}.ID,{$wpdb->posts}.post_parent)" : "{$wpdb->posts}.ID" ) . ' and wtbp_rel' . $i . '.term_taxonomy_id IN (' . $list . '))';
					}
				}
			}
			$clauses['groupby'] = "{$wpdb->posts}.ID";
			unset($wp_query->query['wtbp_tax_query']);
		}

		if (isset($wp_query->query['wtbp_meta_query'])) {
			$filterMetaProductIdList = $this->devideMetaFilters($wp_query->query['wtbp_meta_query']);
			$filterMetaProductIdList = array_map('array_unique', $filterMetaProductIdList);

			$productIdCommonList = array();
			foreach ($filterMetaProductIdList as $productIdList) {
				$productIdCommonList = array_merge($productIdList, $productIdCommonList);
			}

			$productIdQuery = $this->applyAndFilterLogic($filterMetaProductIdList, $productIdCommonList);

			if (!empty($productIdQuery)) {
				$productIdQuery = implode(', ', $productIdQuery);
				$clauses['where'] .= " AND {$wpdb->posts}.ID IN(" . $productIdQuery . ')';
			} else {
				$clauses['where'] .= " AND {$wpdb->posts}.ID IN(0)";
			}
		}

		// search by table columns
		if (isset($wp_query->query['wtbp_search']) && empty($wp_query->query['wtbp_search_main']) ) {
			$clauses = $this->getSearchClauses($clauses, $wp_query->query['wtbp_search'], true);

			unset($wp_query->query['wtbp_search']);
		}

		// main search
		if (!empty($wp_query->query['wtbp_search_main']) ) {
			$clauses = $this->getSearchClauses($clauses, $wp_query->query['wtbp_search_main'], false);

			unset($wp_query->query['wtbp_search_main']);
		}

		if (empty($wp_query->query['orderby']) || !is_string($wp_query->query['orderby'])) {
			return $clauses;
		}

		if (strpos($wp_query->query['orderby'], 'wtbp-') === 0) {
			$orderby = substr($wp_query->query['orderby'], 5);
			$taxonomy = $orderby;
			$needed = false;
			if ('featured' == $orderby) {
				$taxonomy = 'product_visibility';
				$needed = array('featured');
			}
			$all = ( false == $needed );
			$terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true));
			$list = '';
			if (is_array($terms)) {
				foreach ($terms as $term) {
					if ($all || in_array($term->slug, $needed)) {
						$list .= $term->term_taxonomy_id . ',';
					}
				}
			}
			$byName = ( 'featured' == $orderby ? false : true );
			if (!empty($list)) {
				$isAttr = strpos($taxonomy, 'pa_') === 0;
				$forParent = ( 'product_cat' == $taxonomy );

				$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->term_relationships} AS wtbp_rel ON (wtbp_rel.object_id=" .
					( $forParent ? "IF(post_type='product',{$wpdb->posts}.ID,{$wpdb->posts}.post_parent)" : "{$wpdb->posts}.ID" ) . ' and wtbp_rel.term_taxonomy_id IN (' . substr($list, 0, -1) . '))';

				if ($byName) {
					$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->term_taxonomy} AS wtbp_tax ON (wtbp_rel.term_taxonomy_id = wtbp_tax.term_taxonomy_id)";
					$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->terms} wtbp_terms USING (term_id)";
					if ($isAttr) {
						$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->postmeta} AS wtbp_meta ON ({$wpdb->posts}.ID = wtbp_meta.post_id AND wtbp_meta.meta_key='attribute_" . $taxonomy . "')";
					}
					$clauses['groupby'] = "{$wpdb->posts}.ID";
					if ($isAttr) {
						$clauses['orderby'] = 'GROUP_CONCAT(IFNULL(wtbp_terms.name, wtbp_meta.meta_value) ORDER BY name ASC) ';
					} else {
						$clauses['orderby'] = 'GROUP_CONCAT(wtbp_terms.name ORDER BY name ASC) ';
					}
				} else {
					$clauses['orderby'] = 'term_taxonomy_id ';
				}
				$clauses['orderby'] .= $wp_query->query['order'] . ", {$wpdb->posts}.ID ASC";
			}
		} elseif (strpos($wp_query->query['orderby'], 'acf-') === 0) {
			$meta               = substr($wp_query->query['orderby'], 4);
			$acfFieldSetting    = acf_get_field($meta);
			$clauses['join']   .= " LEFT OUTER JOIN {$wpdb->postmeta} AS wtbp_meta ON ({$wpdb->posts}.ID = wtbp_meta.post_id AND wtbp_meta.meta_key='" . $meta . "')";
			$clauses['orderby'] = ( ( isset($acfFieldSetting['type']) && 'number' === $acfFieldSetting['type'] ) ? 'CAST(wtbp_meta.meta_value AS UNSIGNED)' : 'wtbp_meta.meta_value' ) . ' ' . $wp_query->query['order'] . ", {$wpdb->posts}.ID ASC";
		}

		return $clauses;
	}

	public function getTaxonomyTermsByName( $taxonomy, $value, $like = false, $arr = false, $children = false, $isId = false ) {
		$terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true));
		$list = array();
		if (is_array($terms) && !empty($value)) {
			$values = is_array($value) ? $value : array($value);
			foreach ($terms as $term) {
				foreach ($values as $value) {
					$value = htmlspecialchars( $value );
					$key   = $isId ? $term->term_id : htmlspecialchars( $term->name );
					if ( $like ? stripos( $key, $value ) !== false : $key == $value ) {
						if ($like) {
							$list[] = $term->term_taxonomy_id;
						} else {
							$list[$value] = $term->term_taxonomy_id;
						}
						if ($children) {
							$childs = get_terms(array('taxonomy' => $taxonomy, 'child_of' => $term->term_taxonomy_id));
							if (is_array($childs)) {
								foreach ($childs as $child) {
									$list[$child->name] = $child->term_taxonomy_id;
								}
							}
						}
					}
				}
			}
		}
		return $arr ? $list : implode(',', $list);
	}

	public function customizeCartButton( $settings ) {
		$optionList = array();
		if (!$this->getTableSetting($settings, 'use_cart_styles', false)) {
			return $optionList;
		}

		$styles = $this->getTableSetting($settings, 'cart_styles', array());
		$buttonType = $this->getTableSetting($styles, 'type', 'text');
		if ('icon' == $buttonType) {
			$icon = $this->getTableSetting($styles, 'icon', false);

			if (false !== $icon) {
				add_filter('woocommerce_product_add_to_cart_text', array($this, 'replaceAddToCartText'), 20, 1);
				$this->addToCartText = 'wtbp';

				$this->addToCartIcon = $icon;
				add_action('woocommerce_loop_add_to_cart_link', array($this, 'replaceAddToCartIcon'), 20, 2);
			}
		} elseif ('text' == $buttonType) {
			$productTypesTableList = array(
				'simple',
				'external',
				'grouped',
				'variable_attr_visible',
				'variable_attr_hidden',
			);
			$allText = $this->getTableSetting($styles, 'text');
			if ($this->getTableSetting($settings, 'cart_styles_button_text_product_types', false)) {
				foreach ($productTypesTableList as $productType) {
					$text = $this->getTableSetting($styles, 'text_' . $productType);
					if (!empty($text)) {
						$optionList[$productType] = $text;
					}
				}
			}
			$setHook = false;
			if (!empty($optionList)) {
				$view = FrameWtbp::_()->getModule('wootablepress')->getView();
				if (property_exists($view, 'loopProductType')) {
					$optionList['all'] = $allText;
					$optionList['hideVariation'] = false;
					$orders = $view->orderColumns;
				
					foreach ($orders as $column) {
						if ('add_to_cart' == $column['main_slug']) {
							$optionList['hideVariation'] = $this->getTableSetting($column, 'add_to_cart_hide_variation_attribute', false);
							break;
						}
					}
					add_filter('woocommerce_product_add_to_cart_text', array($this, 'replaceAddToCartTextByType'), 20, 1);
					add_filter('add_to_cart_text', array($this, 'replaceAddToCartText'), 20, 1);
					$this->addToCartText = $optionList;
					$setHook = true;
				}

			}
			if (!$setHook && !empty($allText)) {
				add_filter('woocommerce_product_add_to_cart_text', array($this, 'replaceAddToCartText'), 20, 1);
				add_filter('add_to_cart_text', array($this, 'replaceAddToCartText'), 20, 1);
				$this->addToCartText = $allText;
			}
			
		}
		return $optionList;
	}

	public function replaceAddToCartTextByType( $text ) {
		$productType = FrameWtbp::_()->getModule('wootablepress')->getView()->loopProductType;
		$options = $this->addToCartText;

		if ( 'variable' == $productType  ) {
			$productType .= $options['hideVariation'] ? '_attr_hidden' : '_attr_visible';
		} elseif ( 'variation' == $productType ) {
			$productType = 'simple';
		}
		if (!empty($options[$productType])) {
			return $options[$productType];
		}
		if (!empty($options['all'])) {
			return $options['all'];
		}
		return $text;
	}

	public function replaceAddToCartText( $text ) {
		return $this->addToCartText;
	}

	public function replaceAddToCartTextMPC( $button ) {
		if (!is_null($this->addToCartText) && strpos($button, 'product_type_variable')) {
			$newText = '';
			$options = $this->addToCartText;
			if (is_array($options)) {
				if (!empty($options['variable_attr_visible'])) {
					$newText = $options['variable_attr_visible'];
				} else if (!empty($options['all'])) {
					$newText = $options['all'];
				}
			} else {
				$newText = $options;
			}
			if (!empty($newText)) {
				$endA = strpos($button, '</a>');
				if ($endA) {
					$beginA = strrpos($button, '>', $endA - strlen($button));
					if ($beginA) {
						$beginA++;
						$text = substr($button, $beginA, $endA - $beginA);
						$button = str_replace($text, $newText, $button);
					}
				}
			}
		}
		return $button;
	}

	public function replaceAddToCartIcon( $link, $product ) {
		return str_replace('wtbp', '<i class="fa fa-' . esc_attr($this->addToCartIcon) . '" aria-hidden="true"></i>' , $link);
	}

	public function addHiddenColumns( $order, $settings ) {
		$isSsp = $this->getTableSetting($settings, 'pagination', false) && $this->getTableSetting($settings, 'pagination_ssp', false);
		if ($isSsp) {
			return $order;
		}

		$orderArr = json_decode(stripslashes($order), true);

		$added = false;

		if ($this->getTableSetting($settings, 'filter_attribute', false) && $this->getTableSetting($settings, 'filter_attribute_hide', false)) {
			$attributeIds = $this->getTableSetting($settings, 'filter_attribute_selected', array());
			$allreadyExistArray = array();
			foreach ($orderArr as $item) {
				if (stristr($item['slug'], 'attribute-') != false) {
					$allreadyExistArray[] = $item['slug'];
				}
			}
			foreach ($attributeIds as $attributeId) {
				$slug = 'attribute-' . $attributeId;
				if ( in_array($slug, $allreadyExistArray) ) {
					continue;
				}
				$orderArr[] = array(
					'slug' => 'attribute-' . $attributeId,
					'original_name' => 'colAttrHide',
					'display_name' => '',
					'main_slug' => 'attribute',
					'sub_slug' => '');
				$added = true;
			}
		}

		if ($this->getTableSetting($settings, 'filter_ctax', false) && $this->getTableSetting($settings, 'filter_ctax_hide', false)) {
			$cTaxSlugs = $this->getTableSetting($settings, 'filter_ctax_selected', array());
			$allreadyExistArray = array();

			$module = $this->getModule();
			foreach ($orderArr as $item) {
				if (stristr($item['slug'], $module->ctax_prefix . '-') != false || stristr($item['slug'], $module->acf_prefix . '-') != false) {
					$allreadyExistArray[] = $item['slug'];
				}
			}
			foreach ($cTaxSlugs as $slug) {
				if ( in_array($slug, $allreadyExistArray) ) {
					continue;
				}
				$curPrefix = ( strpos($slug, $module->ctax_prefix . '-') === 0 ? $module->ctax_prefix : $module->acf_prefix );
				$orderArr[] = array(
					'slug' => $slug,
					'original_name' => 'colAttrHide',
					'display_name' => '',
					'main_slug' => $curPrefix,
					'sub_slug' => str_replace($curPrefix . '-', '', $slug));
				$added = true;
			}
		}

		if ($this->getTableSetting($settings, 'filter_category', false) && $this->getTableSetting($settings, 'filter_category_hide', false)) {
			$allreadyExist = false;
			foreach ($orderArr as $item) {
				if ('categories' == $item['slug']) {
					$allreadyExist = true;
					break;
				}
			}
			if (!$allreadyExist) {
				$orderArr[] = array(
					'slug' => 'categories',
					'original_name' => 'colAttrHide',
					'display_name' => '',
					'main_slug' => 'categories',
					'sub_slug' => '');
				$added = true;
			}
		}

		if ($this->getTableSetting($settings, 'filter_tag', false) && $this->getTableSetting($settings, 'filter_tag_hide', false)) {
			$allreadyExist = false;
			foreach ($orderArr as $item) {
				if ('tags' == $item['slug']) {
					$allreadyExist = true;
					break;
				}
			}
			if (!$allreadyExist) {
				$orderArr[] = array(
					'slug' => 'tags',
					'original_name' => 'colAttrHide',
					'display_name' => '',
					'main_slug' => 'tags',
					'sub_slug' => '');
				$added = true;
			}
		}
		return $added ? json_encode($orderArr, JSON_UNESCAPED_UNICODE) : $order;
	}

	/**
	 * Divide meta query by filters with product id they have in their queries
	 *
	 * @param array $filtersMetaQuery
	 *
	 * @return array Filter meta_key as array keys and product id as values
	 */
	public function devideMetaFilters( $filtersMetaQuery ) {
		$filterMetaProductIdList =  array();
		$productIdQueryAndLIst = array();

		foreach ($filtersMetaQuery as $filterData) {
			$productIdQueryOrLIst = array();
			foreach ($filterData['meta_value'] as $filterMeta) {
				$productIdLIst = explode(', ', $filterMeta['productIdList']);
				$productIdQueryOrLIst = array_merge($productIdQueryOrLIst, $productIdLIst);
			}

			if ('and' == $filterData['logic']) {
				$productIdQueryOrLIst = $this->applyAndFilterLogic($filterData['meta_value'], $productIdQueryOrLIst);
			}

			$filterMetaProductIdList[$filterData['meta_key']] = $productIdQueryOrLIst;
		}

		return $filterMetaProductIdList;
	}

	/**
	 * Apply And logic to selected filter paramets
	 *
	 * @param array $filtersSepareteParam choosen filters options list
	 * @param array $filterCommonParam filters choosen options combined into on array
	 *
	 * @return array
	 */
	public function applyAndFilterLogic( $filtersSepareteParam, $filterCommonParam ) {
		$productIdQuery = array();
		$countFilters = count($filtersSepareteParam);
		$filterCommonParam = array_count_values($filterCommonParam);
		foreach ($filterCommonParam as $productId => $countProductId) {
			if ($countFilters == $countProductId) {
				$productIdQuery[] = $productId;
			}
		}

		return $productIdQuery;
	}

	/**
	 * Get where wp_query clauses for search functinality in table in SSP mode
	 *
	 * @param string $clauses
	 * @param array $searchList column search list
	 * @param string $logic
	 *
	 * @return string
	 */
	public function getSearchClauses( $clauses, $searchList, $isAnd ) {
		global $wpdb;

		$where = '';
		$logic = $isAnd ? ' AND ' : ' OR ';
		$join = $isAnd ? ' INNER ' : ' LEFT ';
		$iStr = $isAnd ? '_a' : '_o';
		$i = 0;

		foreach ($searchList as $search) {
			$i++;
			$iterator = $iStr . $i;
			$values = !is_array($search['value']) ? array($search['value']) : $search['value'];
			switch ($search['type']) {
				case 'field':
					$valuesWhere = '';
					foreach ($values as $key => $value) {
						$valuesWhere .= ( $key > 0 ? ' OR' : '' ) . ' ' . $wpdb->prepare("%1s %2s '%3s'", $search['key'], 'LIKE', '%' . $value . '%');
					}
					$where .= ( empty($where) ? '' : $logic ) . '(' . $valuesWhere . ')';

					break;
				case 'meta':
					$clauses['join'] .= $join . "JOIN {$wpdb->postmeta} AS wtbp_meta" . $iterator . " ON ({$wpdb->posts}.ID=wtbp_meta" . $iterator . '.post_id AND wtbp_meta' . $iterator . $wpdb->prepare('.meta_key=%s)', $search['key']);
					$valuesWhere = '';
					foreach ($values as $key => $value) {
						$valuesWhere .= ( $key > 0 ? ' OR' : '' ) . ' wtbp_meta' . $iterator . '.meta_value' . $wpdb->prepare(" %1s '%2s'", 'LIKE', '%' . $value . '%');
					}
					$where .= ( empty($where) ? '' : $logic ) . '(' . $valuesWhere . ')';
					break;
				case 'user':
					$clauses['join'] .= $join . "JOIN {$wpdb->users} AS wtbp_users" . $iterator . " ON ({$wpdb->posts}.post_author=wtbp_users" . $iterator . '.ID) ';
					$valuesWhere = '';
					foreach ($values as $key => $value) {
						$valuesWhere .= ( $key > 0 ? ' OR' : '' ) . ' wtbp_users' . $iterator . '.display_name' . $wpdb->prepare(" %1s '%2s'", 'LIKE', '%' . $value . '%');
					}
					$where .= ( empty($where) ? '' : $logic ) . '(' . $valuesWhere . ')';
					break;
				case 'tax':
					$taxonomy = $search['key'];
					$list = $this->getTaxonomyTermsByName($taxonomy, $values, true);
					$emptyList = empty($list);
					$list = ( $emptyList ? 0 : $list );

					$isAttr = strpos($taxonomy, 'pa_') === 0;
					$forParent = ( 'product_cat' == $taxonomy );
					$listQuery = $isAnd || !$emptyList;

					if ($isAttr) {
						if ($listQuery) {
							$clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} AS wtbp_rel" . $iterator . ' ON (wtbp_rel' . $iterator . ".object_id={$wpdb->posts}.ID and wtbp_rel" . $iterator . '.term_taxonomy_id IN (' . $list . '))';
						}
						$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS wtbp_meta" . $iterator . " ON ({$wpdb->posts}.ID = wtbp_meta" . $iterator . '.post_id AND wtbp_meta' . $iterator . ".meta_key='attribute_" . $taxonomy . "')";
						$valuesWhere = $listQuery ? ' wtbp_rel' . $iterator . '.object_id is not NULL OR ' : '';
						foreach ($values as $key => $value) {
							$valuesWhere .= ( $key > 0 ? ' OR' : '' ) . ' wtbp_meta' . $iterator . '.meta_value' . $wpdb->prepare(" %1s '%2s'", 'LIKE', '%' . $value . '%');
						}
						$where .= ( empty($where) ? '' : $logic ) . '(' . $valuesWhere . ')';
					} elseif ($listQuery) {
						$clauses['join'] .= $join . "JOIN {$wpdb->term_relationships} AS wtbp_rel" . $iterator . ' ON (wtbp_rel' . $iterator . '.object_id=' .
							( $forParent ? "IF(post_type='product',{$wpdb->posts}.ID,{$wpdb->posts}.post_parent)" : "{$wpdb->posts}.ID" ) . ' AND wtbp_rel' . $iterator . '.term_taxonomy_id IN (' . $list . '))';
						if (!$isAnd) {
							$where .= ( empty($where) ? '' : $logic ) . ' (wtbp_rel' . $iterator . '.object_id is not NULL)';
						}
					}
					break;
				default:
					break;
			}
		}

		if (!empty($where)) {
			$clauses['where'] .= ' AND ( ' . $where . ' )';
		}
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		return $clauses;
	}

	//Xoo Quick View button
	private function xoo_qv_button( $id ) {
		if ( is_plugin_active( 'quick-view-woocommerce-premium/xoo-quickview-main.php' ) ) {
			global $xoo_qv_button_text_value, $xoo_qv_btn_icon_value, $xoo_qv_gl_qi_value;
			$html = '<a class="xoo-qv-button" data-qv-id = "' . $id . '">';
			if ( $xoo_qv_btn_icon_value ) {
				$html .= '<span class="xoo-qv-btn-icon xooqv-' . $xoo_qv_gl_qi_value . ' xoo-qv"></span>';
			}
			$html .= $xoo_qv_button_text_value;
			$html .= '</a>';

			return $html;
		} elseif ( is_plugin_active( 'quick-view-woocommerce/xoo-quickview-main.php' ) ) {
			global $xoo_qv_button_text_value, $xoo_qv_btn_icon_value;
			$html = '<a class="xoo-qv-button" qv-id = "' . $id . '">';
			if ( $xoo_qv_btn_icon_value ) {
				$html .= '<span class="xoo-qv-btn-icon xooqv-eye xoo-qv"></span>';
			}
			$html .= esc_attr__( $xoo_qv_button_text_value, 'quick-view-woocommerce' );
			$html .= '</a>';

			return $html;
		}
	}
}
