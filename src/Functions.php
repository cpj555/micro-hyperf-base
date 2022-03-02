<?php

declare(strict_types=1);
if (! function_exists('redis')) {
    /**
     * @param string $poolName
     * @return Redis
     */
    function redis()
    {
        $container = \Hyperf\Utils\ApplicationContext::getContainer();
        return $container->get(\Hyperf\Redis\Redis::class);
    }
}

if (! function_exists('isSelfProcess')) {
    /**
     * 本地环境是否启动process amqp crontab 等.
     */
    function isSelfProcess(): bool
    {
        if (env('SELF_PROCESS') === null) {
            return true;
        }
        return env('SELF_PROCESS');
    }
}

if (! function_exists('appZone')) {
    function appZone(): string
    {
        $contextRpcZone = \Hyperf\Context\Context::get(\Losingbattle\MicroBase\Constants\App::ZONE);
        if ($contextRpcZone) {
            return $contextRpcZone;
        }

        return env(\Losingbattle\MicroBase\Constants\App::ZONE, 'default');
    }
}

if (! function_exists('setContextAppZone')) {
    function setContextAppZone(string $appZone): void
    {
        \Hyperf\Context\Context::set(\Losingbattle\MicroBase\Constants\App::ZONE, $appZone);
    }
}

if (! function_exists('appName')) {
    function appName(): string
    {
        return env(\Losingbattle\MicroBase\Constants\App::NAME, 'undefined');
    }
}

if (! function_exists('memory_usage')) {
    function memory_usage(): string
    {
        return round(memory_get_usage() / 1024 / 1024, 2) . 'MB';
    }
}

if (! function_exists('logname')) {
    function logname(string $logname): string
    {
        return str_replace('%app_name%', appName() . '-', $logname);
    }
}

if (! function_exists('producer')) {
    function producer(): Losingbattle\RocketMqHttp\Producer
    {
        return \Hyperf\Utils\ApplicationContext::getContainer()->get(\Losingbattle\RocketMqHttp\Producer::class);
    }
}

/*
 * 获取毫秒的时间戳
 */
if (! function_exists('getTimeStampMillisecond')) {
    function getTimeStampMillisecond(): float
    {
        [$s1, $s2] = explode(' ', microtime());
        return (float) sprintf('%.0f', ((float) $s1 + (float) $s2) * 1000);
    }
}

if (! function_exists('getMacAddress')) {
    function getMacAddress(): ?string
    {
        $macAddresses = swoole_get_local_mac();

        foreach (\Hyperf\Utils\Arr::wrap($macAddresses) as $name => $address) {
            if ($address && $address !== '00:00:00:00:00:00') {
                return $name . ':' . str_replace(':', '', $address);
            }
        }

        return null;
    }
}

if (! function_exists('get_class_name')) {
    function get_class_name($classname)
    {
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }
        return $pos;
    }
}
