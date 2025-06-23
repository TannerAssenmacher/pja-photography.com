<?php
$settings = $this->getTableSetting($this->settings, 'settings', array());
$fontsList = array_merge($this->getModule()->getFontsList(), $this->getModule()->getStandardFontsList());
natsort($fontsList);
$defaultFont = $this->getModule()->defaultFont;
array_unshift($fontsList, $defaultFont);

$useCustomStyles = $this->getTableSetting($settings, 'use_custom_styles', false);
$wtbpHiddenStyles = $useCustomStyles ? '' : 'wtbpHidden';
$styles = $this->getTableSetting($settings, 'styles', array());
?>
<div class="setting-wrapper setting-wrapper-inline">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Use custom table styles', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Choose your custom table styles below. Any settings you leave blank will default to your theme styles.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php HtmlWtbp::checkboxToggle('settings[use_custom_styles]', array('checked' => $useCustomStyles)); ?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Borders external', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php HtmlWtbp::colorpickerCompact('settings[styles][external_border_color]', $this->getTableSetting($styles, 'external_border_color', '')); ?>
		<label class="wtbpInnerLabel"><?php esc_html_e('width', 'woo-product-tables'); ?></label>
		<?php
			HtmlWtbp::text('settings[styles][external_border_width]', array(
				'value' => $this->getTableSetting($styles, 'external_border_width', ''),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Borders header', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[styles][header_border_color]',
				$this->getTableSetting($styles, 'header_border_color', '')
			);
			?>
		<label class="wtbpInnerLabel"><?php esc_html_e('width', 'woo-product-tables'); ?></label>
		<?php
			HtmlWtbp::text('settings[styles][header_border_width]', array(
				'value' => $this->getTableSetting($styles, 'header_border_width', ''),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Borders rows', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[styles][row_border_color]',
				$this->getTableSetting($styles, 'row_border_color', '')
			);
			?>
		<label class="wtbpInnerLabel"><?php esc_html_e('width', 'woo-product-tables'); ?></label>
		<?php
			HtmlWtbp::text('settings[styles][row_border_width]', array(
				'value' => $this->getTableSetting($styles, 'row_border_width', ''),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Borders columns', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[styles][column_border_color]',
				$this->getTableSetting($styles, 'column_border_color', '')
			);
			?>
		<label class="wtbpInnerLabel"><?php esc_html_e('width', 'woo-product-tables'); ?></label>
		<?php
			HtmlWtbp::text('settings[styles][column_border_width]', array(
				'value' => $this->getTableSetting($styles, 'column_border_width', ''),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Header background', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[styles][header_bg_color]',
				$this->getTableSetting($styles, 'header_bg_color', '')
			);
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Header font', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<div class="wtbpStyleGroup">
			<?php
				HtmlWtbp::selectbox('settings[styles][header_font_family]', array(
					'options' => $fontsList,
					'value' => $this->getTableSetting($styles, 'header_font_family', $defaultFont),
					'values' => 'labels')
				);
				?>
		</div>
		<?php
			HtmlWtbp::colorpickerCompact('settings[styles][header_font_color]',
				$this->getTableSetting($styles, 'header_font_color', '')
			);
			?>
		<label class="wtbpInnerLabel"><?php esc_html_e('size', 'woo-product-tables'); ?></label>
		<?php
			HtmlWtbp::text('settings[styles][header_font_size]', array(
				'value' => $this->getTableSetting($styles, 'header_font_size', ''),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption setting-wrapper-block wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Horizontal alignment for header', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set horizontal alignment of table header contents.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[styles][header_hor_alignment]', array(
			'options' => array(
				'' => __('None', 'woo-product-tables'),
				'left' => __('Left', 'woo-product-tables'),
				'center' => __('Center', 'woo-product-tables'),
				'right' => __('Right', 'woo-product-tables')),
			'value' => $this->getTableSetting($styles, 'header_hor_alignment', '')
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Cell background', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[styles][cell_bg_color]', $this->getTableSetting($styles, 'cell_bg_color', ''));
			HtmlWtbp::hidden('settings[styles][cell_color_even]', array('value' => $this->getTableSetting($styles, 'cell_color_even', '')));
			HtmlWtbp::hidden('settings[styles][cell_color_hover]', array('value' => $this->getTableSetting($styles, 'cell_color_hover', '')));
			HtmlWtbp::hidden('settings[styles][cell_color_order]', array('value' => $this->getTableSetting($styles, 'cell_color_order', '')));
		?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Cell font', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<div class="wtbpStyleGroup">
		<?php
			HtmlWtbp::selectbox('settings[styles][cell_font_family]', array(
				'options' => $fontsList,
				'value' => $this->getTableSetting($styles, 'cell_font_family', $defaultFont),
				'values' => 'labels')
			);
			?>
		</div>
		<?php HtmlWtbp::colorpickerCompact('settings[styles][cell_font_color]', $this->getTableSetting($styles, 'cell_font_color', '')); ?>
		<label class="wtbpInnerLabel"><?php esc_html_e('size', 'woo-product-tables'); ?></label>
		<?php
			HtmlWtbp::text('settings[styles][cell_font_size]', array(
				'value' => $this->getTableSetting($styles, 'cell_font_size', ''),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-wrapper-block setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Horizontal alignment for cells', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set horizontal alignment of table cell contents.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[styles][horizontal_alignment]', array(
				'options' => array(
					'' => __('None', 'woo-product-tables'),
					'left' => __('Left', 'woo-product-tables'),
					'center' => __('Center', 'woo-product-tables'),
					'right' => __('Right', 'woo-product-tables')),
				'value' => $this->getTableSetting($styles, 'horizontal_alignment', '')
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Cell paddings', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set cell paddings in this order: Top Right Bottom Left.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<div class="setting-group-input">
		<?php
			HtmlWtbp::text('settings[styles][cell_padding_top]', array(
				'value' => $this->getTableSetting($styles, 'cell_padding_top', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[styles][cell_padding_right]', array(
				'value' => $this->getTableSetting($styles, 'cell_padding_right', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[styles][cell_padding_bottom]', array(
				'value' => $this->getTableSetting($styles, 'cell_padding_bottom', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[styles][cell_padding_left]', array(
				'value' => $this->getTableSetting($styles, 'cell_padding_left', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		</div>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Search Bar Colors', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set cell paddings in this order: Top Right Bottom Left.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<div class="wtbpStyleGroup">
			<?php
				HtmlWtbp::colorpickerCompact('settings[styles][search_bg_color]',
				$this->getTableSetting($styles, 'search_bg_color', '')
				);
				?>
			<label class="wtbpLastLabel"><?php esc_html_e('background', 'woo-product-tables'); ?></label>
		</div>
		<div class="wtbpStyleGroup">
			<?php
				HtmlWtbp::colorpickerCompact('settings[styles][search_font_color]',
					$this->getTableSetting($styles, 'search_font_color', '')
				);
				?>
			<label class="wtbpLastLabel"><?php esc_html_e('font', 'woo-product-tables'); ?></label>
		</div>
		<div class="wtbpStyleGroup">
			<?php
				HtmlWtbp::colorpickerCompact('settings[styles][search_border_color]',
					$this->getTableSetting($styles, 'search_border_color', '')
				);
				?>
			<label class="wtbpLastLabel"><?php esc_html_e('border', 'woo-product-tables'); ?></label>
		</div>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Fixed Layout', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set all columns of the same width.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[styles][fixed_layout]', array(
				'checked' => $this->getTableSetting($styles, 'fixed_layout', '0')
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Vertical alignment', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set vertical alignment of table cell contents.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[styles][vertical_alignment]', array(
			'options' => array(
				'' => __('None', 'woo-product-tables'),
				'top' => __('Top', 'woo-product-tables'),
				'middle' => __('Middle', 'woo-product-tables'),
				'bottom' => __('Bottom', 'woo-product-tables')),
			'value' => $this->getTableSetting($styles, 'vertical_alignment', '')
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Pagination Position', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set horizontal pagination buttons position.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[styles][pagination_position]', array(
			'options' => array(
				'' => __('None', 'woo-product-tables'),
				'left' => __('Left', 'woo-product-tables'),
				'center' => __('Center', 'woo-product-tables'),
				'right' => __('Right', 'woo-product-tables')),
			'value' => $this->getTableSetting($styles, 'pagination_position', '')
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Show sorting icon on mouse over', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set horizontal pagination buttons position.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[styles][show_sort_hover]', array(
				'checked' => $this->getTableSetting($styles, 'show_sort_hover', '0')
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e("Filter's select tag flexible width", 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__("Set filter's with flexible select tag that will be fitted most long option name.", 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[styles][filter_select_flixible]', array(
				'checked' => $this->getTableSetting($styles, 'filter_select_flixible', '0')
			));
			?>
	</div>
</div>

<div class="setting-wrapper setting-wrapper-inline setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>" data-main="settings[use_custom_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Popup size', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__("You can find popup in 'Short description' and 'Summary' table columns.", 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[styles][column_popup_width]', array(
			'value' => ( isset($settings['styles']['column_popup_width']) ? $settings['styles']['column_popup_width'] : '80' ),
			'attrs' => 'class="wtbp-small-input"'
			));
			?>
		<?php
			HtmlWtbp::selectbox('settings[styles][column_popup_width_unit]', array(
			'options' => array('px' => 'px', '%' => '%'),
			'value' => ( isset($settings['styles']['column_popup_width_unit']) ? $settings['styles']['column_popup_width_unit'] : '%' ),
			'attrs' => 'class="woobewoo-flat-input wtbp-small-input"'
			));
			?>
	</div>
</div>


<?php
$useCartStyles = $this->getTableSetting($settings, 'use_cart_styles', false);
$wtbpHiddenStyles = $useCartStyles ? '' : 'wtbpHidden';
$styles = $this->getTableSetting($settings, 'cart_styles', array());
?>
<div class="setting-title">
	<?php esc_html_e('Buy Button Styling', 'woo-product-tables'); ?>
</div>
<div class="setting-wrapper setting-wrapper-inline">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Use custom Buy Button styles', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Choose your custom styles for button Add to cart. Any settings you leave blank will default.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php HtmlWtbp::checkboxToggle('settings[use_cart_styles]', array('checked' => $useCartStyles)); ?>
	</div>
</div>
<?php $buttonType = $this->getTableSetting($styles, 'type', 'text'); ?>
<div class="setting-wrapper setting-suboption setting-wrapper-inline wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Button type', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[cart_styles][type]', array(
			'options' => array(
				'text' => __('Text button', 'woo-product-tables'),
				'icon' => __('Icon button', 'woo-product-tables')),
			'value' => $buttonType,
			'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>

<?php $classHidden = !$useCartStyles || 'text' != $buttonType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles][type]" data-main-value="text">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text (any product type)', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Button text for any product type.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text]', array(
				'value' => $this->getTableSetting($styles, 'text', ''),
			));
			?>
	</div>
</div>

<?php $classHidden = !$useCartStyles || 'text' != $buttonType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles][type]" data-main-value="text">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text (after clicking the button)', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Text for the button after clicking on it', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text_onclick]', array(
				'value' => $this->getTableSetting($styles, 'text_onclick', ''),
			));
			?>
	</div>
</div>


<?php $isUsTextProductTypes = $this->getTableSetting($settings, 'cart_styles_button_text_product_types', false); ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	 data-main="settings[cart_styles][type]"  data-main-value="text">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Set text by product types', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Set button text for a specific product types.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[cart_styles_button_text_product_types]', array(
				'checked' => $isUsTextProductTypes,
			));
			?>
	</div>
</div>

<?php $classHidden = ! $useCartStyles || 'text' != $buttonType || ! $isUsTextProductTypes ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles_button_text_product_types]" data-main-value="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text (simple product)', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Button text for simple product type.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text_simple]', array(
				'value' => $this->getTableSetting($styles, 'text_simple', ''),
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles_button_text_product_types]" data-main-value="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text (grouped product)', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Button text for grouped product type.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text_grouped]', array(
				'value' => $this->getTableSetting($styles, 'text_grouped', ''),
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles_button_text_product_types]" data-main-value="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text (external product)', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Button text for external product type.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text_external]', array(
				'value' => $this->getTableSetting($styles, 'text_external', ''),
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles_button_text_product_types]" data-main-value="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text (varibale product with attributes)', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Button text for variable product type with visible attributes.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text_variable_attr_visible]', array(
				'value' => $this->getTableSetting($styles, 'text_variable_attr_visible', ''),
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles_button_text_product_types]" data-main-value="true">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text (varibale product without attributes)', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Button text for variable product type with hidden attributes.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text_variable_attr_hidden]', array(
				'value' => $this->getTableSetting($styles, 'text_variable_attr_hidden', ''),
			));
			?>
	</div>
</div>



<?php $classHidden = !$useCartStyles || 'icon' != $buttonType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-suboption setting-wrapper-inline wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles][type]" data-main-value="icon">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Icon', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[cart_styles][icon]', array(
			'options' => array(
				'cart-plus' => '&#xf217;',
				'cart-arrow-down' => '&#xf218;',
				'shopping-cart' => '&#xf07a;',
				'shopping-basket' => '&#xf291;',
				'shopping-bag' => '&#xf290;',
				'plus' => '&#xf067;',
				'credit-card' => '&#xf09d;'),
			'value' => $this->getTableSetting($styles, 'icon', 'cart-plus'),
			'attrs' => ' class="woobewoo-flat-input wtbp-select-icon"'
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Font', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<div class="wtbpStyleGroup">
			<?php
				HtmlWtbp::selectbox('settings[cart_styles][font_family]', array(
					'options' => $fontsList,
					'value' => $this->getTableSetting($styles, 'font_family', $defaultFont),
					'values' => 'labels',
					'attrs' => ' class="input-left"'
					)
				);
				HtmlWtbp::selectbox('settings[cart_styles][font_weight]', array(
				'options' => array(
					'' => '',
					'n' => 'normal',
					'b' => 'bold',
					'i' => 'italic',
					'bi' => 'bold + italic'),
				'value' => $this->getTableSetting($styles, 'font_weight', ''),
				'attrs' => ' class="input-right woobewoo-flat-input"'
				));
				?>
		</div>
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][font_color]',
				$this->getTableSetting($styles, 'font_color', '')
			);
			?>
		<label class="wtbpInnerLabel"><?php esc_html_e('size', 'woo-product-tables'); ?></label>
		<?php
			HtmlWtbp::text('settings[cart_styles][font_size]', array(
				'value' => $this->getTableSetting($styles, 'font_size', ''),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Font (hover)', 'woo-product-tables'); ?>
			<button type="button" class="wtbpMiniButton wtbpCopyStyles" data-style="font"><i class="fa fa-level-down" aria-hidden="true"></i></button>
		</label>
	</div>
	<div class="setting-input">
		<div class="wtbpStyleGroup">
			<?php
				HtmlWtbp::colorpickerCompact('settings[cart_styles][font_color_hover]',
				$this->getTableSetting($styles, 'font_color_hover', '')
				);
				HtmlWtbp::selectbox('settings[cart_styles][font_weight_hover]', array(
				'options' => array(
					'' => '',
					'n' => 'normal',
					'b' => 'bold',
					'i' => 'italic',
					'bi' => 'bold + italic'),
				'value' => $this->getTableSetting($styles, 'font_weight_hover', ''),
				'attrs' => ' class="input-right woobewoo-flat-input"'
				));
				?>
		</div>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCustomStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Text shadow', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set text shadow in this order: color, X, Y, blur.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][text_shadow_color]',
				$this->getTableSetting($styles, 'text_shadow_color', '')
			);
			?>
		<div class="setting-group-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][text_shadow_x]', array(
				'value' => $this->getTableSetting($styles, 'text_shadow_x', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][text_shadow_y]', array(
				'value' => $this->getTableSetting($styles, 'text_shadow_y', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][text_shadow_blur]', array(
				'value' => $this->getTableSetting($styles, 'text_shadow_blur', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		</div>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-wrapper-inline setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Button size', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set width and height values in pixels (in that order).', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][button_width]', array(
				'value' => $this->getTableSetting($styles, 'button_width', ''),
				'attrs' => 'class="woobewoo-flat-input woobewoo-width60"'
			));
			echo ' x ';
			HtmlWtbp::text('settings[cart_styles][button_height]', array(
				'value' => $this->getTableSetting($styles, 'button_height', ''),
				'attrs' => 'class="woobewoo-flat-input woobewoo-width60"'
			));
			?>
	</div>
</div>
<div class="setting-wrapper setting-suboption setting-wrapper-inline wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Corners radius', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][radius]', array(
				'value' => $this->getTableSetting($styles, 'radius', ''),
				'attrs' => 'class="wtbp-small-input wtbp-mr5"'
			));
			HtmlWtbp::selectbox('settings[cart_styles][radius_unit]', array(
				'options' => array('px' => 'px', '%' => '%'),
				'value' => $this->getTableSetting($styles, 'radius_unit', 'pixels'),
				'attrs' => 'class="woobewoo-flat-input wtbp-small-input"'
			));
			?>
	</div>
</div>

<?php $bgType = $this->getTableSetting($styles, 'background', '', false, array('unicolored', 'bicolored', 'gradient', 'pyramid')); ?>
<div class="setting-wrapper setting-suboption setting-wrapper-inline wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Background type', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[cart_styles][background]', array(
			'options' => array(
				'' => __('none', 'woo-product-tables'),
				'unicolored' => __('unicolored', 'woo-product-tables'),
				'bicolored' => __('bicolored', 'woo-product-tables'),
				'gradient' => __('simple gradient', 'woo-product-tables'),
				'pyramid' => __('pyramid gradient', 'woo-product-tables')),
			'value' => $bgType,
			'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $classHidden = !$useCartStyles || 'unicolored' != $bgType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles][background]" data-main-value="unicolored">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Background color', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][button_color]',
				$this->getTableSetting($styles, 'button_color', '')
			);
			?>
	</div>
