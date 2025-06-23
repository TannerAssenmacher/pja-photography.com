											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Show variation thumbnails', 'woo-product-tables'); ?>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[show_variation_image]', array(
															'checked' => ( isset($this->settings['settings']['show_variation_image']) ? (int) $this->settings['settings']['show_variation_image'] : '' )
														));
														?>
												</div>
											</div>
											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Show first variation as default', 'woo-product-tables'); ?>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[set_def_var]', array(
															'checked' => ( isset($this->settings['settings']['set_def_var']) ? (int) $this->settings['settings']['set_def_var'] : '' )
														));
														?>
												</div>
											</div>
											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Show variation price in price column', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Works only with enabled price column.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[var_price_column]', array(
															'checked' => ( isset($this->settings['settings']['var_price_column']) ? (int) $this->settings['settings']['var_price_column'] : '' )
														));
														?>
												</div>
											</div>
											<?php $selectedAddCart = $this->getTableSetting($this->settings['settings'], 'multiple_add_cart', false); ?>
											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Add selected to cart', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr('<div class="wtbpTooltipsWrapper"><div class="wtbpTooltipsText">' . __('Multiple add to cart selected products. <a href="https://woobewoo.com/documentation/add-to-cart-button-and-variations/" target="_blank">Read more.</a>', 'woo-product-tables') . '</div><img src="' . esc_url($this->image_path . 'img/add_selected_to_cart.png') . '" height="86"></div>'); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[multiple_add_cart]', array(
															'checked' => ( isset($this->settings['settings']['multiple_add_cart']) ? (int) $this->settings['settings']['multiple_add_cart'] : '' ),
															'attrs' => ' data-need-save="1"'
														));
														?>
												</div>
											</div>
											<?php $classHidden = $selectedAddCart ? '' : 'wtbpHidden'; ?>
											<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>" data-main="settings[multiple_add_cart]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Checkboxes position', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Last column does not work in Responsive mode and Automatic column hiding mode with Server-side Processing.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-input">
													<?php 
														HtmlWtbp::selectbox('settings[multiple_add_cart_position]', array(
														'options' => array(
															'first' => __('First column', 'woo-product-tables'),
															'last' => __('Last column', 'woo-product-tables')
															),
														'value' => ( isset($this->settings['settings']['multiple_add_cart_position']) ? $this->settings['settings']['multiple_add_cart_position'] : 'first' ),
														'attrs' => ' class="woobewoo-flat-input"'
														));
														?>
												</div>
											</div>
											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Add all to cart', 'woo-product-tables'); ?>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[all_add_cart]', array(
															'checked' => ( isset($this->settings['settings']['all_add_cart']) ? (int) $this->settings['settings']['all_add_cart'] : '' )
														));
														?>
												</div>
											</div>

											<?php $bunchAddCart = $this->getTableSetting($this->settings['settings'], 'bunch_add_cart', false); ?>
											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Min/Max bunch add to cart', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Set min and max quantity values for a  "Add selected to cart" and "Add all to cart" buttons.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php
														HtmlWtbp::checkboxToggle('settings[bunch_add_cart]', array(
															'checked' => ( isset($this->settings['settings']['bunch_add_cart']) ? (int) $this->settings['settings']['bunch_add_cart'] : '' ),
															'attrs' => ' data-need-save="1"'
														));
														?>
												</div>
											</div>
											<?php $classHidden = $bunchAddCart ? '' : 'wtbpHidden'; ?>
											<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>" data-main="settings[bunch_add_cart]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Min products in bunch', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Minimum quantity of products in bunch of product in multiple to cart addition.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-input">
													<?php
													HtmlWtbp::text('settings[bunch_add_cart_min]', array(
														'value' => ( isset($this->settings['settings']['bunch_add_cart_min']) ? $this->settings['settings']['bunch_add_cart_min'] : '' ),
														'attrs' => ' class="woobewoo-flat-input woobewoo-width60 wtbp-only-numbers" data-need-save="1"'
													));
													?>
												</div>
											</div>
											<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>" data-main="settings[bunch_add_cart]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Max products in bunch', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Maximum quantity of products in bunch of product in multiple to cart addition.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-input">
													<?php
													HtmlWtbp::text('settings[bunch_add_cart_max]', array(
														'value' => ( isset($this->settings['settings']['bunch_add_cart_max']) ? $this->settings['settings']['bunch_add_cart_max'] : '' ),
														'attrs' => ' class="woobewoo-flat-input woobewoo-width60 wtbp-only-numbers" data-need-save="1"'
													));
													?>
												</div>
											</div>

											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Hide view cart link', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Hide view cart link.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php 
														HtmlWtbp::checkboxToggle('settings[view_cart_hide]', array(
															'checked' => ( isset($this->settings['settings']['view_cart_hide']) ? (int) $this->settings['settings']['view_cart_hide'] : '' )
														));
														?>
												</div>
											</div>
											<div class="setting-wrapper">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Lazy load', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Lazy load for big table', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php
													HtmlWtbp::checkboxToggle('settings[lazy_load]', array(
														'checked' => ( isset($this->settings['settings']['lazy_load']) ? (int) $this->settings['settings']['lazy_load'] : '' ),
														'attrs' => ' data-need-save="1"'
													));
													?>
												</div>
											</div>
											<?php
											$lazyLoad = $this->getTableSetting($this->settings['settings'], 'lazy_load', false);
											$classHidden = $lazyLoad ? '' : 'wtbpHidden';
											?>
											<div class="setting-wrapper setting-wrapper-inline setting-suboption <?php echo esc_attr($classHidden); ?>"
												 data-main="settings[lazy_load]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Lazy load limit', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Here you can set the number of rows to display on one lazy load.', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-input">
													<?php
													HtmlWtbp::text('settings[lazy_load_length]', array(
														'value' => ( isset($this->settings['settings']['lazy_load_length']) ? $this->settings['settings']['lazy_load_length'] : 50 ),
														'attrs' => ' class="woobewoo-flat-input woobewoo-width60 wtbp-only-numbers" data-need-save="1"'
													));
													?>
												</div>
											</div>
											<?php
											if (method_exists(FrameWtbp::_()->getModule('wootablepress'), 'isWcmfPluginActivated')) {
												$classHidden = FrameWtbp::_()->getModule('wootablepress')->isWcmfPluginActivated() ? '' : 'wtbpHidden';
											}
											?>
											<div class="setting-wrapper <?php echo esc_attr($classHidden); ?>">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Show products by vendor', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Automatically show products by WCFM vendor on Vendor page', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-check">
													<?php
													HtmlWtbp::checkboxToggle('settings[show_products_by_vendor]', array(
														'checked' => ( isset($this->settings['settings']['show_products_by_vendor']) ? (int) $this->settings['settings']['show_products_by_vendor'] : '' )
													));
													?>
												</div>
											</div>
