<?php
/**
 * @author workbunny/Chaz6chez
 * @email chaz6chez1993@outlook.com
 */
declare(strict_types=1);

namespace Workbunny\WebmanCoroutine\Utils\Worker;

use Workbunny\WebmanCoroutine\Exceptions\WorkerException;
use Workbunny\WebmanCoroutine\Factory;
use Workbunny\WebmanCoroutine\Handlers\HandlerInterface;
use Workerman\Worker;

abstract class AbstractWorker extends Worker
{

    /** @inheritDoc */
    protected static function initWorkers()
    {
        foreach (static::$_workers as $worker) {
            // 加载环境
            /** @var HandlerInterface $handler */
            $handler = Factory::getCurrentHandler();
            if (!$handler) {
                $className = $worker::class;
                throw new WorkerException("Please run Factory::init or set $className::\$EventLoopClass = event_loop(). ");
            }
            $handler::initEnv();
            // 加载__init__开头的初始化方法
            $traits = class_uses($worker, false);
            foreach ($traits as $trait) {
                $methods = (new \ReflectionClass($trait))->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    if (str_starts_with($methodName, '__init__') and method_exists($worker, $methodName)) {
                        $worker->$methodName();
                    }
                }
            }
        }
        // 运行
        parent::initWorkers();
    }
}