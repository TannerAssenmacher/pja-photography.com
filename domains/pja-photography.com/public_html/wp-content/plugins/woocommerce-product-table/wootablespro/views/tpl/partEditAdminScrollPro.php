											<?php 
												$classHidden = $this->getTableSetting($this->settings, 'pagination', false) ? '' : 'wtbpHidden';
											?>
											<div class="setting-wrapper setting-suboption <?php echo esc_attr($classHidden); ?>"
												data-main="settings[pagination]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Scroll to top on pagination', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Scroll page to the top of the table when pagination is used.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[pagination_scroll]', array(
														'checked' => ( isset($this->settings['pagination_scroll']) ? $this->settings['pagination_scroll'] : '' )
														));
														?>
												</div>
											</div>
