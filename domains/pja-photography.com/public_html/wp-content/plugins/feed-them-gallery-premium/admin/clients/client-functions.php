<?php
/**
 * Client Functions
 *
 * @package     Feed Them Gallery Clients Manager
 * @subpackage  Clients/Functions
 * @copyright   Copyright (c) 2020, SlickRemix
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Adds the albums table to the client view in Clients Manager.
 *
 * @since	1.1.5
 * @param	object	$client	FTG CM Client object
 * @return	void
 */
function ftg_premium_add_clients_manager_albums_table( $client )	{
	?>
	<h3><?php _e( 'Client Albums', 'feed-them-gallery-premium' ); ?></h3>
	<?php $albums = $client->get_items( 'albums' ); ?>
	<table class="wp-list-table widefat striped albums">
		<thead>
			<tr>
				<th><?php _e( 'Album', 'feed-them-gallery-premium' ); ?></th>
				<th><?php _e( 'Actions', 'feed-them-gallery-premium' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $albums ) ) : ?>
				<?php foreach ( $albums as $album ) : ?>
					<?php $actions = FTG_CM_NAMESPACE\FTG_CM_Admin_Client_Functions::ftg_cm_get_client_gallery_action_links( $client, $client->user_id, $album->ID, 'client' ); ?>
					<tr>
						<td><?php echo $album->post_title; ?></td>
						<td><?php echo implode( '&nbsp;&#124;&nbsp;', $actions ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="2">
						<?php _e( 'No albums Found', 'feed-them-gallery-premium' ); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<?php
} // ftg_premium_add_clients_manager_albums_table
add_action( 'ftg_cm_client_after_tables', 'ftg_premium_add_clients_manager_albums_table' );
