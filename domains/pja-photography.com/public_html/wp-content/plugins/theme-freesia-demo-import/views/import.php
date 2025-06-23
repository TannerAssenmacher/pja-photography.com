<?php
/**
 * The install plugins page view.
 *
 * @package ocdi
 */

namespace OCDI;

$plugin_installer = new PluginInstaller();
$theme_plugins    = $plugin_installer->get_theme_plugins();
$theme            = wp_get_theme();
?>

<div class="ocdi ocdi--install-plugins">

	<?php echo wp_kses_post( ViewHelpers::plugin_header_output() ); ?>

	<div class="ocdi__content-container">

		<div class="ocdi__admin-notices js-ocdi-admin-notices-container"></div>

		<div class="ocdi__content-container-content">
			<div class="ocdi__content-container-content--main">
				<?php if ( isset( $_GET['import'] ) ) : ?>
					<div class="ocdi-install-plugins-content js-ocdi-install-plugins-content">
						<div class="ocdi-install-plugins-content-header">
							<h2><?php esc_html_e( 'Before We Import Your Demo', 'theme-freesia-demo-import' ); ?></h2>
							<p>
								<?php esc_html_e( 'To ensure the best experience, installing the following plugins is strongly recommended, and in some cases required.', 'theme-freesia-demo-import' ); ?>
							</p>

							<?php if ( ! empty( $this->import_files[ $_GET['import'] ]['import_notice'] ) ) : ?>
								<div class="notice  notice-info">
									<p><?php echo wp_kses_post( $this->import_files[ $_GET['import'] ]['import_notice'] ); ?></p>
								</div>
							<?php endif; ?>
						</div>
						<div class="ocdi-install-plugins-content-content">
							<?php if ( empty( $theme_plugins ) ) : ?>
								<div class="ocdi-content-notice">
									<p>
										<?php esc_html_e( 'All required/recommended plugins are already installed. You can import your demo content.' , 'theme-freesia-demo-import' ); ?>
									</p>
								</div>

							<?php endif; ?>
						</div>
						<div class="ocdi-install-plugins-content-footer">
							<a href="<?php echo esc_url( $this->get_plugin_settings_url() ); ?>" class="button"><img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/long-arrow-alt-left-blue.svg' ); ?>" alt="<?php esc_attr_e( 'Back icon', 'theme-freesia-demo-import' ); ?>"><span><?php esc_html_e( 'Go Back' , 'theme-freesia-demo-import' ); ?></span></a>
							<a href="#" class="button button-primary js-ocdi-install-plugins-before-import"><?php esc_html_e( 'Continue & Import' , 'theme-freesia-demo-import' ); ?></a>
						</div>
					</div>
				<?php else : ?>
					<div class="js-ocdi-auto-start-manual-import"></div>
				<?php endif; ?>

				<div class="ocdi-importing js-ocdi-importing">
					<div class="ocdi-importing-header">
						<h2><?php esc_html_e( 'Importing Content' , 'theme-freesia-demo-import' ); ?></h2>
						<p><?php esc_html_e( 'Please sit tight while we import your content. Do not refresh the page or hit the back button.' , 'theme-freesia-demo-import' ); ?></p>
					</div>
					<div class="ocdi-importing-content">
						<img class="ocdi-importing-content-importing" src="<?php echo esc_url( OCDI_URL . 'assets/images/importing.svg' ); ?>" alt="<?php esc_attr_e( 'Importing animation', 'theme-freesia-demo-import' ); ?>">
					</div>
				</div>

				<div class="ocdi-imported js-ocdi-imported">
					<div class="ocdi-imported-header">
						<h2 class="js-ocdi-ajax-response-title"><?php esc_html_e( 'Import Complete!' , 'theme-freesia-demo-import' ); ?></h2>
						<div class="js-ocdi-ajax-response-subtitle">
							<p>
								<?php esc_html_e( 'Congrats, your demo was imported successfully. You can now begin editing your site.' , 'theme-freesia-demo-import' ); ?>
							</p>
						</div>
					</div>
					<div class="ocdi-imported-content">
						<div class="ocdi__response  js-ocdi-ajax-response"></div>
					</div>
					<div class="ocdi-imported-footer">
						<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'Theme Settings' , 'theme-freesia-demo-import' ); ?></a>
						<a href="<?php echo esc_url( get_home_url() ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'Visit Site' , 'theme-freesia-demo-import' ); ?></a>
					</div>
				</div>
			</div>
			<div class="ocdi__content-container-content--side">
				<?php
					$selected = isset( $_GET['import'] ) ? (int) $_GET['import'] : null;
					echo wp_kses_post( ViewHelpers::small_theme_card( $selected ) );
				?>
			</div>
		</div>

	</div>
</div>
