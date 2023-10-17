<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Rewrite;

use Hyperf\Coroutine\Coroutine;
use Hyperf\Coroutine\WaitGroup;
use Swoole\Coroutine\Channel;

class Parallel
{
    /**
     * @var callable[]
     */
    private $callbacks = [];

    /**
     * @var null|Channel
     */
    private $concurrentChannel;

    /**
     * @param int $concurrent if $concurrent is equal to 0, that means unlimit
     */
    public function __construct(int $concurrent = 0)
    {
        if ($concurrent > 0) {
            $this->concurrentChannel = new Channel($concurrent);
        }
    }

    public function add(callable $callable, $key = null): void
    {
        if ($key === null) {
            $this->callbacks[] = $callable;
        } else {
            $this->callbacks[$key] = $callable;
        }
    }

    //主要重写该方法 任意一个有异常直接抛出该异常
    public function wait(bool $throw = true): array
    {
        $result = $throwables = [];
        $wg = new WaitGroup();
        $wg->add(\count($this->callbacks));
        foreach ($this->callbacks as $key => $callback) {
            $this->concurrentChannel && $this->concurrentChannel->push(true);
            Coroutine::create(function () use ($callback, $key, $wg, &$result, &$throwables): void {
                try {
                    $result[$key] = call($callback);
                } catch (\Throwable $throwable) {
                    $throwables[$key] = $throwable;
                } finally {
                    $this->concurrentChannel && $this->concurrentChannel->pop();
                    $wg->done();
                }
            });
        }
        $wg->wait();
        if ($throw && (\count($throwables)) > 0) {
            throw current($throwables);
        }
        return $result;
    }

    public function clear(): void
    {
        $this->callbacks = [];
    }
}
