<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\TraceId;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\UuidFactory;

class TraceIdGeneratorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ConfigInterface $config */
        $config = $container->get(ConfigInterface::class);

        $driver = $config->get('trace_id.driver', UuidGenerator::class);

        switch ($driver) {
            case SnowflakeIdGenerator::class:
                return new SnowflakeIdGenerator($container);
            case UuidGenerator::class:
            default:
                return new UuidGenerator($container, new UuidFactory());
        }
    }
}
