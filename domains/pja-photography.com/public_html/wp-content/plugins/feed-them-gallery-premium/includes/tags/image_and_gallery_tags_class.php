<?php
namespace feed_them_gallery;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image and Gallery Tags
 *
 * This class extends the Gallery class to give ability to create tags for image and galleries.
 *
 * @version  1.0.6
 * @package  FeedThemSocial/Tags
 * @author   SlickRemix
 */

class image_and_gallery_tags_class {


	/**
	 * Gallery_to_Woocommerce constructor.
	 */
	public function __construct() {
	}


	/**
	 * FT Gallery Return Tags
	 *
	 * Return a list of tags with commas and use trim to remove the last comma.
	 *
	 * @param string $final_id The post ID.
	 * @param string $image_or_page Is this an image or a page.
	 * @param string $term_list The list of terms.
	 * @return mixed
	 * @since 1.0.6
	 */
	public function ft_gallery_return_tags( $final_id, $image_or_page, $term_list ) {
		$display_gallery = new Display_Gallery();
		$option          = $display_gallery->ft_gallery_get_option_or_get_postmeta( $final_id );

		$separator = isset( $option[ 'ftg_' . $image_or_page . '_tags_separator' ] ) ? $option[ 'ftg_' . $image_or_page . '_tags_separator' ] : ',&nbsp;';
		$output    = '';

		foreach ( $term_list as $term_single ) {
			$output .= '<a href="' . esc_url( get_site_url() ) . '/?type=' . $image_or_page . '&ftg-tags=' . $term_single->slug . '" title"' . esc_html( $term_single->name ) . '">' . $term_single->name . '</a>' . $separator;
		}

		// echo '<pre>';
		// print_r($term_list);
		// echo '</pre>';.
		return '<span class="ftg-' . $image_or_page . '-tags-link">' . rtrim( $output, '' . esc_html( $separator ) . '' ) . '</span>';
	}

	/**
	 * FT Gallery Tags
	 *
	 * Return either image tags or page tags.
	 *
	 * @param string $id The image ID.
	 * @param string $post_id The post ID.
	 * @param string $image_or_page Is this an image or a page.
	 * @since 1.0.6
	 */
	public function ft_gallery_tags( $id, $post_id, $image_or_page ) {

		$display_gallery = new Display_Gallery();
		$option          = $display_gallery->ft_gallery_get_option_or_get_postmeta( $id );

		$term_list = wp_get_post_terms(
			$id,
			'ftg-tags',
			array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'fields'  => 'all',
			)
		);
		if ( $term_list ) {

			$tags_option = isset( $option[ 'ftg_' . $image_or_page . '_tags_text' ] ) ? $option[ 'ftg_' . $image_or_page . '_tags_text' ] : '';

			if ( 'image' === $image_or_page ) {
				$final_id = $post_id;
			} else {
				$final_id = $id;
			}

			$imagetag_or_gallerytag = 'image' === $image_or_page ? 'Tags:' : 'Gallery Tags:';
			if ( ! empty( $tags_option ) ) {
				$tags_text = $tags_option;
			} else {
				$tags_text = $imagetag_or_gallerytag;
			}
			?>
		<div class='ftg-<?php echo esc_attr( $image_or_page ); ?>-terms-list'>
			<div class="ftg-<?php echo esc_attr( $image_or_page ); ?>-tags-links-wrap">
				<span class="ftg-<?php echo esc_attr( $image_or_page ); ?>-tags-text"><?php echo esc_html( $tags_text ); ?></span>
				<?php
				echo wp_kses(
					$this->ft_gallery_return_tags( $final_id, $image_or_page, $term_list ),
					array(
						'a'    => array(
							'href'  => array(),
							'title' => array(),
						),
						'span' => array(
							'class' => array(),
						),
					)
				);
				?>
			</div>
			</div>
			<?php
		}
	}

}
