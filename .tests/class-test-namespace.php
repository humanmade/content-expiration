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
			'ID' => 1,
			'post_status' => 'expired',
			'post_type' => 'post',
		];

		$postarr = [
			'ID' => 1,
			'post_status' => 'publish',
			'post_type' => 'post',
			'post_modified' => 'test_value',
			'post_modified_gmt' => 'test_value_gmt',
		];

		// Test that modified_date is kept when transitioning status to expired.
		$modified_data = Content_Expiration\post_data( $data, $postarr );

		$this->assertEquals( 'test_value', $modified_data['post_modified'] );
		$this->assertEquals( 'test_value_gmt', $modified_data['post_modified_gmt'] );

		// Test that post is published when saving expired post.
		$postarr['post_status'] = 'expired';
		$modified_data = Content_Expiration\post_data( $data, $postarr );

		$this->assertEquals( 'publish', $modified_data['post_status'] );

		// Test that data is untouched when the post type doesn't support expiration.
		$postarr['post_type'] = 'page';
		$modified_data = Content_Expiration\post_data( $data, $postarr );

		$this->assertEquals( $data, $modified_data );
	}
}
