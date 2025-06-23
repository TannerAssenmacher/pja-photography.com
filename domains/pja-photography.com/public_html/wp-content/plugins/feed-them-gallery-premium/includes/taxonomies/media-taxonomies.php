<?php

namespace feed_them_gallery;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Media_Taxonomies
 */
class Media_Taxonomies {


    /**
     * Load Class
     *
     * Function to initiate class loading.
     *
     * @since 1.1.8
     */
	public static function load() {
		$instance = new self();

		// Add Actions and Filters.
		$instance->add_actions_filters();
	}

    /**
     * Add Actions & Filters
     *
     * Adds the Actions and filters for the class.
     *
     * @since 1.1.8
     */
	public function add_actions_filters() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_filter( 'manage_edit-attachment_sortable_columns', array( $this, 'manage_edit_attachment_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 0, 1 );
		add_action( 'wp_ajax_save-media-terms', array( $this, 'save_media_terms' ), 0, 1 );
		add_action( 'wp_ajax_add-media-term', array( $this, 'add_media_term' ), 0, 1 );
		add_action( 'wp_ajax_delete-media-term', array( $this, 'delete_media_term' ), 0, 1 );

		register_taxonomy_for_object_type( 'category', 'attachment' );
		add_post_type_support( 'attachment', 'category' );
	}

	/**
	 * Constructor
	 *
	 * @access public
	 * @since v0.9
	 */
	public function __construct() { }

	/**
	 * Add media term
	 * ajax callback
	 *
	 * @since v.1.3
	 **/
	public function add_media_term() {
		$response      = array();
		$attachment_id = (int) stripslashes_deep( $_REQUEST['attachment_id'] );
		$taxonomy      = get_taxonomy( sanitize_text_field( stripslashes_deep( $_REQUEST['taxonomy'] ) ) );
		$parent        = ( (int) $_REQUEST['parent'] > 0 ) ? (int) stripslashes_deep( $_REQUEST['parent'] ) : 0;

		// Check if term already exists.
		$term = get_term_by( 'name', sanitize_text_field( stripslashes_deep( $_REQUEST['term'] ) ), $taxonomy->name );

		// No, so lets add it.
		if ( ! $term ) :
			$term = wp_insert_term( sanitize_text_field( stripslashes_deep( $_REQUEST['term'] ) ), $taxonomy->name, array( 'parent' => $parent ) );
			$term = get_term_by( 'id', $term['term_id'], $taxonomy->name );
			endif;

		// Connect attachment with term.
		wp_set_object_terms( $attachment_id, $term->term_id, $taxonomy->name, true );

		$attachment_terms = wp_get_object_terms(
			$attachment_id,
			$taxonomy->name,
			array(
				'fields' => 'ids',
			)
		);

		ob_start();
		wp_terms_checklist(
			0,
			array(
				'selected_cats' => $attachment_terms,
				'taxonomy'      => $taxonomy->name,
				'checked_ontop' => false,
			)
		);
		$checklist = ob_get_contents();
		ob_end_clean();

		$response['checkboxes'] = $checklist;
		$response['selectbox']  = wp_dropdown_categories(
			array(
				'taxonomy'         => $taxonomy->name,
				'class'            => 'parent-' . $taxonomy->name,
				'id'               => 'parent-' . $taxonomy->name,
				'name'             => 'parent-' . $taxonomy->name,
				'show_option_none' => '- ' . $taxonomy->labels->parent_item . ' -',
				'hide_empty'       => false,
				'echo'             => false,
			)
		);

		die( json_encode( $response ) );
	}

	/**
	 * Save media terms
	 *
	 * @todo security nonce
	 * @since v0.9
	 */
	public function save_media_terms() {

		$attachment_id = (int) sanitize_text_field( stripslashes_deep( $_REQUEST['attachment_id'] ) );

		if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
			die();
		}

		// Check if there is Preset Terms!
		$preset_terms = wp_get_post_terms(
			$attachment_id,
			'ftg-tags',
			array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'fields'  => 'names',
			)
		);

