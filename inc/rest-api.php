<?php
/**
 * Add REST endpoint for listing expiring post types.
 */

namespace HM\Content_Expiration\REST_API;

use const HM\Content_Expiration\SUPPORTS_KEY;

/**
 * Register actions and filters.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'rest_api_init', __NAMESPACE__ . '\\register_endpoint' );
}

/**
 * Register endpoint for listing post types that support content expiration.
 *
 * @return void
 */
function register_endpoint() {
	register_rest_route(
		'content-expiration/v1',
		'/types/',
		[
			'methods' => 'GET',
			'callback' => __NAMESPACE__ . '\\get_types',
  	]
	);
}

/**
 * Return array of post types that support content expiration for use in REST API response.
 *
 * @return array
 */
function get_types() {
	$post_types = array_map(
		function ( $post_type) {
			$post_type_object = get_post_type_object( $post_type );
			return [
				'label' => $post_type_object->label,
				'name' => $post_type,
			];
		},
		get_post_types_by_support( SUPPORTS_KEY )
	);

	return $post_types;
}
