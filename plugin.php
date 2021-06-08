<?php
/**
 * Plugin Name: Content Expiration
 * Description: Expire processes in HM Playbook after 3 months and remind process owners to review them.
 * Author: Human Made Limited
 * Author URI: https://humanmade.com/
 * Version: 1.0
 */

namespace HM\Content_Expiration;

require __DIR__ . '/inc/namespace.php';
require __DIR__ . '/inc/workflows.php';

bootstrap();
Workflows\bootstrap();
