<?php
/**
 * Add column to list tables of post types that support content expiration.
 */

namespace HM\Content_Expiration\Admin;

use const HM\Content_Expiration\SUPPORTS_KEY;
use function HM\Content_Expiration\get_expire_timestamp;

/**
 * Register actions and filters.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\setup_columns' );
}

/**
 * Setup content expiration columns for post types that support expiration.
 *
 * @return void
 */
function setup_columns() {
	foreach ( get_post_types_by_support( SUPPORTS_KEY ) as $post_type ) {
		add_filter( "manage_{$post_type}_posts_columns", __NAMESPACE__ . '\\column_head' );
		add_action( "manage_{$post_type}_posts_custom_column", __NAMESPACE__ . '\\column_content', 10, 2 );
		add_filter( "manage_edit-{$post_type}_sortable_columns", __NAMESPACE__ . '\\sortable_columns' );
	}
}

/**
 * Define expiration column.
 *
 * @param array $columns Array of posts list table columns.
 * @return array
 */
function column_head( $columns ) {
	$columns['expires'] = __( 'Expires', 'content-expiration' );

	return $columns;
}

/**
 * Output content of expiration column.
 *
 * @param string $column Column key.
 * @param int $post_id Post ID.
 * @return void
 */
function column_content( $column, $post_id ) {
	if ( $column !== 'expires' ) {
		return;
	}

	$timestamp = get_expire_timestamp( $post_id );

	// Mimic date formatting from core posts list table.
	$formatted_time = sprintf(
		/* translators: 1: Post date, 2: Post time. */
		__( '%1$s at %2$s' ),
		/* translators: Post date format. See https://www.php.net/manual/datetime.format.php */
		date_i18n( __( 'Y/m/d' ), $timestamp, true ),
		/* translators: Post time format. See https://www.php.net/manual/datetime.format.php */
		date_i18n( __( 'g:i a' ), $timestamp, true )
	);

	if ( $timestamp > time() ) {
		printf(
			'<span>%1$s<br />%2$s</span>',
			esc_html__( 'Expires on', 'content-expiration' ),
			esc_html( $formatted_time )
		);
	} else {
		printf(
			'<strong class="wp-ui-text-highlight">%1$s<br />%2$s</strong>',
			esc_html__( 'Expired on', 'content-expiration' ),
			esc_html( $formatted_time )
		);
	}
}

/**
 * Allow sorting by expiration column.
 *
 * Since the expiration time is based on post modified time, we can just sort by modified time.
 *
 * @param array $columns Array of sortable table columns.
 * @return array
 */
function sortable_columns( $columns ) {
	$columns['expires'] = 'post_modified';
	return $columns;
}
