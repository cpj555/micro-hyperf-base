<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Listener;

use Hyperf\Database\Events\TransactionBeginning;
use Hyperf\Database\Events\TransactionCommitted;
use Hyperf\Database\Events\TransactionRolledBack;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

class TransactionListener implements ListenerInterface
{

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('sql');
    }

    public function listen(): array
    {
        // TODO: Implement listen() method.
        return [
            TransactionBeginning::class,
            TransactionCommitted::class,
            TransactionRolledBack::class,
        ];
    }

    public function process(object $event): void
    {
        switch (true) {
            case $event instanceof TransactionBeginning:
                $this->logger->info('[transaction],beginning');
                break;
            case $event instanceof TransactionCommitted:
                $this->logger->info('[transaction],committed');
                break;
            case $event instanceof TransactionRolledBack:
                $this->logger->info('[transaction],rolledBack');
                break;
        }
    }
}
