<?php
/**
 * Expire processes in HM Playbook after 3 months and remind process owners to review them.
 */

namespace HM\Content_Expiration;

use DateTime, DateInterval;

const CRON_KEY = 'content_expiration_cron';
const SUPPORTS_KEY = 'content-expiration';

/**
 * Register actions and filters.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_expire_status' );
	add_action( 'wp_loaded', __NAMESPACE__ . '\\setup_post_types' );

	add_action( CRON_KEY, __NAMESPACE__ . '\\expire_post' );

	add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\\post_data', 10, 2 );
}

/**
 * Hook into save_post for post types that support content expiration.
 *
 * @return void
 */
function setup_post_types() {
	foreach ( get_post_types_by_support( SUPPORTS_KEY ) as $post_type ) {
		add_action( "save_post_{$post_type}", __NAMESPACE__ . '\\schedule_expire' );
	}
}

/**
 * Register expire post status.
 *
 * @return void
 */
function register_expire_status() {
	register_post_status(
		'expired',
		[
			'label'       => _x( 'Expired', 'post status', 'content-expiration' ),
			'public'      => true,
			/* translators: %s: Number of expired posts. */
			'label_count' => _n_noop(
				'Expired <span class="count">(%s)</span>',
				'Expired <span class="count">(%s)</span>',
				'content-expiration'
			),
		]
	);
}

/**
 * Expire post.
 *
 * Invoked by cron 'expire_post_cron' event.
 *
 * @param int $post_id Post ID.
 */
function expire_post( $post_id ) {
	if ( ! post_type_supports( get_post_type( $post_id ), SUPPORTS_KEY ) ) {
		return;
	}

	if ( get_post_status( $post_id ) !== 'publish' ) {
		return;
	}

	$post = get_post( $post_id );
	if ( empty( $post ) || is_wp_error( $post ) ) {
		return;
	}

	$expire_timestamp = get_expire_timestamp( $post_id );

	if ( $expire_timestamp > time() ) {
		schedule_expire( $post_id, $expire_timestamp );
		return;
	}

	$postarr = [
		'ID' => $post_id,
		'post_status' => 'expired',
	];

	return wp_update_post( $postarr );
}

/**
 *  Get post expire timestamp.
 *
 *  @param  int    $post_id Post ID.
 *  @return int Timestamp.
 */
function get_expire_timestamp( $post_id ) {
	$last_modified_timestamp = get_post_modified_time( 'U', true, $post_id );

	$last_modified_date = new DateTime();
	$last_modified_date->setTimestamp( $last_modified_timestamp );

	/**
	 * Filters the content expiration period.
	 *
	 * Alows different expiration periods per post type or for individual posts.
	 * The default period is 3 months, the format of the returned value should be
	 * a valid interval specification for `DateInterval::__construct`.
	 *
	 * @param string $period Expiration period.
	 * @param string $post_type Post type.
	 * @param int $post_id Post ID.
	 */
	$expire_interval = apply_filters( 'expiration_period', 'P3M', get_post_type( $post_id ), $post_id );
	$expire_date = $last_modified_date->add( new DateInterval( $expire_interval ) );

	return (int) $expire_date->format( 'U' );
}

/**
 * Unschedule expireing post.
 *
 * @param int $post_id Post ID.
 */
function unschedule_expire( $post_id ) {
	wp_clear_scheduled_hook( CRON_KEY, [ $post_id ] );
}

/**
 *  Schedule expireing post.
 *
 *  @param  int $post_id   Post ID.
 */
function schedule_expire( $post_id ) {
	unschedule_expire( $post_id );

	if ( ! post_type_supports( get_post_type( $post_id ), SUPPORTS_KEY ) ) {
		return;
	}

	if ( get_post_status( $post_id ) !== 'publish' ) {
		return;
	}

	$timestamp = get_expire_timestamp( $post_id );

	if ( ! empty( $timestamp ) ) {
		wp_schedule_single_event( $timestamp, CRON_KEY, [ $post_id ] );
	}
}

/**
 * Filter post data when expiring post or updating expired post.
 *
 * Keep post_modified timestamp when expiring post and
 * change post_status to publish when saving expired posts.
 *
 * @param array $data Array of processed post data to be inserted/update into the database.
 * @param array $postarr Array of unprocessed data as passed to `wp_insert_post()`.
 * @return array
 */
function post_data( $data, $postarr ) {
	if ( empty( $postarr['ID'] ) ) {
		return $data;
	}

	if ( ! post_type_supports( get_post_type( $post_id ), SUPPORTS_KEY ) ) {
		return $data;
	}

	$old_status = get_post_status( $postarr['ID'] );

	// Keep post_modified timestamp when expiring post.
	if ( $old_status === 'publish' && $data['post_status'] === 'expired' ) {
		$data['post_modified'] = $postarr['post_modified'];
		$data['post_modified_gmt'] = $postarr['post_modified_gmt'];
	}

	// Change post_status to publish when saving expired posts.
	if ( $old_status === 'expired' ) {
		$data['post_status'] = 'publish';
	}

	return $data;
}
