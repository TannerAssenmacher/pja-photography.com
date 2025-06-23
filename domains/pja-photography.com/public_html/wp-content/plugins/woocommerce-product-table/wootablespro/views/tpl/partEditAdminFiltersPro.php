<?php
$settings = $this->getTableSetting($this->settings, 'settings', array());
$isFilter = $this->getTableSetting($settings, 'filter_attribute', false);
?>
<div class="setting-wrapper">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Attribute filter', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr('<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Attribute filter. Works only with enabled attribute columns. <a href="https://woobewoo.com/documentation/product-attribute-and-category-filters" target="_blank">Read more.</a>', 'woo-product-tables') . '</div><img src="' . esc_url($this->image_path . 'img/filter_attr.png') . '" height="49"></div>'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_attribute]', array(
				'checked' => $isFilter
			));
			?>
	</div>
</div>
<?php 
$productAttr = wc_get_attribute_taxonomies();
$attrDisplay = array();
foreach ($productAttr as $attr) {
	$attrDisplay[$attr->attribute_id] = $attr->attribute_label;
}
$attributeOrder = $this->getTableSetting($settings, 'filter_attribute_selected_order', '');
if (!empty($attributeOrder) && !empty($attrDisplay)) {
	$attributeOrder = explode(',', $attributeOrder);
	$attrDisplay    = array_replace(array_flip($attributeOrder), $attrDisplay);
}
$hiddenClass = ( $isFilter ? '' : 'wtbpHidden' );
?>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_attribute]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Attribute filter title', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Set it if you want to replace titles for all filtered attributes.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[filter_attribute_title]', array(
				'value' => ( isset($settings['filter_attribute_title']) ? $settings['filter_attribute_title'] : '' )
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_attribute]" data-use-sortable="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Select attributes', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Select attributes to filtering.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php 
		if ($attrDisplay) {
			HtmlWtbp::selectlist('settings[filter_attribute_selected][]', array(
				'options' => $attrDisplay,
				'value' => $this->getTableSetting($settings, 'filter_attribute_selected', ''),
				'attrs' => ' class="woobewoo-flat-input"'
			));
			HtmlWtbp::hidden('settings[filter_attribute_selected_order]', array(
				'value' => is_array($attributeOrder) ? implode(',', $attributeOrder) : $attributeOrder,
				'attrs' => ' class="woobewoo-selected-order"'
			));
		} else {
			echo '<div class="settings-info">' . esc_html__('There are no attributes in woocommerce store', 'woo-product-tables') . '</div>';
		}
		?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_attribute]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Hide searching attributes from table', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Hide attribute column(s) and keep the filter to display. You don’t need to add attributes as a column to make filters available. Selected filters will be displayed. If you will add some attribute as a column manually, it will not be hidden even if “Hide searching attributes from a table” is enabled.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_attribute_hide]', array(
				'checked' => $this->getTableSetting($settings, 'filter_attribute_hide', false),
			));
			?>
	</div>
</div>
<?php $showType = $this->getTableSetting($settings, 'filter_attribute_type', 'dropdown'); ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_attribute]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Show as', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_attribute_type]', array(
				'options' => array('dropdown' => 'dropdown', 'multi' => 'multiple dropdown'),
				'value' => $showType,
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $classHidden = !$isFilter || 'dropdown' != $showType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_attribute_type]" data-main-value="dropdown">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Search for a specific attribute', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr('<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Finds all products where the attribute exactly matches the search criteria.', 'woo-product-tables') . '</div><img src="' . esc_url($this->image_path . 'img/filter_attr_specific.png') . '" height="250"></div>'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_attribute_specific]', array(
				'checked' => $this->getTableSetting($settings, 'filter_attribute_specific', false)
			));
			?>
	</div>
</div>
<?php $classHidden = !$isFilter || 'multi' != $showType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_attribute_type]" data-main-value="multi">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Logic', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_attribute_logic]', array(
				'options' => array('or' => 'or', 'and' => 'and'),
				'value' => $this->getTableSetting($settings, 'filter_attribute_logic', 'or'),
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $orderCustom = $this->getTableSetting($settings, 'filter_attribute_order_custom', false); ?>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	 data-main="settings[filter_attribute]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Custom order for terms', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Will sorting attribute terms by Woocommerce custom sorting', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[filter_attribute_order_custom]', array(
				'checked' => $this->getTableSetting($settings, 'filter_attribute_order_custom', false)
			));
			?>
	</div>
