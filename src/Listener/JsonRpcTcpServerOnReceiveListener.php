<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Listener;

use Losingbattle\MicroBase\Events\OnReceiveExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Rpc\Context;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class JsonRpcTcpServerOnReceiveListener implements ListenerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerFactory::class)->get('rpc_server', 'rpc_server');
    }

    public function listen(): array
    {
        // TODO: Implement listen() method.
        return [
            OnReceiveExecuted::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof OnReceiveExecuted) {
            $rpcContext = $this->container->get(Context::class);
            $depth = $rpcContext->get('depth');
            if ($depth) {
                $rpcContext->set('depth', ++$depth);
            }

            $this->logger->info('rpc_server', $event->toArray());
        }
    }
}
