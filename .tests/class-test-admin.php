<?php
/**
 * Tests for base namespace.
 */

namespace HM\Content_Expiration\Tests;

use HM\Content_Expiration\Admin;
use WP_UnitTestCase;

/**
 * Tests for Admin namespace.
 */
class Test_Admin extends WP_UnitTestCase {
	/**
	 * Ensure the `Expires` column is added successfully.
	 */
	function test_expire_column_filter() {
		$columns = Admin\column_head( [] );

		$this->assertArrayHasKey( 'expires', $columns );
	}

	/**
	 * Ensure the `Expires` column is sortable.
	 */
	function test_expire_column_sortable() {
		$columns = Admin\sortable_columns( [] );

		$this->assertEquals( 'post_modified', $columns['expires'] );
	}

	/**
	 * Ensure all columns actions and filters are set up.
	 */
	function test_hooks() {
		$this->assertEquals( 10, has_filter( 'init', 'HM\Content_Expiration\Admin\setup_columns' ) );

		$this->assertEquals( 10, has_filter( 'manage_post_posts_columns', 'HM\Content_Expiration\Admin\column_head' ) );
		$this->assertEquals( 10, has_action( 'manage_post_posts_custom_column', 'HM\Content_Expiration\Admin\column_content' ) );
		$this->assertEquals( 10, has_filter( 'manage_edit-post_sortable_columns', 'HM\Content_Expiration\Admin\sortable_columns' ) );
	}
}
