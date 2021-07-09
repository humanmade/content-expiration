<?php
/**
 * Plugin Name: Content Expiration
 * Description: Expire content of any post type after set period, based on post modified time.
 * Author: Human Made Limited
 * Author URI: https://humanmade.com/
 * Version: 1.0
 */

namespace HM\Content_Expiration;

require __DIR__ . '/inc/namespace.php';
require __DIR__ . '/inc/admin.php';
require __DIR__ . '/inc/rest-api.php';
require __DIR__ . '/inc/workflows.php';

bootstrap();
Admin\bootstrap();
REST_API\bootstrap();
Workflows\bootstrap();
