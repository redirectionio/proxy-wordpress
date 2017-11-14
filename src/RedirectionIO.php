<?php

namespace RedirectionIO\Client\Wordpress;

use RedirectionIO\Client\Client;
use RedirectionIO\Client\Exception\AgentNotFoundException;
use RedirectionIO\Client\HttpMessage\RedirectResponse;
use RedirectionIO\Client\HttpMessage\Request;
use RedirectionIO\Client\HttpMessage\Response;

class RedirectionIO
{
    public function __construct()
    {
        register_activation_hook(__FILE__, [$this, 'setUp']);
        add_action('plugins_loaded', [$this, 'findRedirect']);
    }

    public function setUp()
    {
        add_option('redirectionio', [[
            'name' => '',
            'host' => '',
            'port' => '',
        ]]);
    }

    public function findRedirect()
    {
        $options = get_option('redirectionio');
        $connectionOptions = [];

        foreach ($options as $option) {
            foreach ($option as $key => $val) {
                if ($key === 'name') {
                    continue;
                }

                $connectionOptions[$option['name']][$key] = $val;
            }
        }

        $client = new Client($connectionOptions);
        $request = new Request(
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['HTTP_REFERER']
        );
        
        try {
            $response = $client->findRedirect($request);
        } catch (AgentNotFoundException $e) {
            return;
        }

        if (null === $response) {
            $response = new Response(http_response_code());
            $client->log($request, $response);

            return;
        }

        $client->log($request, $response);
        wp_redirect($response->getLocation(), $response->getStatusCode());
        exit;
    }
}
