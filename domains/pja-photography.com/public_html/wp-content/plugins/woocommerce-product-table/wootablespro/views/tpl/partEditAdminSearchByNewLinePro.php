											<?php
											$hiddenClass = ( $this->getTableSetting($this->settings['settings'], 'column_searching', false) ? '' : 'wtbpHidden' );
											?>
											<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($hiddenClass); ?>"
												 data-main="settings[column_searching]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Use newline as separator', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('You can use newline as separator with search by column', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php
													HtmlWtbp::checkboxToggle('settings[column_searching_textarea]', array(
														'checked' => ( isset($this->settings['column_searching_textarea']) ? (int) $this->settings['column_searching_textarea'] : '' )
													));
													?>
												</div>
											</div>
											<?php
											$isTextarea = $this->getTableSetting($this->settings, 'column_searching_textarea', false);
											$classHidden = !$isTextarea ? 'wtbpHidden' : '';
											?>
											<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
												 data-main="settings[column_searching_textarea]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Set count of input rows', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set the number of rows of the search input that will be visible at a time', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-input">
													<?php
													HtmlWtbp::input('settings[column_searching_textarea_rows]', array(
														'type' => 'number',
														'value' => ( isset($this->settings['column_searching_textarea_rows']) ? $this->settings['column_searching_textarea_rows'] : 1 ),
														'attrs' => 'class="wtbp-small-input" min="1" max="10"'
													));
													?>
												</div>
											</div>
