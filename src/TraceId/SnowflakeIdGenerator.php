<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\TraceId;

use Losingbattle\MicroBase\Contract\TraceIdGeneratorInterface;
use Hyperf\Context\Context;
use Hyperf\Snowflake\IdGeneratorInterface;
use Psr\Container\ContainerInterface;

class SnowflakeIdGenerator implements TraceIdGeneratorInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function generate()
    {
        /** @var IdGeneratorInterface $snowflakeIdGenetator */
        $snowflakeIdGenetator = $this->container->get(IdGeneratorInterface::class);
        return Context::getOrSet(Constants::CONTEXT_TRACE_ID_KEY, $snowflakeIdGenetator->generate());
    }
}
