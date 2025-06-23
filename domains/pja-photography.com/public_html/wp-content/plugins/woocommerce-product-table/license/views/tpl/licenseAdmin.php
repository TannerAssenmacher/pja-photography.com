<section class="woobewoo-bar">
	<h4>
		<?php if ($this->isActive) {
			/* translators: %s: plugin name */
			echo sprintf(esc_html__('Congratulations! PRO version of %s plugin is activated and working fine!', 'woo-product-tables'), esc_html(WTBP_WP_PLUGIN_NAME));
		} elseif ($this->isExpired) {
			/* translators: %s: plugin name */
			echo sprintf(esc_html__('Your license for PRO version of %s plugin - expired.', 'woo-product-tables'), esc_html(WTBP_WP_PLUGIN_NAME));

			if (empty($this->licenseType)) {
				/* translators: %s url */
				echo ' ' . sprintf(esc_html__('You can %s to extend your license, then - click on "Re-activate" button to re-activate your PRO version.', 'woo-product-tables'), '<a href="' . esc_url($this->extendUrl) . '" target="_blank">click here</a>');
			}
		} else {
			/* translators: %s: plugin name */
			echo sprintf(esc_html__('Congratulations! You have successfully installed PRO version of %s plugin.', 'woo-product-tables'), esc_html(WTBP_WP_PLUGIN_NAME)) . ' ';
			if ('wc' == $this->licenseType) {
				/* translators: %s url */
				echo sprintf(esc_html__('To activate your PRO version verification of your subscription is required. Go to %s and make sure you are logged in to your WooCommerce.com account.', 'woo-product-tables'), '<b>' . esc_html('WooCommerce > Extensions > WooCommerce.com Subscriptions') . '</b>');
			} else if ('cc' == $this->licenseType) {
				echo esc_html__('Final step to finish Your PRO version setup - is to enter your Username for codecanyon.net and Purchase code on this page. This will activate Your copy of software on this site.', 'woo-product-tables');
			} else {
				echo esc_html__('Final step to finish Your PRO version setup - is to enter your Email and License Key on this page. This will activate Your copy of software on this site.', 'woo-product-tables');
			}
		}
		?>
	</h4>
	<div class="woobewoo-clear"></div>
	<hr />
</section>
<section>
	<form id="wtbpLicenseForm" class="">
		<div class="woobewoo-item woobewoo-panel">
			<table class="form-table">
				<tr
				<?php 
				if (!empty($this->licenseType)) {
					echo ' class="woobewoo-hidden"';
				}
				?>
					>
					<th scope="row">
						<?php esc_html_e('Email', 'woo-product-tables'); ?>
					</th>
					<td class="woobewoo-width1">
						<?php
						echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Your email address, used on checkout procedure on', 'woo-product-tables') . ' <a href="http://woobewoo.com/" target="_blank">http://woobewoo.com/</a>') . '"></i>';
						?>
					</td>
					<td>
						<?php HtmlWtbp::text('email', array('value' => $this->credentials['email'], 'attrs' => 'class="woobewoo-width300"')); ?>
					</td>
				</tr>
				<tr
				<?php 
				if ('cc' != $this->licenseType) {
					echo ' class="woobewoo-hidden"';
				}
				?>
					>
					<th scope="row">
						<?php esc_html_e('Username', 'woo-product-tables'); ?>
					</th>
					<td class="woobewoo-width1">
						<?php
						echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Username for codecanyon.net', 'woo-product-tables')) . '"></i>';
						?>
					</td>
					<td>
						<?php HtmlWtbp::text('name', array('value' => $this->credentials['name'], 'attrs' => 'class="woobewoo-width300"')); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php 
						if ('cc' == $this->licenseType) {
							esc_html_e('Purchase code', 'woo-product-tables'); 
						} else {
							esc_html_e('License Key', 'woo-product-tables'); 
						}
						?>
					</th>
					<td>
						<?php
						if ('wc' == $this->licenseType) {
							echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Your License Key from your WooCommerce.com Subscriptions', 'woo-product-tables')) . '"></i>';
						} else if ('cc' == $this->licenseType) {
							echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Your Purchase code from', 'woo-product-tables')) . '"></i>';
						} else {
							echo '<i class="fa fa-question woobewoo-tooltip" title="' . esc_attr(__('Your License Key from your account on', 'woo-product-tables') . ' <a href="http://woobewoo.com/" target="_blank">http://woobewoo.com/</a>') . '"></i>';
						}
						?>
					</td>
					<td>
						<?php HtmlWtbp::text('key', array('value' => $this->credentials['key'], 'attrs' => 'class="woobewoo-width300"' . ( 'wc' == $this->licenseType ? ' disabled' : '' ))); ?>
					</td>
				</tr>
				<tr
				<?php 
				if ('wc' == $this->licenseType) {
					echo ' class="woobewoo-hidden"';
				}
				?>
					>
					<th scope="row" colspan="3">
						<?php HtmlWtbp::hidden('type', array('value' => $this->credentials['type'])); ?>
						<?php HtmlWtbp::hidden('mod', array('value' => 'license')); ?>
						<?php HtmlWtbp::hidden('action', array('value' => 'activate')); ?>
						<button class="button button-primary">
							<i class="fa fa-fw fa-save"></i>
							<?php
							if ($this->isExpired) {
								esc_html_e('Re-activate', 'woo-product-tables');
							} else {
								esc_html_e('Activate', 'woo-product-tables');
							}
							?>
						</button>
					</th>
				</tr>
			</table>
			<div class="woobewoo-clear"></div>
			
		</div>
	</form>
</section>
