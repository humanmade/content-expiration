<?php
/**
 * HM Plugin Tester bootstrap configuration.
 *
 * @see https://github.com/humanmade/plugin-tester
 */

declare( strict_types=1 );

require '/wp-phpunit/includes/functions.php';

tests_add_filter( 'muplugins_loaded', function () {
	require dirname( __FILE__ ) . '/../plugin.php';
} );

require '/wp-phpunit/includes/bootstrap.php';
