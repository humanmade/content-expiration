<?php
/**
 * Tests for base namespace.
 */

namespace HM\Content_Expiration\Tests;

use HM\Content_Expiration\REST_API;
use WP_UnitTestCase;

/**
 * Tests for REST_API namespace.
 */
class Test_REST_API extends WP_UnitTestCase {
	/**
	 * Ensure actions and filters are set up.
	 */
	function test_hooks() {
		$this->assertEquals( 10, has_filter( 'rest_api_init', 'HM\Content_Expiration\REST_API\register_endpoint' ) );
	}

	/**
	 * Ensure the REST endpoint is registered.
	 */
	function test_register_endpoint() {
		$rest_server = rest_get_server();

		$namespaces = $rest_server->get_namespaces();

		$this->assertContains( 'content-expiration/v1', $namespaces );

		$routes = $rest_server->get_routes( 'content-expiration/v1' );

		$this->assertContains( '/content-expiration/v1/types', array_keys( $routes ) );
	}

	/**
	 * Ensure get_types returns the expected result.
	 */
	function test_get_types() {
		$post_types = REST_API\get_types();

		$posts_type = get_post_type_object( 'post' );

		$this->assertSame(
			[
				[
					'label' => $posts_type->label,
					'name' => $posts_type->name,
				],
			],
			$post_types
		);
	}
}
