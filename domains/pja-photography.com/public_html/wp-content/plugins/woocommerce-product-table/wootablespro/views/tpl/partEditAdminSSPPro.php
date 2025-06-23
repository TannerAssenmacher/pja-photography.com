											<?php
											$hiddenClass = ( $this->getTableSetting($this->settings['settings'], 'pagination', false) ? '' : 'wtbpHidden' );
											?>
											<div class="setting-wrapper setting-suboption <?php echo esc_attr($hiddenClass); ?>"
												data-main="settings[pagination]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Server-side Processing', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('This option is recommended for a large tables that cannot be processed in conventional way. The table will be sequentially loaded by ajax on a per page basis, all filtering, ordering and search clauses is server-side implemented too.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[pagination_ssp]', array(
															'checked' => ( isset($this->settings['settings']['pagination_ssp']) ? (int) $this->settings['settings']['pagination_ssp'] : '' ),
															'attrs' => ' data-need-save="1"'
														));
														?>
												</div>
											</div>
