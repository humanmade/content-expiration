<?php
/**
 * Plugin Name: Content Expiration
 * Description: Expire content of any post type after set period, based on post modified time.
 * Author: Human Made Limited
 * Author URI: https://humanmade.com/
 * Version: 1.0
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace HM\Content_Expiration;

require __DIR__ . '/inc/namespace.php';
require __DIR__ . '/inc/admin.php';
require __DIR__ . '/inc/workflows.php';

bootstrap();
Admin\bootstrap();
Workflows\bootstrap();
