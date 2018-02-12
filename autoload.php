<?php

/**
 * Plugin Name: redirection.io
 * Plugin URI: https://redirection.io
 * Description: Proxy client for redirection.io | Put an end to 404 errors - Track HTTP errors and setup useful HTTP redirections
 * Version: 0.1
 * Author: redirection.io
 * Author Website: https://redirection.io
 * Text Domain: redirectionio
 * Domain Path: /wordpress/languages.
 */

namespace RedirectionIO\Client\Wordpress;

// Prevent public user to directly access .php files through url
if (!defined('ABSPATH')) {
    exit;
}

// Call autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Instantiate plugin
new RedirectionIO();
new RedirectionIOSettingsPage();

register_activation_hook(__FILE__, ['RedirectionIO\Client\Wordpress\RedirectionIO', 'setUp']);
