<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\ResponseResult;

use Losingbattle\MicroBase\Constants\ResponseStruct;
use Losingbattle\MicroBase\Contract\ResponseResultInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\ReflectionManager;
use Psr\Container\ContainerInterface;

class ResponseResultFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);

        $invoke = $config->get('response.invoke');

        if ($invoke && $reflectionClass = ReflectionManager::reflectClass($invoke)) {
            if (isset($reflectionClass->getInterfaces()[ResponseResultInterface::class])) {
                return new $invoke($container);
            }
        }

        $struct = $config->get('response.struct', ResponseStruct::CDM);

        switch ($struct) {
            case ResponseStruct::CDM:
                return new CdmResponseResult();
        }
    }
}