</div>

<?php 
	$isFilter = $this->getTableSetting($this->settings['settings'], 'filter_tag', false);
	$hiddenClass = $isFilter ? '' : 'wtbpHidden';
?>
<div class="setting-wrapper">
	<div class="setting-label">
		<label>
			<?php 
			esc_html_e('Tags filter', 'woo-product-tables');
			$tooltip = '<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Tags filter. Works only with enabled tags column.', 'woo-product-tables') . '</div></div>';
			?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr($tooltip); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_tag]', array(
				'checked' => $isFilter
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_tag]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Tags filter title', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Display as a default option of dropdown tags filter.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[filter_tag_title]', array(
				'value' => ( isset($settings['filter_tag_title']) ? $settings['filter_tag_title'] : '' ),
				'attrs' => 'placeholder="' . __('Tag', 'woo-product-tables') . '"',
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_tag]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Hide tags from table', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Hide tags column and keep the filter to display. You don’t need to add Tags as a column to make filter available. If you will add Tags as a column manually, it will not be hidden even if “Hide tags from table” is enabled.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_tag_hide]', array(
				'checked' => $this->getTableSetting($settings, 'filter_tag_hide', false),
			));
			?>
	</div>
</div>
<?php $showType = $this->getTableSetting($settings, 'filter_tag_type', 'dropdown'); ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_tag]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Show as', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_tag_type]', array(
				'options' => array('dropdown' => 'dropdown', 'multi' => 'multiple dropdown'),
				'value' => $showType,
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $classHidden = !$isFilter || 'multi' != $showType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_tag_type]" data-main-value="multi">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Logic', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_tag_logic]', array(
				'options' => array('or' => 'or', 'and' => 'and'),
				'value' => $this->getTableSetting($settings, 'filter_tag_logic', 'or'),
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $isUseSelected = $this->getTableSetting($settings, 'filter_tag_use_selected', false); ?>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	 data-main="settings[filter_tag]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Use only some tags', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Show in filter only selected tags', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[filter_tag_use_selected]', array(
				'checked' => $isUseSelected,
			));
			?>
	</div>
</div>
<?php
	$productTags = get_terms( 'product_tag', array(
		'orderby' => 'name',
		'order' => 'asc',
		'hide_empty' => false,
		'parent' => 0
	));
	$tagsDisplay = array();
	foreach ($productTags as $item) {
		$tagsDisplay[$item->term_id] = $item->name;
	}
	$tagOrder = $this->getTableSetting($settings, 'filter_tag_selected_order', '');
	if (!empty($tagOrder) && !empty($tagsDisplay)) {
		$tagOrder    = explode(',', $tagOrder);
		$tagsDisplay = array_replace(array_flip($tagOrder), $tagsDisplay);
	}
	$classHidden = !$isFilter || !$isUseSelected ? 'wtbpHidden' : '';
	?>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($classHidden); ?>"
	 data-main="settings[filter_tag_use_selected]" data-use-sortable="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Select tags', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Select tags to filtering.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
		if ($tagsDisplay) {
			HtmlWtbp::selectlist('settings[filter_tag_selected][]', array(
				'options' => $tagsDisplay,
				'value' => $this->getTableSetting($settings, 'filter_tag_selected', ''),
				'attrs' => ' class="woobewoo-flat-input"'
			));
		} else {
			echo '<div class="settings-info">' . esc_html__('There are no tags in woocommerce store', 'woo-product-tables') . '</div>';
		}
		?>
		<?php
			HtmlWtbp::hidden('settings[filter_tag_selected_order]', array(
				'value' => is_array($tagOrder) ? implode(',', $tagOrder) : $tagOrder,
				'attrs' => ' class="woobewoo-selected-order"'
			));
			?>
	</div>
</div>

