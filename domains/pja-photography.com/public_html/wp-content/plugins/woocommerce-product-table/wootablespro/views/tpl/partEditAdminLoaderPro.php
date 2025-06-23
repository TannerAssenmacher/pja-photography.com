											<?php
											$iconName = $this->getTableSetting($this->settings['settings'], 'table_loader_icon_name', 'default');
											$iconNumber = $this->getTableSetting($this->settings['settings'], 'table_loader_icon_number', '0');

											$isHideLoader = $this->getTableSetting($this->settings['settings'], 'hide_table_loader', false);
											$hiddenClass = ( $isHideLoader ? 'wtbpHidden' : '' );
											?>
											<div class="setting-wrapper setting-wrapper-block setting-suboption <?php echo esc_attr($hiddenClass); ?>"
													data-main-reverse="settings[hide_table_loader]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Table Loader Icon', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Choose icon for loader', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-input">
													<div class="button button-small chooseLoaderIcon"><?php esc_html_e('Choose Icon', 'woo-product-tables'); ?></div>
													<div class="wtbpIconPreview">
														<?php 
														if ('default' === $iconName) {
															echo '<div class="woobewoo-table-loader wtbpLogoLoader"></div>';
														} else if ('spinner' === $iconName) {
															echo '<div class="woobewoo-table-loader spinner"></div>';
														} else {
															echo '<div class="woobewoo-table-loader la-' . esc_attr($iconName) . ' la-2x">';
															for ($i = 1; $i <= $iconNumber; $i++) {
																echo '<div></div>';
															}
															echo '</div>';
														}
														?>
													</div>
													<?php 
														HtmlWtbp::hidden('settings[table_loader_icon_name]', array(
															'value' => $iconName
														));
														HtmlWtbp::hidden('settings[table_loader_icon_number]', array(
															'value' => $iconNumber
														));
														?>
													<div class="wtbpLoaderIconTemplate wtbpHidden">
														<?php
														$loaderSkins = array(
															'timer' => 1, //number means count of div necessary to display loader
															'ball-beat'=> 3,
															'ball-circus'=> 5,
															'ball-atom'=> 4,
															'ball-spin-clockwise-fade-rotating'=> 8,
															'line-scale'=> 5,
															'ball-climbing-dot'=> 4,
															'square-jelly-box'=> 2,
															'ball-rotate'=> 1,
															'ball-clip-rotate-multiple'=> 2,
															'cube-transition'=> 2,
															'square-loader'=> 1,
															'ball-8bits'=> 16,
															'ball-newton-cradle'=> 4,
															'ball-pulse-rise'=> 5,
															'triangle-skew-spin'=> 1,
															'fire'=> 3,
															'ball-zig-zag-deflect'=> 2
														);
														?>
														<div class="items items-list">
															<div class="item">
																<div class="item-inner">
																	<div class="item-loader-container">
																		<div class="preicon_img" data-name="default" data-items="0">
																			<div class="woobewoo-table-loader wtbpLogoLoader"></div>
																		</div>
																	</div>
																</div>
																<div class="item-title">default</div>
															</div>
															<div class="item">
																<div class="item-inner">
																	<div class="item-loader-container">
																		<div class="preicon_img" data-name="spinner" data-items="0">
																			<div class="woobewoo-table-loader spinner"></div>
																		</div>
																	</div>
																</div>
																<div class="item-title">spinner</div>
															</div>
															<?php
															foreach ($loaderSkins as $name=>$number) {
																?>
																<div class="item">
																	<div class="item-inner">
																		<div class="item-loader-container">
																			<div class="woobewoo-table-loader la-<?php echo esc_attr($name); ?> la-2x preicon_img" data-name="<?php echo esc_attr($name); ?>" data-items="<?php echo esc_attr($number); ?>">
																				<?php
																				for ($i=0; $i<$number; $i++) {
																					echo '<div></div>';
																				}
																				?>

																			</div>
																		</div>
																	</div>
																	<div class="item-title"><?php echo esc_html($name); ?></div>
																</div>
															<?php }	?>
														</div>
													</div>
												</div>
											</div>
											<div class="setting-wrapper setting-wrapper-block setting-suboption <?php echo esc_attr($hiddenClass); ?>"
													data-main-reverse="settings[hide_table_loader]">
												<div class="setting-label">
													<label>
														<?php esc_html_e('Table Loader Color', 'woo-product-tables'); ?>
														<i class="fa fa-question woobewoo-tooltip" title="<?php echo esc_attr(__('Choose color for loader', 'woo-product-tables')); ?>"></i>
													</label>
												</div>
												<div class="setting-input">
													<?php 
														HtmlWtbp::colorpickerCompact('settings[table_loader_icon_color]',
															$this->getTableSetting($this->settings['settings'], 'table_loader_icon_color', 'black')
														);
														?>
												</div>
											</div>
