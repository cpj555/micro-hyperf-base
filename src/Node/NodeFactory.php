<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Node;

use Psr\Container\ContainerInterface;

class NodeFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new NodeGenerator($container);
    }
}
