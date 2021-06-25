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

// Add content expiration support to posts so that we can test functionality.
tests_add_filter( 'plugins_loaded', function () {
	add_post_type_support( 'post', 'content-expiration' );
}, 4 );

require '/wp-phpunit/includes/bootstrap.php';
