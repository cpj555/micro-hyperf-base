<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Listener;

use Losingbattle\MicroBase\Events\HttpClientRequestExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class HttpClientOnRequestListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerFactory::class)->get('http_client', 'http_client');
    }

    public function listen(): array
    {
        // TODO: Implement listen() method.
        return [
            HttpClientRequestExecuted::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof HttpClientRequestExecuted) {
            $this->logger->info($event->client, $event->toArray());
        }
    }
}
