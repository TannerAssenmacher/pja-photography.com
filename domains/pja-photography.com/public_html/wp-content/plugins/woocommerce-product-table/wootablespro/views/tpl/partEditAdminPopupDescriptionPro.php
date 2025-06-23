											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Show variation description instead of product description', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Show variation description instead of product description in variations popup.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php
														HtmlWtbp::checkboxToggle('settings[show_variation_description]', array(
															'checked' => ( isset($this->settings['show_variation_description']) ? (int) $this->settings['show_variation_description'] : '' )
														));
														?>
												</div>
											</div>
