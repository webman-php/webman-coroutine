<?php

declare(strict_types=1);

namespace Workbunny\Tests;

use PHPUnit\Framework\TestCase;
use Workbunny\Tests\mock\TestHandler;

use function Workbunny\WebmanCoroutine\event_loop;

use Workbunny\WebmanCoroutine\Factory;

use function Workbunny\WebmanCoroutine\is_coroutine_env;
use function Workbunny\WebmanCoroutine\package_installed;

/**
 * @runTestsInSeparateProcesses
 */
class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../src/helpers.php';
    }

    public function testEventLoopWithExpectedClass()
    {
        Factory::register(__METHOD__, TestHandler::class);
        $expectedClass = __METHOD__;
        $result = event_loop($expectedClass);
        $this->assertEquals($expectedClass, $result);

        Factory::unregister(__METHOD__);
    }

    public function testEventLoopWithDefaultClass()
    {
        // env auto return
        $result = event_loop();
        $this->assertEquals(Factory::WORKERMAN_DEFAULT, $result);
        // not found class
        $result = event_loop(__METHOD__);
        $this->assertEquals(Factory::WORKERMAN_DEFAULT, $result);
    }

    public function testPackageInstalled()
    {
        $packageName = 'swow/swow';
        $this->assertTrue(package_installed($packageName));
        $packageName = 'nonexistent/package';
        $this->assertFalse(package_installed($packageName));
    }

    public function testIsCoroutineEnv()
    {
        $this->assertTrue(is_coroutine_env());
    }
}