</div>
<?php $classHidden = $useCartStyles && ( 'bicolored' == $bgType || 'gradient' == $bgType || 'pyramid' == $bgType ) ? '' : 'wtbpHidden'; ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles][background]" data-main-value="bicolored|gradient|pyramid">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Background colors', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][bg_color1]',
				$this->getTableSetting($styles, 'bg_color1', '')
			);
			?>
		<div class="setting-group-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][bg_color2]',
				$this->getTableSetting($styles, 'bg_color2', '')
			);
			?>
		</div>
	</div>
</div>

<?php $bgType = $this->getTableSetting($styles, 'background_hover', '', false, array('unicolored', 'bicolored', 'gradient', 'pyramid')); ?>
<div class="setting-wrapper setting-suboption setting-wrapper-inline wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Background (hover)', 'woo-product-tables'); ?>
			<button type="button" class="wtbpMiniButton wtbpCopyStyles" data-style="background"><i class="fa fa-level-down" aria-hidden="true"></i></button>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::selectbox('settings[cart_styles][background_hover]', array(
			'options' => array(
				'' => __('none', 'woo-product-tables'),
				'unicolored' => __('unicolored', 'woo-product-tables'),
				'bicolored' => __('bicolored', 'woo-product-tables'),
				'gradient' => __('simple gradient', 'woo-product-tables'),
				'pyramid' => __('pyramid gradient', 'woo-product-tables')),
			'value' => $bgType,
			'attrs' => ' class="woobewoo-flat-input"'
			));
			?>
	</div>
