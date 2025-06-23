<section class="woobewoo-bar">
	<div class="woobewoo-title-module">
		<?php 
			echo esc_html__('Bulk converter Custom product attributes to Taxonomy product attributes', 'woo-product-tables');
		?>
	</div>
	<div class="woobewoo-clear"></div>
</section>
<?php
$classHidden = '';
if (count($this->customAttributes) == 0) {
	$classHidden = 'woobewoo-hidden';
	$this->customAttributes[''] = esc_attr(__('No custom product attributes found.', 'woo-product-tables'));
}
?>
<section>
	<form id="wtbpConverterForm">
		<div class="woobewoo-item woobewoo-panel">
			<table class="form-table">
				<tr>
					<th scope="row">
						<?php esc_html_e('Select custom product attributes', 'woo-product-tables'); ?>
					</th>
					<td class="woobewoo-width1">
						<?php
						echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Select one of the custom product attributes to convert.', 'woo-product-tables')) . '"></i>';
						?>
					</td>
					<td>
						<?php 
							HtmlWtbp::selectbox('custom_attribute', array(
								'options' => $this->customAttributes,
								'attrs' => 'class="woobewoo-flat-input woobewoo-width300"')
							);
							?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php esc_html_e('Name for taxonomy attribute', 'woo-product-tables'); ?>
					</th>
					<td class="woobewoo-width1">
						<?php
							echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Define a name for new taxonomy attribute.', 'woo-product-tables')) . '"></i>';
						?>
					</td>
					<td>
						<?php HtmlWtbp::text('attribute_name', array('value' => '', 'attrs' => 'class="woobewoo-flat-input woobewoo-width300"')); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php esc_html_e('Slug for taxonomy attribute', 'woo-product-tables'); ?>
					</th>
					<td class="woobewoo-width1">
						<?php
							echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Unique attribute slug. Must be no more than 28 characters. Be careful: if you specify an existing slug, then new terms will be added to it and it will be attached to the products.', 'woo-product-tables')) . '"></i>';
						?>
					</td>
					<td>
						<?php HtmlWtbp::text('attribute_slug', array('value' => '', 'attrs' => 'class="woobewoo-flat-input woobewoo-width300"')); ?>
					</td>
				</tr>
				<tr class="<?php echo esc_attr($classHidden); ?>">
					<th scope="row">
						<?php HtmlWtbp::hidden('mod', array('value' => 'utilities')); ?>
						<?php HtmlWtbp::hidden('action', array('value' => 'convertAttribute')); ?>
						<button class="button button-primary">
							<i class="fa fa-fw fa-wrench"></i>
							<?php esc_html_e('Go', 'woo-product-tables'); ?>
						</button>
					</th>
					<td class="woobewoo-width1">
					</td>
					<td>
						<div class="woobewoo-error">
							<?php echo esc_html__('WARNING: Remember to backup your database first!', 'woo-product-tables'); ?>
						</div>
					</td>
				</tr>
			</table>
			<div class="woobewoo-clear"></div>
		</div>
	</form>
</section>
