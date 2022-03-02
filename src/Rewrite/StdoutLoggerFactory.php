<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Rewrite;

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

class StdoutLoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return $container->get(LoggerFactory::class)->get('system', 'system');
    }
}
