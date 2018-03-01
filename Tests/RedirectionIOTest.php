<?php

/**
 * Mock some needed functions.
 */

namespace RedirectionIO\Client\Wordpress;

use RedirectionIO\Client\Wordpress\Tests\RedirectionIOTest;

function add_action()
{
}

function get_option()
{
    return RedirectionIOTest::$connections;
}

function get_site_url()
{
}

function get_admin_url()
{
}

function http_response_code()
{
    RedirectionIOTest::$isRedirect = false;
}

function wp_redirect($location, $statusCode)
{
    RedirectionIOTest::$isRedirect = true;
    RedirectionIOTest::$redirect = [
        'location' => $location,
        'statusCode' => $statusCode,
    ];
}

namespace RedirectionIO\Client\Wordpress\Tests;

use PHPUnit\Framework\TestCase;
use RedirectionIO\Client\Wordpress\RedirectionIO;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @covers \RedirectionIO\Client\Wordpress\RedirectionIO
 */
class RedirectionIOTest extends TestCase
{
    public static $connections = [
        'connections' => [
            [
                'name' => 'agent',
                'host' => 'localhost',
                'port' => 3100,
            ],
        ],
        'doNotRedirectAdmin' => false,
    ];

    public static $redirect = [];
    public static $isRedirect = null;

    private $rio;

    public static function setUpBeforeClass()
    {
        static::startAgent();
    }

    public function setUp()
    {
        // clear
        self::$redirect = [];
        $_SERVER = [];

        $this->rio = $this->getMockBuilder(RedirectionIO::class)
            ->setMethods(['exitCode'])
            ->getMock();
    }

    public function testWhenRedirectRuleExists()
    {
        $this->initializeServerVars(['path' => '/foo']);

        $this->rio->findRedirect();

        $this->assertTrue(self::$isRedirect);
        $this->assertSame('/bar', self::$redirect['location']);
        $this->assertSame(301, self::$redirect['statusCode']);
    }

    public function testWhenRedirectRuleNotExists()
    {
        $this->initializeServerVars(['path' => '/hello']);

        $this->rio->findRedirect();

        $this->assertSame(false, self::$isRedirect);
    }

    public function testWhenAgentDown()
    {
        self::$connections = [
            'connections' => [
                [
                    'name' => 'agent',
                    'host' => 'unknown-host',
                    'port' => 80,
                ],
            ],
            'doNotRedirectAdmin' => false,
        ];

        $this->initializeServerVars(['path' => '/foo']);

        $this->assertSame(false, $this->rio->findRedirect());
    }

    private static function startAgent($port = 3100)
    {
        $finder = new PhpExecutableFinder();
        if (false === $binary = $finder->find()) {
            throw new \RuntimeException('Unable to find PHP binary to run a fake agent.');
        }

        // find fake_agent location
        $parentFolder = substr(__DIR__, -15, -6);
        $fakeAgent = ('wordpress' === $parentFolder) ?
            __DIR__ . '/../../sdk/src/Resources/fake_agent.php' :
            './vendor/redirectionio/proxy-sdk/src/Resources/fake_agent.php';

        $agent = new Process([$binary, $fakeAgent]);
        $agent
            ->inheritEnvironmentVariables(true)
            ->setEnv(['RIO_PORT' => $port])
            ->start()
        ;

        static::waitUntilProcReady($agent);

        if ($agent->isTerminated() && !$agent->isSuccessful()) {
            throw new ProcessFailedException($agent);
        }

        register_shutdown_function(function () use ($agent) {
            $agent->stop();
        });

        return $agent;
    }

    private function initializeServerVars($options = [])
    {
        $_SERVER['HTTP_HOST'] = isset($options['host']) ? $options['host'] : 'host1.com';
        $_SERVER['REQUEST_URI'] = isset($options['path']) ? $options['path'] : '';
        $_SERVER['HTTP_USER_AGENT'] = isset($options['user_agent']) ? $options['user_agent'] : 'redirection-io-client/0.0.1';
        $_SERVER['HTTP_REFERER'] = isset($options['referer']) ? $options['referer'] : 'http://host0.com';
    }

    private static function waitUntilProcReady(Process $proc)
    {
        while (true) {
            usleep(50000);
            foreach ($proc as $type => $data) {
                if ($proc::OUT === $type || $proc::ERR === $type) {
                    break 2;
                }
            }
        }
    }
}
