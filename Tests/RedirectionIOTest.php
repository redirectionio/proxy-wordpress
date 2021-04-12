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
    return RedirectionIOTest::$options;
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
    RedirectionIOTest::$response = [
        'location' => $location,
        'statusCode' => $statusCode,
    ];
}

function status_header($statusCode)
{
    RedirectionIOTest::$isGone = true;
    RedirectionIOTest::$response = [
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
    public static $options = [
        'projectKey' => 'szio2389-bfdz-51e8-8468-02dcop129501:ep6a4805-eo6z-dzo6-aeb0-8c1lbmo40242',
        'connections' => [
            [
                'name' => 'agent',
                'remote_socket' => 'tcp://localhost:3100',
            ],
        ],
        'doNotRedirectAdmin' => false,
    ];

    public static $response = [];
    public static $isRedirect = null;
    public static $isGone = null;

    private $rio;

    public static function setUpBeforeClass()
    {
        static::startAgent();
    }

    protected function setUp()
    {
        // clear
        self::$response = [];
        unset(
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['HTTP_REFERER']
        );

        $this->rio = $this->getMockBuilder(RedirectionIO::class)
            ->setMethods(['exitCode'])
            ->getMock();
    }

    public function testWhenRedirectRuleExists()
    {
        $this->initializeServerVars(['path' => '/foo']);

        $this->rio->findRedirect();

        $this->assertTrue(self::$isRedirect);
        $this->assertSame('/bar', self::$response['location']);
        $this->assertSame(301, self::$response['statusCode']);
    }

    public function testWhen410RuleExists()
    {
        $this->initializeServerVars(['path' => '/garply']);

        $this->rio->findRedirect();

        $this->assertTrue(self::$isGone);
        $this->assertSame(410, self::$response['statusCode']);
    }

    public function testWhenRedirectRuleNotExists()
    {
        $this->initializeServerVars(['path' => '/hello']);

        $this->assertFalse($this->rio->findRedirect());
    }

    public function testWhenAgentDown()
    {
        self::$options = [
            'projectKey' => self::$options['projectKey'],
            'connections' => [
                [
                    'name' => 'agent',
                    'remote_socket' => 'tcp://unknown-host:80',
                ],
            ],
            'doNotRedirectAdmin' => false,
        ];

        $this->initializeServerVars(['path' => '/foo']);

        $this->assertFalse($this->rio->findRedirect());
    }

    private static function startAgent($port = 3100)
    {
        $finder = new PhpExecutableFinder();
        if (false === $binary = $finder->find()) {
            throw new \RuntimeException('Unable to find PHP binary to run a fake agent.');
        }

        // Always prefer vendor
        $fakeAgent = './vendor/redirectionio/proxy-sdk/src/Resources/fake_agent.php';

        if (!file_exists($fakeAgent)) {
            $fakeAgent = __DIR__ . '/../../sdk/src/Resources/fake_agent.php';
        }

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