<?php 
$columns = isset($this->columns) ? $this->columns : $this->getModule()->addFullColumnList(array());
$isFilter    = $this->getTableSetting($this->settings['settings'], 'filter_ctax', false);
$hiddenClass = ( $isFilter ? '' : 'wtbpHidden' );
?>
<div class="setting-wrapper">
	<div class="setting-label">
		<label>
			<?php
			esc_html_e('Custom taxonomy filter', 'woo-product-tables'); 
			/* translators: 1: link for Custom Post Type UI 2: link ACF 3: link for Read more */
			$tooltip = '<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . sprintf(__('Display filters for custom taxonomy created with the help of %1$s and %2$s plugins. %3$s', 'woo-product-tables'), '<a href="https://woobewoo.com/documentation/how-to-add-custom-taxonomy-to-the-table" target="_blank">Custom Post Type UI</a>', '<a href="https://woobewoo.com/documentation/how-to-add-custom-taxonomy-to-the-table" target="_blank">ACF</a>', '<a href="https://woobewoo.com/documentation/product-attribute-and-category-filters" target="_blank">' . __('Read more', 'woo-product-tables') . '</a>') . '</div></div>';
			?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr($tooltip); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_ctax]', array(
				'checked' => $isFilter
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_ctax]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Custom taxonomies filter title', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Set it if you want to replace titles for all custom taxonomies.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[filter_ctax_title]', array(
				'value' => ( isset($settings['filter_ctax_title']) ? $settings['filter_ctax_title'] : '' ),
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_ctax]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Select taxonomies', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Select custom taxonomies to filtering.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php 
		$taxonomies = array();
		$excludeTaxList = $this->getModule()->getExceptionFilterTaxonomies();
		foreach ($columns as $column) {
			if (!empty($column['is_custom']) && ! in_array($column['type'], $excludeTaxList)) {
				$taxonomies[$column['slug']] = $column['name'];
			}
		}

		$taxonomies = DispatcherWtbp::applyFilters(
			'getFilterCustomTaxonomies',
			$taxonomies,
			$this->settings['settings']
		);

		if ($taxonomies) {
			$ctaxSelected = isset($this->settings['settings']['filter_ctax_selected']) ? $this->settings['settings']['filter_ctax_selected'] : '';
			$ctaxSelected = DispatcherWtbp::applyFilters(
				'getFilterCustomTaxonomiesSelected',
				$ctaxSelected,
				$this->settings['settings']
			);
			HtmlWtbp::selectlist('settings[filter_ctax_selected][]', array(
				'options' => $taxonomies,
				'value'   => $ctaxSelected,
				'attrs'   => ' class="woobewoo-flat-input"'
			));
		} else {
			echo '<div class="settings-info">' . esc_html__('There are no custom taxonomies in woocommerce store', 'woo-product-tables') . '</div>';
		}
		?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_ctax]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Hide searching attributes from table', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Hide custom taxonomies column(s) and keep the filter to display. You don’t need to add custom taxonomies as a column to make filters available. Selected filters will be displayed. If you will add some custom taxonomies as a column manually, it will not be hidden even if “Hide searching custom taxonomies from a table” is enabled.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_ctax_hide]', array(
				'checked' => $this->getTableSetting($settings, 'filter_ctax_hide', false),
			));
			?>
	</div>
</div>
<?php $showType = $this->getTableSetting($settings, 'filter_ctax_type', 'dropdown'); ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_ctax]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Show as', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_ctax_type]', array(
				'options' => array('dropdown' => 'dropdown', 'multi' => 'multiple dropdown'),
				'value' => $showType,
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $classHidden = !$isFilter || 'multi' != $showType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_ctax_type]" data-main-value="multi">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Logic', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_ctax_logic]', array(
				'options' => array('or' => 'or', 'and' => 'and'),
				'value' => $this->getTableSetting($settings, 'filter_ctax_logic', 'or'),
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>

<?php 
$isFilter = $this->getTableSetting($this->settings['settings'], 'filter_price', false);
$hiddenClass = $isFilter ? '' : 'wtbpHidden';
?>
<div class="setting-wrapper">
	<div class="setting-label">
		<label>
			<?php 
			esc_html_e('Price filter', 'woo-product-tables');
			$tooltip = '<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Price filter. Works only with enabled price column.', 'woo-product-tables') . '</div></div>';
			?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr($tooltip); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_price]', array(
				'checked' => $isFilter
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_price]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Price filter title', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Display as a title for a dropdown filter price.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[filter_price_title]', array(
				'value' => ( isset($settings['filter_price_title']) ? $settings['filter_price_title'] : '' ),
				'attrs' => 'placeholder="' . __('Price', 'woo-product-tables') . '"',
			));
			?>
	</div>
