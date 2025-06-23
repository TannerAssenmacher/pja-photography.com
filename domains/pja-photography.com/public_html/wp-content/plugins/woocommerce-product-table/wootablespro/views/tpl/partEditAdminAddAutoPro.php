<div class="woobewoo-form-group">
	<div class="woobewoo-check-group">
		<?php 
			HtmlWtbp::checkbox('settings[auto_categories_enable]', array(
			'checked' => ( isset($this->settings['settings']['auto_categories_enable']) ? (int) $this->settings['settings']['auto_categories_enable'] : '' )
			));
			?>
		<label><?php esc_html_e('Add products automatically', 'woo-product-tables'); ?></label>
		<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('If you turned on the flag "Add products automatically" and selected all categories in the list, then there is no need to select products in the table below - all products will automatically be displayed in the table on the front.', 'woo-product-tables'); ?>"></i>
	</div>
	<div class="woobewoo-input-group">
		<?php
			HtmlWtbp::hidden('settings[auto_categories_list]', array(
			'value' => ( isset($this->settings['settings']['auto_categories_list']) ? $this->settings['settings']['auto_categories_list'] : '' )
			)); 
			?>
		<select id="wtbpAutoCategoriesList" value=""
			data-placeholder="<?php echo esc_attr(__('Select category', 'woo-product-tables')); ?>..."
			data-all-selected="<?php echo esc_attr(__('All selected', 'woo-product-tables')); ?>"
			data-select-all-text="<?php echo esc_attr(__('Select all', 'woo-product-tables')); ?>">
				<?php HtmlWtbp::echoEscapedHtml($this->categories_html); ?>	
		</select>
		<button id="wtbpAutoAddProducts" class="button button-small"><span><?php esc_html_e('Done', 'woo-product-tables'); ?></span></button>
	</div>
</div>
<div class="woobewoo-form-group">
	<div class="woobewoo-check-group">
		<?php
		HtmlWtbp::checkbox('settings[auto_variations_enable]', array(
			'checked' => ( isset($this->settings['settings']['auto_variations_enable']) ? (int) $this->settings['settings']['auto_variations_enable'] : '' )
		));
		?>
		<label><?php esc_html_e('Add products variations automatically', 'woo-product-tables'); ?></label>
		<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('If you turned on the flag "Add products variations automatically" and selected all products in the list, then there is no need to select products in the table below - all products variations will automatically be displayed in the table on the front.', 'woo-product-tables'); ?>"></i>
	</div>
	<div class="woobewoo-input-group">
		<?php
		HtmlWtbp::hidden('settings[auto_variations_list]', array(
			'value' => ( isset($this->settings['settings']['auto_variations_list']) ? $this->settings['settings']['auto_variations_list'] : '' )
		));
		?>
		<select id="wtbpAutoVariationsList" value=""
				data-placeholder="<?php echo esc_attr(__('Select product', 'woo-product-tables')); ?>..."
				data-all-selected="<?php echo esc_attr(__('All selected', 'woo-product-tables')); ?>"
				data-select-all-text="<?php echo esc_attr(__('Select all', 'woo-product-tables')); ?>">
			<?php HtmlWtbp::echoEscapedHtml($this->products_has_variations_html); ?>
		</select>
		<button id="wtbpAutoAddVariations" class="button button-small"><span><?php esc_html_e('Done', 'woo-product-tables'); ?></span></button>
	</div>
</div>
<div class="woobewoo-form-group">
	<div class="woobewoo-check-group">
		<?php 
			HtmlWtbp::checkbox('settings[filter_dynamically]', array(
			'checked' => ( isset($this->settings['settings']['filter_dynamically']) ? (int) $this->settings['settings']['filter_dynamically'] : '' )
			));
			?>
		<label><?php esc_html_e('Filter products dynamically based on page type', 'woo-product-tables'); ?></label>
		<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('If enabled, then on the category page only products of this category will be filtered from the list, on the tag page - only products of this tags, on the attribute page - products with this attribute, and on the product page - variations of this product.', 'woo-product-tables'); ?>"></i>
	</div>
</div>
