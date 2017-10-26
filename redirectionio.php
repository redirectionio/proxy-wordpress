<?php

/**
 * Plugin Name: redirection.io
 * Plugin URI: https://redirection.io
 * Description: Proxy client for redirection.io | Put an end to 404 errors - Track HTTP errors and setup useful HTTP redirections
 * Version: 0.1
 * Author: redirection.io
 * Author Website: https://redirection.io
 */

namespace RedirectionIO\Client\Wordpress;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/sdk/vendor/autoload.php';

use RedirectionIO\Client\Client;
use RedirectionIO\Client\HTTPMessage\ServerRequest;

class RedirectionIO
{
    public function __construct()
    {
        register_activation_hook(__FILE__, 'setUpPlugin');
        register_uninstall_hook(__FILE__, 'uninstallPlugin');
        
        add_action('plugins_loaded', [$this, 'findRedirect']);
        add_action('admin_menu', [$this, 'setUpAdminPage']);
        add_action('admin_init', [$this, 'registerAdminSettings']);
    }

    public function setUpPlugin()
    {
        add_option('redirectionio', ['host' => '', 'port' => '']);
    }

    public function uninstallPlugin()
    {
        delete_option('redirectionio');
    }

    public function setUpAdminPage()
    {
        add_options_page('redirection.io', 'redirection.io', 'manage_options', 'redirectionio', [$this, 'createAdminPage']);
    }

    public function createAdminPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        require_once __DIR__ . '/wordpress/admin_template.php';
    }

    public function registerAdminSettings()
    {
        register_setting(
            'redirectionio-group',
            'redirectionio',
            [$this, 'sanitizeInput']
        );

        add_settings_section(
            'redirectionio-section',
            'Settings',
            [$this, 'printSection'],
            'redirectionio'
        );

        add_settings_field(
            'host',
            'Host',
            [$this, 'printHostField'],
            'redirectionio',
            'redirectionio-section'
        );

        add_settings_field(
            'port',
            'Port',
            [$this, 'printPortField'],
            'redirectionio',
            'redirectionio-section'
        );
    }

    public function printSection()
    {
        echo '<p>Please set here the connection options of your redirection.io agent [required].</p>';
    }

    public function printHostField()
    {
        $options = get_option('redirectionio');
        echo "<input id='redirectionio_host' name='redirectionio[host]' size='40' type='text' value='{$options['host']}' />";
    }

    public function printPortField()
    {
        $options = get_option('redirectionio');
        echo "<input id='redirectionio_port' name='redirectionio[port]' size='40' type='text' value='{$options['port']}' />";
    }

    public function sanitizeInput($input)
    {
        $newInput = [];

        if (isset($input['host'])) {
            $newInput['host'] = sanitize_text_field($input['host']);
        }

        if (isset($input['port'])) {
            $newInput['port'] = sanitize_text_field($input['port']);
        }

        return $newInput;
    }

    public function findRedirect()
    {
        $options = get_option('redirectionio');

        if ($options['host'] === '' || $options['port'] === '') {
            return;
        }

        $connectionOptions = ['agent' => [
            'host' => $options['host'],
            'port' => $options['port']
        ]];
        
        $client = new Client($connectionOptions);
        $request = new ServerRequest(
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['HTTP_REFERER']
        );
        
        $response = $client->findRedirect($request);

        if (null === $response) {
            return;
        }

        wp_redirect($response->getLocation(), $response->getStatusCode());
        exit;
    }
}

new RedirectionIO();