</div>
<?php $showType = $this->getTableSetting($settings, 'filter_price_type', 'dropdown'); ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_price]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Show as', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_price_type]', array(
				'options' => array('dropdown' => 'dropdown', 'multi' => 'multiple dropdown'),
				'value' => $showType,
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $setType = $this->getTableSetting($settings, 'filter_range_type', 'auto'); ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_price]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Set range', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('For manually settings press the "Setup" button and customize your price range settings. You may increase or decrease the number of steps and set different values for each step.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_range_type]', array(
				'options' => array('auto' => 'automatically', 'manual' => 'manually'),
				'value' => $setType,
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $classHidden = !$isFilter || 'auto' != $setType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_range_type]" data-main-value="auto">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Step', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr('<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Here you may set the value of prise increase step. The default value is set to 20. All the steps are equal. When setting the step, please note that the number of elements in the list should not exceed 100, otherwise the step setting will be reset and automatically calculated.', 'woo-product-tables') . '</div></div>'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::text('settings[filter_range_step]', array(
				'value' => $this->getTableSetting($settings, 'filter_range_step', '20'),
				'attrs' => ' class="woobewoo-flat-input woobewoo-width60"'
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_range_type]" data-main-value="auto">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Min price', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::text('settings[filter_range_min]', array(
				'value' => $this->getTableSetting($settings, 'filter_range_min', ''),
				'attrs' => ' class="woobewoo-flat-input woobewoo-width60"'
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_range_type]" data-main-value="auto">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Max price', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::text('settings[filter_range_max]', array(
				'value' => $this->getTableSetting($settings, 'filter_range_max', ''),
				'attrs' => ' class="woobewoo-flat-input woobewoo-width60"'
			));
			?>
	</div>
</div>
<?php $classHidden = !$isFilter || 'manual' != $setType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_range_type]" data-main-value="manual">
	<div class="setting-label">
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::hidden('settings[filter_range_list]', array(
				'value' => $this->getTableSetting($settings, 'filter_range_list', ''),
				'attrs' => ' id="wtbpRangeList"'
			));
			?>
		<div id="wtbpRangeListSetup" class="button button-small"><?php esc_html_e('Setup range', 'woo-product-tables'); ?></div>
		<div class="wtbpHidden">
			<div class="wtbpRangeListTemplate">
				<div class="wtbpRangeList">
					<div class="wtbpRangeListFrom">
						<?php esc_html_e('From', 'woo-product-tables'); ?>
						<input type="text" name="from" value="">
					</div>
					<div class="wtbpRangeListTo">
						<?php esc_html_e('To', 'woo-product-tables'); ?>
						<input type="text" name="to" value="">
					</div>
					<div class="wtbpRangeListHandler">
						<i class="fa fa-arrows-v"></i>
					</div>
					<div class="wtbpRangeListRemove">
						<i class="fa fa-trash-o"></i>
					</div>
				</div>
			</div>

			<div class="wtbpRangeListButtonTemplate">
				<div class="wtbpRangeListButton">
					<button class="button wtbpAddPriceRange"><?php esc_html_e('Add', 'woo-product-tables'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
$isFilter = $this->getTableSetting($this->settings['settings'], 'filter_category', false);
$hiddenClass = $isFilter ? '' : 'wtbpHidden';
?>
<div class="setting-wrapper">
	<div class="setting-label">
		<label>
			<?php 
			esc_html_e('Category filter', 'woo-product-tables');
			$tooltip = '<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Category filter. Works only with enabled category column.', 'woo-product-tables') . ' <a href="https://woobewoo.com/documentation/product-attribute-and-category-filters/" target="_blank">' . __('Read more', 'woo-product-tables') . '</a></div></div>';
			?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr($tooltip); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_category]', array(
				'checked' => $isFilter
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Filter category title', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Display as a default option of dropdown filter category.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[filter_category_title]', array(
				'value' => ( isset($settings['filter_category_title']) ? $settings['filter_category_title'] : '' ),
				'attrs' => 'placeholder="' . __('Category', 'woo-product-tables') . '"',
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Hide categories from table', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Hide categories column and keep the filter to display. You don’t need to add Categories as a column to make filter available. If you will add Categories as a column manually, it will not be hidden even if “Hide categories from table” is enabled.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[filter_category_hide]', array(
				'checked' => $this->getTableSetting($settings, 'filter_category_hide', false),
			));
			?>
	</div>
