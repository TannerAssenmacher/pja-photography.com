<div class="woobewoo-form-group">
	<div class="woobewoo-check-group">
		<?php 
			HtmlWtbp::checkbox('settings[user_products]', array(
			'checked' => ( isset($this->settings['settings']['user_products']) ? (int) $this->settings['settings']['user_products'] : '' )
			));
			?>
		<label><?php esc_html_e('Displays the user\'s products', 'woo-product-tables'); ?></label>
		<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Displays previously purchased products of the user', 'woo-product-tables'); ?>"></i>
	</div>
</div>
