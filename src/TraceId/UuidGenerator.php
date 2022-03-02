<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\TraceId;

use Losingbattle\MicroBase\Contract\TraceIdGeneratorInterface;
use Hyperf\Context\Context;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\UuidFactory;

class UuidGenerator implements TraceIdGeneratorInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var UuidFactory
     */
    private $uuidFactory;

    public function __construct($container, UuidFactory $uuidFactory)
    {
        $this->container = $container;
        $this->uuidFactory = $uuidFactory;
    }

    public function generate()
    {
        return Context::getOrSet(Constants::CONTEXT_TRACE_ID_KEY, $this->uuidFactory->uuid4()->getHex()->toString());
    }
}
