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
}
