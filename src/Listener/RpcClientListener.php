<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Listener;

use Losingbattle\MicroBase\Events\RpcClientRequestExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class RpcClientListener implements ListenerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('rpc_client', 'rpc_client');
    }

    public function listen(): array
    {
        // TODO: Implement listen() method.

        return [
            RpcClientRequestExecuted::class,
        ];
    }

    public function process(object $event): void
    {
        // TODO: Implement process() method.
        if ($event instanceof RpcClientRequestExecuted) {
            $this->logger->info('rpc_client', $event->toArray());
        }
    }
}
