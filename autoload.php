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

// Call redirection.io PHP SDK autoloader
require_once __DIR__ . '/sdk/vendor/autoload.php';

// Create redirection.io WP plugin autoloader
spl_autoload_register(function ($class) {
    $namespace = 'RedirectionIO\Client\Wordpress';

    if (false === strpos($class, $namespace)) {
        return;
    }

    $directory = realpath(plugin_dir_path(__FILE__)) . '/src/';
    $file = str_replace($namespace . '\\', '', $class) . '.php';
    require_once $directory . $file;
});

// Instantiate plugin
new RedirectionIO();
new RedirectionIOSettingsPage();
