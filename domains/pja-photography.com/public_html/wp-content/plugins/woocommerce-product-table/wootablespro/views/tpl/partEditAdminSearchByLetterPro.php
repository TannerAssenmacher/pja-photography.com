<div class="setting-wrapper">
	<div class="setting-label">
		<label>
			<?php esc_html_e( 'Search by letter', 'woo-product-tables' ); ?>
			<i class="fa fa-question woobewoo-tooltip"
			   title="<?php echo esc_attr( '<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __( 'Show alphabet for search by first letter', 'woo-product-tables' ) . '</div></div>' ); ?>"></i>
		</label>
	</div>
	<div class="setting-check">
		<?php
		HtmlWtbp::checkboxToggle( 'settings[search_by_letter]', array(
			'checked' => ( isset( $this->settings['search_by_letter'] ) ? (int) $this->settings['search_by_letter'] : '' )
		) );
		?>
	</div>
</div>
