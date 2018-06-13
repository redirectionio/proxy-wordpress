<?php

namespace RedirectionIO\Client\Wordpress;

use RedirectionIO\Client\Sdk\Client;
use RedirectionIO\Client\Sdk\HttpMessage\Request;
use RedirectionIO\Client\Sdk\HttpMessage\Response;

/**
 * Main plugin file.
 *
 * This class is the core logic of the plugin.
 */
class RedirectionIO
{
    private $client;

    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'findRedirect']);
        add_action('template_redirect', [$this, 'log']);
    }

    public function setUp()
    {
        add_option('redirectionio', [
            'connections' => [
                [
                    'name' => '',
                    'remote_socket' => '',
                ],
            ],
            'doNotRedirectAdmin' => true,
        ]);
    }

    public function findRedirect()
    {
        $options = get_option('redirectionio');
        $connections = [];

        if (false === $options || !isset($options['connections'])) {
            return false;
        }

        foreach ($options['connections'] as $connection) {
            $connections[$connection['name']] = $connection['remote_socket'];
        }

        $this->client = new Client($connections);
        $scheme = 'http';

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        } elseif (!empty($_SERVER['HTTPS'])) {
            $scheme = 'https';
        }

        $request = new Request(
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['HTTP_REFERER'],
            $scheme
        );

        if ($this->isAdminPage($request) && $options['doNotRedirectAdmin']) {
            return false;
        }

        $response = $this->client->findRedirect($request);

        if (null === $response) {
            return false;
        }

        $this->client->log($request, $response);

        if ($response->getStatusCode() === 410) {
            define('DONOTCACHEPAGE', true); // WP Super Cache and W3 Total Cache recognise this
            status_header(410);
        } else {
            wp_redirect($response->getLocation(), $response->getStatusCode());
        }

        $this->exitCode();
    }

    public function log()
    {
        $request = new Request(
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['HTTP_REFERER'],
            $scheme
        );
        $response = new Response(http_response_code());

        $this->client->log($request, $response);
    }

    public function exitCode()
    {
        exit;
    }

    /**
     * Check if the requested page belongs to admin area.
     *
     * @param Request $request
     */
    private function isAdminPage(Request $request)
    {
        $adminRoot = str_replace(get_site_url(), '', get_admin_url());
        $requestPath = substr($request->getPath(), 0, strlen($adminRoot));

        if ($adminRoot === $requestPath) {
            return true;
        }

        return false;
    }
}
