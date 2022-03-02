<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Listener;

use Losingbattle\MicroBase\Events\ExceptionExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ExceptionListener implements ListenerInterface
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
        $this->logger = $container->get(LoggerFactory::class)->get('exception', 'exception');
    }

    public function listen(): array
    {
        // TODO: Implement listen() method.
        return [
            ExceptionExecuted::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof ExceptionExecuted) {
            $throwable = $event->throwable;
            $request = $event->request;

            $this->logger->error('exception', [
                'uri' => $request->getUri()->getPath(),
                'content' => $request->getParsedBody(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'message' => $throwable->getMessage(),
            ]);
        }
    }
}
