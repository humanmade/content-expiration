<?php
/**
 * Tests for base namespace.
 */

namespace HM\Content_Expiration\Tests;

use HM\Content_Expiration\Admin;
use WP_UnitTestCase;

/**
 * Tests for base namespace.
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
}
