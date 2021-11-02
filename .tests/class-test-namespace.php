<?php
/**
 * Tests for base namespace.
 */

namespace HM\Content_Expiration\Tests;

use HM\Content_Expiration;
use WP_UnitTestCase;

/**
 * Tests for base namespace.
 */
class Test_Namespace extends WP_UnitTestCase {
	/**
	 * Ensure actions and filters are set up.
	 */
	function test_hooks() {
		$this->assertEquals( 1, has_filter( 'init', 'HM\Content_Expiration\register_expire_status' ) );
		$this->assertEquals( 99, has_filter( 'init', 'HM\Content_Expiration\setup_post_types' ) );

		$this->assertEquals( 10, has_filter( 'save_post_post', 'HM\Content_Expiration\schedule_expire' ) );
		$this->assertEquals( 10, has_filter( 'wp_insert_post_data', 'HM\Content_Expiration\post_data' ) );

		$this->assertEquals( 10, has_filter( Content_Expiration\CRON_KEY, 'HM\Content_Expiration\expire_post' ) );
	}

	/**
	 * Ensure the `expire` status is registered as expected.
	 */
	function test_expire_status_registry() {
		Content_Expiration\register_expire_status();

		$statuses = get_post_stati();

		$this->assertEquals( 'expired', $statuses['expired'] );
	}

	/**
	 * Ensure post data filter works as expected.
	 */
	function test_post_data_filter() {
		$data = [
			'post_title' => 'Test',
			'post_status' => 'publish',
			'post_type' => 'post',
		];

		$post_id = wp_insert_post( $data );

		$original_post = get_post( $post_id, ARRAY_A );

		$data = $original_post;
		$data['post_status'] = 'expired';
		$data['post_modified'] = '1970-01-01 00:00:00';
		$data['post_modified_gmt'] = '1970-01-01 00:00:00';

		// Test that modified_date is kept when transitioning status to expired.
		$modified_data = Content_Expiration\post_data( $data, $data );

		$this->assertEquals( $original_post['post_modified'], $modified_data['post_modified'] );
		$this->assertEquals( $original_post['post_modified_gmt'], $modified_data['post_modified_gmt'] );

		// Test that data is untouched when the post type doesn't support expiration.
		$data = $original_post;
		$data['post_status'] = 'expired';
		$data['post_type'] = 'page';
		$modified_data = Content_Expiration\post_data( $data, $data );

		$this->assertEquals( $data, $modified_data );
	}
}
