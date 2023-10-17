<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Node;

use Losingbattle\MicroBase\Contract\NodeInterface;
use Psr\Container\ContainerInterface;

class NodeGenerator implements NodeInterface
{
    protected string $nodeName;

    public function __construct(ContainerInterface $container)
    {
        $this->nodeName = (string)env('POD_NAME', bin2hex(random_bytes(8)));
    }

    public function getNodeName(): string
    {
        return $this->nodeName;
    }
}
