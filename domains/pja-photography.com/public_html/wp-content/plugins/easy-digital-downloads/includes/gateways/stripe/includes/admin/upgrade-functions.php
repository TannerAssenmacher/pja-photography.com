<?php

/**
 * Handles automatic upgrades behind the scenes.
 *
 * @since 2.8.10
 */
add_action( 'admin_init', function() {
	/*
	 * Move license data to new option, after product name change.
	 * @link https://github.com/awesomemotive/edd-stripe/issues/715
	 */
	$license_key = edd_get_option( 'edd_stripe_payment_gateway_license_key' );
	if ( $license_key ) {
		edd_update_option( 'edd_stripe_pro_payment_gateway_license_key', sanitize_text_field( $license_key ) );
		edd_delete_option( 'edd_stripe_payment_gateway_license_key' );

		$license_status = get_option( 'edd_stripe_payment_gateway_license_active' );
		if ( $license_status ) {
			update_option( 'edd_stripe_pro_payment_gateway_license_active', $license_status );
			delete_option( 'edd_stripe_payment_gateway_license_active' );
		}
	}
} );

/**
 * Stripe Upgrade Notices
 *
 * @since 2.6
 *
 */
function edd_stripe_upgrade_notices() {

	global $wpdb;

	// Don't show notices on the upgrades page
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'edd-upgrades' ) {
		return;
	}

	if ( ! edd_has_upgrade_completed( 'stripe_customer_id_migration' ) ) {

		$has_stripe_customers = $wpdb->get_var( "SELECT count(user_id) FROM $wpdb->usermeta WHERE meta_key IN ( '_edd_stripe_customer_id', '_edd_stripe_customer_id_test' ) LIMIT 1" );
		$needs_upgrade = ! empty( $has_stripe_customers );

		if( ! $needs_upgrade ) {
			edd_set_upgrade_complete( 'stripe_customer_id_migration' );
			return;
		}

		printf(
			'<div class="updated">' .
			'<p>' .
			/* translators: %s Upgrade link. */
			__( 'Easy Digital Downloads - Stripe Gateway needs to upgrade the customers database; <a href="%s">click here to start the upgrade</a>. <a href="#" onClick="jQuery(this).parent().next(\'p\').slideToggle()">Learn more about this upgrade</a>', 'easy-digital-downloads' ) .
			'</p>' .
			'<p style="display: none;">' .
			__( '<strong>About this upgrade:</strong><br />This upgrade will improve the reliability of associating purchase records with your existing customer records in Stripe by changing their Stripe Customer IDs to be stored locally on their EDD customer record, instead of their user record.', 'easy-digital-downloads' ) .
			'<br /><br />' .
			__( '<strong>Advanced User?</strong><br />This upgrade can also be run via WPCLI with the following command:<br /><code>wp edd-stripe migrate_customer_ids</code>', 'easy-digital-downloads' ) .
			'</p>' .
			'</div>',
			esc_url( admin_url( 'index.php?page=edd-upgrades&edd-upgrade=stripe_customer_id_migration' ) )
		);
	}

}
add_action( 'admin_notices', 'edd_stripe_upgrade_notices' );

/**
 * Migrates Stripe Customer IDs from the usermeta table to the edd_customermeta table.
 *
 * @since  2.6
 * @return void
 */
function edd_stripe_customer_id_migration() {
	global $wpdb;

	if ( ! current_user_can( 'manage_shop_settings' ) ) {
		wp_die( __( 'You do not have permission to do shop upgrades', 'easy-digital-downloads' ), __( 'Error', 'easy-digital-downloads' ), array( 'response' => 403 ) );
	}

	ignore_user_abort( true );

	$step   = isset( $_GET['step'] )   ? absint( $_GET['step'] )   : 1;
	$number = isset( $_GET['number'] ) ? absint( $_GET['number'] ) : 10;
	$offset = $step == 1 ? 0 : ( $step - 1 ) * $number;

	$total = isset( $_GET['total'] ) ? absint( $_GET['total'] ) : false;
	if ( empty( $total ) || $total <= 1 ) {
		$total_sql = "SELECT COUNT(user_id) as total_users FROM $wpdb->usermeta WHERE meta_key IN ( '_edd_stripe_customer_id', '_edd_stripe_customer_id_test' )";
		$results   = $wpdb->get_row( $total_sql );
		$total     = $results->total_users;
	}

	$stripe_user_meta = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM $wpdb->usermeta WHERE meta_key IN ( '_edd_stripe_customer_id', '_edd_stripe_customer_id_test' ) ORDER BY umeta_id ASC LIMIT %d,%d;",
			$offset,
			$number
		)
	);

	if ( $stripe_user_meta ) {

		foreach ( $stripe_user_meta as $stripe_user ) {

			$user  = get_userdata( $stripe_user->user_id );
			$email = $user->user_email;

			$customer = new EDD_Customer( $email );

			// If we don't have a customer on this site, just move along.
			if ( ! $customer->id > 0 ) {
				continue;
			}

			$stripe_customer_id = $stripe_user->meta_value;

			// We should try and use a recurring ID if one exists for this user
			if ( class_exists( 'EDD_Recurring_Subscriber' ) ) {
				$subscriber         = new EDD_Recurring_Subscriber( $customer->id );
				$stripe_customer_id = $subscriber->get_recurring_customer_id( 'stripe' );
			}

			$customer->update_meta( $stripe_user->meta_key, $stripe_customer_id );

		}

		$step ++;
		$redirect = add_query_arg( array(
			'page'        => 'edd-upgrades',
			'edd-upgrade' => 'stripe_customer_id_migration',
			'step'        => $step,
			'number'      => $number,
			'total'       => $total
		), admin_url( 'index.php' ) );

		wp_redirect( $redirect );
		exit;

	} else {

		update_option( 'edds_stripe_version', preg_replace( '/[^0-9.].*/', '', EDD_STRIPE_VERSION ) );
		edd_set_upgrade_complete( 'stripe_customer_id_migration' );
		delete_option( 'edd_doing_upgrade' );

		wp_redirect( admin_url() );
		exit;

	}

}
add_action( 'edd_stripe_customer_id_migration', 'edd_stripe_customer_id_migration' );
