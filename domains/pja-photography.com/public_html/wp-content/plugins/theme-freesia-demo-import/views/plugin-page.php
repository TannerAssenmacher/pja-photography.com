<?php
/**
 * The plugin page view - the "settings" page of the plugin.
 *
 * @package ocdi
 */

namespace OCDI;

$predefined_themes = $this->import_files;

if ( ! empty( $this->import_files ) && isset( $_GET['import-mode'] ) && 'manual' === $_GET['import-mode'] ) {
	$predefined_themes = array();
}

/**
 * Hook for adding the custom plugin page header
 */
Helpers::do_action( 'ocdi/plugin_page_header' );
?>

<div class="ocdi">

	<?php echo wp_kses_post( ViewHelpers::plugin_header_output() ); ?>

	<div class="ocdi__content-container">

		<?php
		// Display warrning if PHP safe mode is enabled, since we wont be able to change the max_execution_time.
		if ( ini_get( 'safe_mode' ) ) {
			printf( /* translators: %1$s - the opening div and paragraph HTML tags, %2$s and %3$s - strong HTML tags, %4$s - the closing div and paragraph HTML tags. */
				esc_html__( '%1$sWarning: your server is using %2$sPHP safe mode%3$s. This means that you might experience server timeout errors.%4$s', 'theme-freesia-demo-import' ),
				'<div class="notice  notice-warning  is-dismissible"><p>',
				'<strong>',
				'</strong>',
				'</p></div>'
			);
		}
		?>

		<div class="ocdi__admin-notices js-ocdi-admin-notices-container"></div>

		<?php
		// Start output buffer for displaying the plugin intro text.
		ob_start();
		?>

		<div class="ocdi__intro-text">
			<p class="about-description">
				<?php esc_html_e( 'Importing demo data (post, pages, images, theme settings, etc.) is the quickest and easiest way to set up your new theme.', 'theme-freesia-demo-import' ); ?>
				<?php esc_html_e( 'It allows you to simply edit everything instead of creating content and layouts from scratch.', 'theme-freesia-demo-import' ); ?>
				<a href="https://ocdi.com/user-guide/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'theme-freesia-demo-import' ); ?></a>.
			</p>
		</div>

		<?php
		$plugin_intro_text = ob_get_clean();

		// Display the plugin intro text (can be replaced with custom text through the filter below).
		echo wp_kses_post( Helpers::apply_filters( 'ocdi/plugin_intro_text', $plugin_intro_text ) );
		?>

		<?php if ( empty( $this->import_files ) ) : ?>
			<div class="notice  notice-info">
				<p><?php esc_html_e( 'There are no predefined import files available for this theme. Please upload the import files manually below.', 'theme-freesia-demo-import' ); ?></p>
			</div>
		<?php endif; ?>

		<?php $theme = wp_get_theme(); ?>

		<div class="ocdi__theme-about">
			<div class="ocdi__theme-about-screenshots">
				<?php if ( $theme->get_screenshot() ) : ?>
				<div class="screenshot"><img src="<?php echo esc_url( $theme->get_screenshot() ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'theme-freesia-demo-import' ); ?>" /></div>
				<?php else : ?>
				<div class="screenshot blank"></div>
				<?php endif; ?>
			</div>

			<div class="ocdi__theme-about-info">
				<div class="top-content">
					<div class="theme-title">
						<h2 class="theme-name"><?php echo esc_html( $theme->name ); ?></h2>
						<span class="theme-version">
							<?php
							/* translators: %s: Theme version. */
							printf( __( 'Version: %s' ), esc_html( $theme->version ) );
							?>
						</span>
					</div>
					<p class="theme-author">
						<?php
						/* translators: %s: Theme author link. */
						printf( __( 'By %s' ), wp_kses_post( $theme->author ) );
						?>
					</p>

					<p class="theme-description"><?php echo wp_kses_post( $theme->description ); ?></p>

					<?php if ( ! empty( $theme->tags ) ) : ?>
					<hr>
					<p class="theme-tags"><span><?php esc_html_e( 'Tags:' ); ?></span> <?php echo esc_html( implode( ', ', $theme->tags ) ); ?></p>
					<?php endif; ?>
				</div>
				<div class="bottom-content">
					<?php if ( ! empty( $this->import_files ) ) : ?>
						<?php if ( empty( $_GET['import-mode'] ) || 'manual' !== $_GET['import-mode'] ) : ?>
							<a href="<?php echo esc_url( $this->get_plugin_settings_url( array( 'import-mode' => 'manual' ) ) ); ?>" class="ocdi-import-mode-switch"><?php esc_html_e( 'Switch to Manual Import', 'theme-freesia-demo-import' ); ?></a>
						<?php else : ?>
							<a href="<?php echo esc_url( $this->get_plugin_settings_url() ); ?>" class="ocdi-import-mode-switch"><?php esc_html_e( 'Switch back to Theme Predefined Imports', 'theme-freesia-demo-import' ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if ( empty( $predefined_themes ) ) : ?>

			<div class="ocdi__file-upload-container">
				<div class="ocdi__file-upload-container--header">
					<h2><?php esc_html_e( 'Manual Demo File Import', 'theme-freesia-demo-import' ); ?></h2>
				</div>

				<div class="ocdi__file-upload-container-items">
					<?php $first_row_class = class_exists( 'ReduxFramework' ) ? 'four' : 'three'; ?>
					<div class="ocdi__file-upload ocdi__card ocdi__card--<?php echo esc_attr( $first_row_class ); ?>">
						<div class="ocdi__card-content">
							<label for="ocdi__content-file-upload">
								<div class="ocdi-icon-container">
									<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/content.svg' ); ?>" class="ocdi-icon--content" alt="<?php esc_attr_e( 'Content import icon', 'theme-freesia-demo-import' ); ?>">
								</div>
								<h3><?php esc_html_e( 'Import Content', 'theme-freesia-demo-import' ); ?></h3>
								<p><?php esc_html_e( 'Select an XML file to import.', 'theme-freesia-demo-import' ); ?></p>
							</label>
							<a href="https://ocdi.com/user-guide/#import-content" target="_blank" rel="noopener noreferrer" class="ocdi__card-content-info">
								<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/info-circle.svg' ); ?>" alt="<?php esc_attr_e( 'Info icon', 'theme-freesia-demo-import' ); ?>">
							</a>
						</div>
						<div class="ocdi__card-footer">
							<label for="ocdi__content-file-upload" class="button button-primary custom-file-upload-button">
								<?php esc_html_e( 'Select a File', 'theme-freesia-demo-import' ); ?>
							</label>
							<input id="ocdi__content-file-upload" type="file" class="ocdi-hide-input" name="content-file-upload">
						</div>
					</div>

					<div class="ocdi__file-upload ocdi__card ocdi__card--<?php echo esc_attr( $first_row_class ); ?>">
						<div class="ocdi__card-content">
							<label for="ocdi__widget-file-upload">
								<div class="ocdi-icon-container">
									<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/widgets.svg' ); ?>" class="ocdi-icon--widgets" alt="<?php esc_attr_e( 'Widgets import icon', 'theme-freesia-demo-import' ); ?>">
								</div>
								<h3><?php esc_html_e( 'Import Widgets', 'theme-freesia-demo-import' ); ?></h3>
								<p><?php esc_html_e( 'Select a JSON/WIE file to import.', 'theme-freesia-demo-import' ); ?></p>
							</label>
							<a href="https://ocdi.com/user-guide/#import-widgets" target="_blank" rel="noopener noreferrer" class="ocdi__card-content-info">
								<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/info-circle.svg' ); ?>" alt="<?php esc_attr_e( 'Info icon', 'theme-freesia-demo-import' ); ?>">
							</a>
						</div>
						<div class="ocdi__card-footer">
							<label for="ocdi__widget-file-upload" class="button button-primary custom-file-upload-button">
								<?php esc_html_e( 'Select a File', 'theme-freesia-demo-import' ); ?>
							</label>
							<input id="ocdi__widget-file-upload" type="file" class="ocdi-hide-input" name="widget-file-upload">
						</div>
					</div>

					<div class="ocdi__file-upload ocdi__card ocdi__card--<?php echo esc_attr( $first_row_class ); ?>">
						<div class="ocdi__card-content">
							<label for="ocdi__customizer-file-upload">
								<div class="ocdi-icon-container">
									<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/brush.svg' ); ?>" class="ocdi-icon--brush" alt="<?php esc_attr_e( 'Customizer import icon', 'theme-freesia-demo-import' ); ?>">
								</div>
								<h3><?php esc_html_e( 'Import Customizer', 'theme-freesia-demo-import' ); ?></h3>
								<p><?php esc_html_e( 'Select a DAT file to import.', 'theme-freesia-demo-import' ); ?></p>
							</label>
							<a href="https://ocdi.com/user-guide/#import-customizer" target="_blank" rel="noopener noreferrer" class="ocdi__card-content-info">
								<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/info-circle.svg' ); ?>" alt="<?php esc_attr_e( 'Info icon', 'theme-freesia-demo-import' ); ?>">
							</a>
						</div>
						<div class="ocdi__card-footer">
							<label for="ocdi__customizer-file-upload" class="button button-primary custom-file-upload-button">
								<?php esc_html_e( 'Select a File', 'theme-freesia-demo-import' ); ?>
							</label>
							<input id="ocdi__customizer-file-upload" type="file" class="ocdi-hide-input" name="customizer-file-upload">
						</div>
					</div>

					<?php if ( class_exists( 'ReduxFramework' ) ) : ?>
					<div class="ocdi__file-upload ocdi__card ocdi__card--<?php echo esc_attr( $first_row_class ); ?>">
						<div class="ocdi__card-content">
							<label for="ocdi__redux-file-upload">
								<div class="ocdi-icon-container">
									<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/redux.svg' ); ?>" class="ocdi-icon--redux" alt="<?php esc_attr_e( 'Redux import icon', 'theme-freesia-demo-import' ); ?>">
								</div>
								<h3><?php esc_html_e( 'Import Redux', 'theme-freesia-demo-import' ); ?></h3>
								<p><?php esc_html_e( 'Select a JSON file and enter Redux option name.', 'theme-freesia-demo-import' ); ?></p>
							</label>
							<a href="https://ocdi.com/user-guide/#import-redux" target="_blank" rel="noopener noreferrer" class="ocdi__card-content-info">
								<img src="<?php echo esc_url( OCDI_URL . 'assets/images/icons/info-circle.svg' ); ?>" alt="<?php esc_attr_e( 'Info icon', 'theme-freesia-demo-import' ); ?>">
							</a>
						</div>
						<div class="ocdi__card-footer">
							<label for="ocdi__redux-file-upload" class="button button-primary custom-file-upload-button">
								<?php esc_html_e( 'Select a File', 'theme-freesia-demo-import' ); ?>
							</label>
							<input id="ocdi__redux-file-upload" type="file" class="ocdi-hide-input" name="redux-file-upload">
							<input id="ocdi__redux-option-name" class="ocdi__redux-option-name-input" type="text" name="redux-option-name" placeholder="<?php esc_attr_e( 'Enter Option Name', 'theme-freesia-demo-import' ); ?>">
						</div>
					</div>
					<?php endif; ?>

				</div>
				<div class="ocdi__file-upload-container--footer">
					<button class="ocdi__button button button-hero js-ocdi-cancel-manual-import" disabled><?php esc_html_e( 'Cancel', 'theme-freesia-demo-import' ); ?></button>
					<button class="ocdi__button button button-hero button-primary js-ocdi-start-manual-import" disabled><?php esc_html_e( 'Continue & Import', 'theme-freesia-demo-import' ); ?></button>
				</div>
			</div>

		<?php elseif ( 1 === count( $predefined_themes ) ) : ?>

			<div class="ocdi__demo-import-notice  js-ocdi-demo-import-notice"><?php
				if ( is_array( $predefined_themes ) && ! empty( $predefined_themes[0]['import_notice'] ) ) {
					echo wp_kses_post( $predefined_themes[0]['import_notice'] );
				}
			?></div>

			<p class="ocdi__button-container">
				<a href="<?php echo esc_url( $this->get_plugin_settings_url( [ 'step' => 'import', 'import' => 0 ] ) ); ?>" class="ocdi__button  button  button-hero  button-primary"><?php esc_html_e( 'Import Demo Data', 'theme-freesia-demo-import' ); ?></a>
			</p>

		<?php else : ?>

			<!-- OCDI grid layout -->
			<div class="ocdi__gl  js-ocdi-gl">
			<?php
				// Prepare navigation data.
				$categories = Helpers::get_all_demo_import_categories( $predefined_themes );
			?>
				<?php if ( ! empty( $categories ) ) : ?>
					<div class="ocdi__gl-header  js-ocdi-gl-header">
						<nav class="ocdi__gl-navigation">
							<ul>
								<li class="active"><a href="#all" class="ocdi__gl-navigation-link  js-ocdi-nav-link"><span><?php esc_html_e( 'All Demos', 'theme-freesia-demo-import' ); ?></span></a></li>
								<?php foreach ( $categories as $key => $name ) : ?>
									<li>
										<a href="#<?php echo esc_attr( $key ); ?>" class="ocdi__gl-navigation-link  js-ocdi-nav-link">
											<span>
												<?php echo esc_html( $name ); ?>
											</span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
						<div clas="ocdi__gl-search">
							<input type="search" class="ocdi__gl-search-input  js-ocdi-gl-search" name="ocdi-gl-search" value="" placeholder="<?php esc_html_e( 'Search Demos...', 'theme-freesia-demo-import' ); ?>">
						</div>
					</div>
				<?php else : ?>
					<hr>
				<?php endif; ?>
				<div class="ocdi__gl-item-container js-ocdi-gl-item-container">
					<?php foreach ( $predefined_themes as $index => $import_file ) : ?>
						<?php
							// Prepare import item display data.
							$img_src = isset( $import_file['import_preview_image_url'] ) ? $import_file['import_preview_image_url'] : '';
							$is_plus = isset( $import_file['isPlus'] ) ? $import_file['isPlus'] : '';
							// Default to the theme screenshot, if a custom preview image is not defined.
							if ( empty( $img_src ) ) {
								$theme = wp_get_theme();
								$img_src = $theme->get_screenshot();
							}

						?>
						<div class="ocdi__gl-item js-ocdi-gl-item" data-categories="<?php echo esc_attr( Helpers::get_demo_import_item_categories( $import_file ) ); ?>" data-name="<?php echo esc_attr( strtolower( $import_file['import_file_name'] ) ); ?>">
							<div class="ocdi__gl-item-image-container">
								<?php if ( ! empty( $img_src ) ) : ?>
									<img class="ocdi__gl-item-image" src="<?php echo esc_url( $img_src ) ?>">
								<?php else : ?>
									<div class="ocdi__gl-item-image  ocdi__gl-item-image--no-image"><?php esc_html_e( 'No preview image.', 'theme-freesia-demo-import' ); ?></div>
								<?php endif; ?>
							</div>
							<div class="ocdi__gl-item-footer<?php echo ! empty( $import_file['preview_url'] ) ? '  ocdi__gl-item-footer--with-preview' : ''; ?>">
								<h4 class="ocdi__gl-item-title" title="<?php echo esc_attr( $import_file['import_file_name'] ); ?>"><?php echo esc_html( $import_file['import_file_name'] ); ?></h4>
								<span class="ocdi__gl-item-buttons">
									<?php if ( ! empty( $import_file['preview_url'] ) ) : ?>
										<a class="ocdi__gl-item-button  button" href="<?php echo esc_url( $import_file['preview_url'] ); ?>" target="_blank"><?php esc_html_e( 'Preview Demo', 'theme-freesia-demo-import' ); ?></a>
									<?php endif; ?>
									<?php if ($is_plus == true){ ?>
								<a class="tfdi__gl-item-button  buy-button button" href="<?php echo esc_url( $import_file['buy_url'] ); ?>" target="_blank"> <?php esc_html_e( 'Buy Now', 'theme-freesia-demo-import' ); ?></a>

							<?php } else { ?>
								<a class="ocdi__gl-item-button  button  button-primary" href="<?php echo $this->get_plugin_settings_url( [ 'step' => 'import', 'import' => esc_attr( $index ) ] ); ?>"><?php esc_html_e( 'Import Demo', 'theme-freesia-demo-import' ); ?></a>
							<?php } ?>
								</span>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
/**
 * Hook for adding the custom admin page footer
 */
Helpers::do_action( 'ocdi/plugin_page_footer' );