</div>
<?php $classHidden = !$useCartStyles || 'unicolored' != $bgType ? 'wtbpHidden' : ''; ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles][background_hover]" data-main-value="unicolored">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Background color (hover)', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][button_color_hover]',
				$this->getTableSetting($styles, 'button_color_hover', '')
			);
			?>
	</div>
</div>
<?php $classHidden = $useCartStyles && ( 'bicolored' == $bgType || 'gradient' == $bgType || 'pyramid' == $bgType ) ? '' : 'wtbpHidden'; ?>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($classHidden); ?>"
	data-main="settings[cart_styles][background_hover]" data-main-value="bicolored|gradient|pyramid">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Background colors (hover)', 'woo-product-tables'); ?>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][bg_color1_hover]',
				$this->getTableSetting($styles, 'bg_color1_hover', '')
			);
			?>
		<div class="setting-group-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][bg_color2_hover]',
				$this->getTableSetting($styles, 'bg_color2_hover', '')
			);
			?>
		</div>
	</div>
</div>

<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Borders', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set button Borders in this order: color, top, right, bottom, left.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][button_border_color]',
				$this->getTableSetting($styles, 'button_border_color', '')
			);
			?>
		<div class="setting-group-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][button_border_top]', array(
				'value' => $this->getTableSetting($styles, 'button_border_top', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_border_right]', array(
				'value' => $this->getTableSetting($styles, 'button_border_right', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_border_bottom]', array(
				'value' => $this->getTableSetting($styles, 'button_border_bottom', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_border_left]', array(
				'value' => $this->getTableSetting($styles, 'button_border_left', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		</div>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Borders (hover)', 'woo-product-tables'); ?>
			<button type="button" class="wtbpMiniButton wtbpCopyStyles" data-style="borders"><i class="fa fa-level-down" aria-hidden="true"></i></button>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][button_border_color_hover]',
				$this->getTableSetting($styles, 'button_border_color_hover', '')
			);
			?>
		<div class="setting-group-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][button_border_top_hover]', array(
				'value' => $this->getTableSetting($styles, 'button_border_top_hover', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_border_right_hover]', array(
				'value' => $this->getTableSetting($styles, 'button_border_right_hover', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_border_bottom_hover]', array(
				'value' => $this->getTableSetting($styles, 'button_border_bottom_hover', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_border_left_hover]', array(
				'value' => $this->getTableSetting($styles, 'button_border_left_hover', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		</div>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Button shadow', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set button shadow in this order: color, X, Y, blur, spread.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<?php
			HtmlWtbp::colorpickerCompact('settings[cart_styles][button_shadow_color]',
				$this->getTableSetting($styles, 'button_shadow_color', '')
			);
			?>
		<div class="setting-group-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][button_shadow_x]', array(
				'value' => $this->getTableSetting($styles, 'button_shadow_x', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_shadow_y]', array(
				'value' => $this->getTableSetting($styles, 'button_shadow_y', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_shadow_blur]', array(
				'value' => $this->getTableSetting($styles, 'button_shadow_blur', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][button_shadow_spread]', array(
				'value' => $this->getTableSetting($styles, 'button_shadow_spread', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		</div>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
		
	</div>
</div>
<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[use_cart_styles]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Padding', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set padding in this order: top, right, bottom, left.', 'woo-product-tables')); ?>"></i>
		</label>
	</div>
	<div class="setting-input">
		<div class="setting-group-input">
		<?php
			HtmlWtbp::text('settings[cart_styles][padding_top]', array(
				'value' => $this->getTableSetting($styles, 'padding_top', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][padding_right]', array(
				'value' => $this->getTableSetting($styles, 'padding_right', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][padding_bottom]', array(
				'value' => $this->getTableSetting($styles, 'padding_bottom', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			HtmlWtbp::text('settings[cart_styles][padding_left]', array(
				'value' => $this->getTableSetting($styles, 'padding_left', '', false, false, true),
				'attrs' => 'class="wtbpInputSmall"'
			));
			?>
		</div>
		<label class="wtbpLastLabel"><?php esc_html_e('px', 'woo-product-tables'); ?></label>
	</div>
</div>

<div class="setting-wrapper setting-suboption wtbpCartStyles <?php echo esc_attr($wtbpHiddenStyles); ?>"
	data-main="settings[cart_styles][type]">
	<div class="setting-label">
		<label>
			<?php esc_html_e('Set buttons in a row', 'woo-product-tables'); ?>
			<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr__('Set the position of buttons for custom style.', 'woo-product-tables'); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
			HtmlWtbp::checkboxToggle('settings[cart_styles][buttons_in_a_row]', array(
				'checked' => $this->getTableSetting($styles, 'buttons_in_a_row', '0')
			));
			?>
	</div>
</div>