</div>
<?php $showType = $this->getTableSetting($settings, 'filter_category_type', 'dropdown'); ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Show as', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[filter_category_type]', array(
				'options' => array('dropdown' => 'dropdown', 'multi' => 'multiple dropdown'),
				'value' => $showType,
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Inner table filter', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Apply filter by category after click category link in table column', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[filter_category_inner_table]', array(
				'checked' => $this->getTableSetting($settings, 'filter_category_inner_table', false),
			));
			?>
	</div>
</div>
<?php $classHidden = !$isFilter || 'multi' != $showType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
	data-main="settings[filter_category_type]" data-main-value="multi">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Logic', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_category_logic]', array(
				'options' => array('or' => 'or', 'and' => 'and'),
				'value' => $this->getTableSetting($settings, 'filter_category_logic', 'or'),
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Filter position', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr('<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Filter position.', 'woo-product-tables') . '</div><img src="' . esc_url($this->image_path . 'img/filter_cat_position.png') . '" height="56"></div>'); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php 
			HtmlWtbp::selectbox('settings[filter_category_position]', array(
				'options' => array('before' => 'before', 'after' => 'after'),
				'value' => ( isset($this->settings['settings']['filter_category_position']) ? $this->settings['settings']['filter_category_position'] : '' ),
				'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $isUseSelected = $this->getTableSetting($settings, 'filter_category_use_selected', false); ?>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	 data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Use only some categories', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Show in filter only selected categories', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[filter_category_use_selected]', array(
				'checked' => $isUseSelected,
			));
			?>
	</div>
</div>
<?php
	$productCats = get_categories( array(
		'taxonomy'     => 'product_cat',
		'orderby'      => 'name',
		'order'        => 'ASC',
		'hide_empty'   => 0,
		'pad_counts'   => 0
	) );
	$catsDisplay = array();
	foreach ($productCats as $attr) {
		$catsDisplay[$attr->term_id] = $attr->name;
	}
	$catOrder = $this->getTableSetting($settings, 'filter_category_selected_order', '');
	if (!empty($catOrder) && !empty($catsDisplay)) {
		$catOrder    = explode(',', $catOrder);
		$catsDisplay = array_replace(array_flip($catOrder), $catsDisplay);
	}
	$classHidden = !$isFilter || !$isUseSelected ? 'wtbpHidden' : '';
	?>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($classHidden); ?>"
	 data-main="settings[filter_category_use_selected]" data-use-sortable="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Select categories', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Select categories to filtering.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
		if ($catsDisplay) {
			HtmlWtbp::selectlist('settings[filter_category_selected][]', array(
				'options' => $catsDisplay,
				'value' => $this->getTableSetting($settings, 'filter_category_selected', ''),
				'attrs' => ' class="woobewoo-flat-input"'
			));
		} else {
			echo '<div class="settings-info">' . esc_html__('There are no categories in woocommerce store', 'woo-product-tables') . '</div>';
		}
		?>
		<?php
			HtmlWtbp::hidden('settings[filter_category_selected_order]', array(
				'value' => is_array($catOrder) ? implode(',', $catOrder) : $catOrder,
				'attrs' => ' class="woobewoo-selected-order"'
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Include children', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_category_children]', array(
				'checked' => $this->getTableSetting($settings, 'filter_category_children', false),
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
	data-main="settings[filter_category]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Use as main filter', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Turn on if you want the category filter to dynamically affect the content of attribute filters.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[filter_category_relations]', array(
				'checked' => $this->getTableSetting($settings, 'filter_category_relations', false),
			));
			?>
	</div>
</div>
<div class="setting-wrapper">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Hide products before filtering', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Hide all the products in the table until a user defines a search parameter or filter.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php 
			HtmlWtbp::checkboxToggle('settings[hide_before_filtering]', array(
				'checked' => $this->getTableSetting($settings, 'hide_before_filtering', false)
			));
			?>
	</div>
</div>
