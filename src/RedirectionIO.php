<?php

namespace RedirectionIO\Client\Wordpress;

use RedirectionIO\Client\Client;
use RedirectionIO\Client\HttpMessage\Request;
use RedirectionIO\Client\HttpMessage\Response;

class RedirectionIO
{
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'findRedirect']);
    }

    public function setUp()
    {
        add_option('redirectionio', [
            'connections' => [
                [
                    'name' => '',
                    'host' => '',
                    'port' => '',
                ],
            ],
            'doNotRedirectAdmin' => true,
        ]);
    }

    public function findRedirect()
    {
        $options = get_option('redirectionio');
        $connections = [];

        foreach ($options['connections'] as $option) {
            foreach ($option as $key => $val) {
                if ($key === 'name') {
                    continue;
                }

                $connections[$option['name']][$key] = $val;
            }
        }

        $client = new Client($connections);
        $request = new Request(
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['HTTP_REFERER']
        );

        if ($this->isAdminPage($request) && $options['doNotRedirectAdmin']) {
            return;
        }

        $response = $client->findRedirect($request);

        if (null === $response) {
            $response = new Response(http_response_code());
            $client->log($request, $response);

            return;
        }

        $client->log($request, $response);
        wp_redirect($response->getLocation(), $response->getStatusCode());
        exit;
    }

    private function isAdminPage($request)
    {
        $adminRoot = str_replace(get_site_url(), '', get_admin_url());
        $requestPath = substr($request->getPath(), 0, strlen($adminRoot));

        if ($adminRoot === $requestPath) {
            return true;
        }

        return false;
    }
}