		// New Term Names.
		$term_names = isset( $_REQUEST['term_names'] ) ? array_unique( stripslashes_deep( $_REQUEST['term_names'] ) ) : false;

		// Set Final Terms.
		$new_terms = $term_names && $preset_terms ? array_diff( $term_names, $preset_terms ) : $term_names;

		$added_terms = wp_set_post_terms( $attachment_id, $new_terms, 'ftg-tags', true );
		wp_update_term_count_now( $term_names, 'ftg-tags' );

		$term_json_array = array();

		// If Preset Terms we need to Combine Arrays.
		if ( $preset_terms ) {
			$final_terms = array_combine( $new_terms, $added_terms );

			if ( is_array( $final_terms ) ) {

				foreach ( $final_terms as $term_name => $term_id ) {
					$term_json_array[] = array(
						'termName' => $term_name,
						'termId'   => $term_id,
					);
				}
			}
		} else {
			foreach ( $term_names as $term_name ) {
				$checked_term = term_exists( $term_name, 'ftg-tags' );
				if ( 0 !== $checked_term && null !== $checked_term ) {
					$term_json_array[] = array(
						'termName' => $term_name,
						'termId'   => $checked_term['term_id'],
					);
				}
			}
		}

		die( json_encode( $term_json_array ) );
	}

	/**
	 * Delete Media Term
	 * ajax callback
	 *
	 * @since v.1.3
	 **/
	public function delete_media_term() {

		$attachment_id = (int) sanitize_text_field( stripslashes_deep( $_REQUEST['attachment_id'] ) );
		$taxonomy      = sanitize_text_field( stripslashes_deep( $_REQUEST['taxonomy'] ) );
		$term_id       = (int) sanitize_text_field( stripslashes_deep( $_REQUEST['term_id'] ) );

		if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
			die();
		}

		$response = wp_remove_object_terms( $attachment_id, $term_id, $taxonomy );

		die( json_encode( $response ) );
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @access public
	 * @since v0.9
	 */
	public function admin_enqueue_assets() {
		wp_enqueue_script( 'media-taxonomies', plugins_url( 'media-taxonomies.js', __FILE__ ), array( 'jquery' ), FEED_THEM_GALLERY_PREMIUM_VERSION );
		wp_enqueue_style( 'media-taxonomies', plugins_url( 'media-taxonomies.css', __FILE__ ), array(), FEED_THEM_GALLERY_PREMIUM_VERSION );
	}

	/**
	 * Add taxonomy information
	 *
	 * @access public
	 * @since v0.9
	 */
	public function admin_head() {

		register_taxonomy_for_object_type( 'category', 'attachment' );
		add_post_type_support( 'attachment', 'category' );

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects' ) );

		if ( ! $taxonomies ) {
			return;
		}

		$attachment_taxonomies = array();
		$attachment_terms      = array();

		foreach ( $taxonomies as $taxonomyname => $taxonomy ) :

			$terms = get_terms(
				$taxonomy->name,
				array(
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => true,
				)
			);

			if ( ! $terms ) {
				break;
			}

			$attachment_taxonomies[ $taxonomy->name ] = $taxonomy->labels->name;
			$attachment_terms[ $taxonomy->name ][]    = array(
				'id'    => 0,
				'label' => esc_html__( 'All', 'feed-them-gallery-premium' ) . ' ' . $taxonomy->labels->name,
				'slug'  => '',
			);

			foreach ( $terms as $term ) {
				$attachment_terms[ $taxonomy->name ][] = array(
					'id'    => $term->term_id,
					'label' => $term->name,
					'slug'  => $term->slug,
				);
			}

		endforeach;

		?>
		<script type="text/javascript">
			var mediaTaxonomies = <?php echo json_encode( $attachment_taxonomies ); ?>,
				mediaTerms = <?php echo json_encode( $attachment_terms ); ?>;
		</script>
		<?php

	}


	/**
	 * Add taxonomy checkboxes
	 *
	 * @access public
	 * @param array $fields Fields
	 * @param obj   $post Post obj
	 * @return array Fields
	 * @since v0.9
	 */
	public function attachment_fields_to_edit( $fields, $post ) {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'attachment' === $screen->id ) {
			return $fields;
		}

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects' ) );

		if ( ! $taxonomies ) {
			return $fields;
		}

		foreach ( $taxonomies as $taxonomyname => $taxonomy ) :

			$fields[ $taxonomyname ] = array(
				'label'        => $taxonomy->labels->singular_name,
				'input'        => 'html',
				'html'         => $this->terms_checkboxes( $taxonomy, $post->ID ),
				// 'value' => '',
				// 'helps' => '',
				'show_in_edit' => true,
			);

		endforeach;

		return $fields;

	}


	/**
	 *
	 * Filter attachments in modal box
	 *
	 * @access public
	 * @since v0.9.1
	 */
	public function pre_get_posts( $query ) {
		if ( ! isset( $query->query_vars['post_type'] ) || 'attachment' !== $query->query_vars['post_type'] ) {
			return;
		}

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects', 'ft_gallery' ) );

		if ( ! $taxonomies ) {
			return;
		}

		foreach ( $taxonomies as $taxonomyname => $taxonomy ) :

			if ( isset( $_REQUEST['query'][ $taxonomyname ]['term_slug'] ) ) :
				$query->set( $taxonomyname, $_REQUEST['query'][ $taxonomyname ]['term_slug'] );
			elseif ( isset( $_REQUEST[ $taxonomyname ] ) && is_numeric( $_REQUEST[ $taxonomyname ] ) && 0 != (int) $_REQUEST[ $taxonomyname ] ) :
				$term = get_term_by( 'id', $_REQUEST[ $taxonomyname ], $taxonomyname );
				if ( is_object( $term ) ) {
					set_query_var( $taxonomyname, $term->slug );
				}
			endif;

		endforeach;

	}

	/**
	 *
	 * Register taxonomy
	 *
	 * @access public
	 * @since v0.9
	 */
	public function register_taxonomy() {

		register_taxonomy(
			'ftg-tags',
			array( 'attachment', 'ft_gallery' ),
			array(
				'hierarchical'          => false,
				'labels'                => array(
					'name'              => esc_html( 'Tags', 'taxonomy general name' ),
					'singular_name'     => esc_html( 'Tag', 'taxonomy singular name' ),
					'search_items'      => esc_html( 'Search Tags' ),
					'all_items'         => esc_html( 'All Tags' ),
					'parent_item'       => esc_html( 'Parent Tag' ),
					'parent_item_colon' => esc_html( 'Parent Tag:' ),
					'edit_item'         => esc_html( 'Edit Tag' ),
					'update_item'       => esc_html( 'Update Tag' ),
					'add_new_item'      => esc_html( 'Add New Tag' ),
					'new_item_name'     => esc_html( 'New Tag Name' ),
					'menu_name'         => esc_html( 'Tags' ),
				),
				'show_ui'               => true,
				'public'                => true,
				'query_var'             => true,
				'rewrite'               => false,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_generic_term_count',
			)
		);

	}


	/**
	 * Add custom filters in attachment listing
	 *
	 * @access public
	 * @since v0.9
	 **/
	public function restrict_manage_posts() {

		global $wp_query;

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects' ) );

		if ( ! $taxonomies ) {
			return;
		}

		foreach ( $taxonomies as $taxonomyname => $taxonomy ) :

			wp_dropdown_categories(
				array(
					'show_option_all' => sprintf( 'View all %s', '%1$s = plural, %2$s = singular', 'media-taxonomies', $taxonomy->labels->name, $taxonomy->labels->singular_name ),
					'taxonomy'        => $taxonomyname,
					'name'            => $taxonomyname,
					'orderby'         => 'name',
					'selected'        => isset( $wp_query->query[ $taxonomyname ] ) ? $wp_query->query[ $taxonomyname ] : '',
					'hierarchical'    => false,
					'hide_empty'      => true,
					'hide_if_empty'   => true,
				)
			);

		endforeach;

	}

	/**
	 * Create a terms box
	 *
	 * @access protected
	 * @param obj $taxonomy Taxonomy
	 * @return str HTML output
	 * @since v0.9
	 */
	public function terms_checkboxes( $taxonomy, $post_id ) {

		if ( ! is_object( $taxonomy ) ) :

			$taxonomy = get_taxonomy( $taxonomy );

		endif;

		$terms = get_terms(
			$taxonomy->name,
			array(
				'hide_empty' => false,
			)
		);

		$attachment_terms = wp_get_object_terms(
			$post_id,
			$taxonomy->name,
			array(
				'fields' => 'names',
			)
		);

		ob_start();

		// Post object.
		$post = null;

		// Tags meta box arguments.
		$box = array(
			'id'    => '',
			'title' => '',
			'args'  => array(
				'taxonomy' => $taxonomy->name,
			),
		);

		$defaults = array( 'taxonomy' => 'post_tag' );
		if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
			$args = array();
		} else {
			$args = $box['args'];
		}
		$r                     = wp_parse_args( $args, $defaults );
		$tax_name              = esc_attr( $r['taxonomy'] );
		$taxonomy              = get_taxonomy( $r['taxonomy'] );
		$user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
		$comma                 = _x( ',', 'tag delimiter', 'feed-them-gallery-premium' );
		$terms_to_edit         = get_terms_to_edit( $post->ID, $tax_name );
		if ( ! is_string( $terms_to_edit ) ) {
			$terms_to_edit = '';
		}
		?>
		<div class="tagsdiv" id="<?php echo esc_attr( $tax_name ); ?>">
			<div class="jaxtag">
				<div class="nojs-tags hide-if-js">
					<label for="tax-input-<?php echo esc_attr( $tax_name ); ?>"><?php echo esc_html( $taxonomy->labels->add_or_remove_items ); ?></label>
					<p><textarea name="tax_input[<?php echo esc_attr( $tax_name ); ?>]" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo esc_attr( $tax_name ); ?>" <?php disabled( ! $user_can_assign_terms ); ?> aria-describedby="new-tag-<?php echo esc_attr( $tax_name ); ?>-desc"><?php echo esc_textarea( str_replace( ',', $comma . ' ', $terms_to_edit ) ); // textarea_escaped by esc_attr()! ?></textarea></p>
				</div>
				<?php if ( $user_can_assign_terms ) : ?>
					<div class="ajaxtag hide-if-no-js">
						<label class="screen-reader-text" for="new-tag-<?php echo esc_attr( $tax_name ); ?>"><?php echo esc_html( $taxonomy->labels->add_new_item ); ?></label>
						<p><input data-wp-taxonomy="<?php echo esc_attr( $tax_name ); ?>" type="text" id="new-tag-<?php echo esc_attr( $tax_name ); ?>" name="newtag[<?php echo esc_attr( $tax_name ); ?>]" class="newtag form-input-tip" size="16" autocomplete="off" aria-describedby="new-tag-<?php echo esc_attr( $tax_name ); ?>-desc" value="" />
							<input type="button" class="button tagadd" value="<?php esc_attr_e( 'Add', 'feed-them-gallery-premium' ); ?>" /></p>
					</div>
					<p class="howto" id="new-tag-<?php echo esc_attr( $tax_name ); ?>-desc"><?php echo esc_html( $taxonomy->labels->separate_items_with_commas ); ?></p>
				<?php elseif ( empty( $terms_to_edit ) ) : ?>
					<p><?php echo esc_html( $taxonomy->labels->no_terms ); ?></p>
				<?php endif; ?>
			</div>
			<ul class="tagchecklist" role="list"></ul>
		</div>
		<?php if ( $user_can_assign_terms ) : ?>
			<p class="hide-if-no-js"><button type="button" class="button-link tagcloud-link" id="link-<?php echo esc_attr( $tax_name ); ?>" aria-expanded="false"><?php echo esc_html( $taxonomy->labels->choose_from_most_used ); ?></button></p>
		<?php endif; ?>

		<?php

		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters( 'media-checkboxes', $output, $taxonomy, $terms );

	}
}
