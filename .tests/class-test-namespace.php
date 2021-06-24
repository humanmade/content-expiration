<?php

use HM\Content_Expiration;

class Test_Namespace extends WP_UnitTestCase {
    /**
     * Ensure the `expire` status is registered as expected.
     */
    function test_expire_status_registry() {
        Content_Expiration\register_expire_status();

        $statuses = get_post_stati();

        $this->assertEquals( 'expired', $statuses['expired'] );
    }
}
