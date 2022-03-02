<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Listener;

use Losingbattle\MicroBase\Constants\HeaderKeys;
use Losingbattle\MicroBase\Events\OnRequestExecuted;
use Losingbattle\MicroBase\Utils\Str;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Arr;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class HttpServerOnRequestListener implements ListenerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('request');
    }

    public function listen(): array
    {
        return [
            OnRequestExecuted::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof OnRequestExecuted) {
            if ($event->method === 'OPTIONS') {
                return;
            }
            //包含关键字不记录请求体
            if (Str::existsInUri($event->path, ['liveness', 'favicon', 'heartbeat'])) {
                return;
            }

            $recordHeader = [];
            $headerKeys = config('request.listener.record_header_keys', []);
            $headerKeys = array_merge($headerKeys, [
                HeaderKeys::X_FORWARDED_FOR, HeaderKeys::X_REAL_IP,
            ]);

//            if ($event->serverName == 'cloudr-rpc') {
//                $headerKeys = array_merge($headerKeys, [SideCarHeaderKeys::RPC_CONTEXT,
//                    SideCarHeaderKeys::RPC_STREAM_LENGTH,
//                    SideCarHeaderKeys::RPC_STREAM_OFFSET,
//                    SideCarHeaderKeys::RPC_STREAM_PROTO_VERSION,
//                    SideCarHeaderKeys::RPC_OK, ]);
//            }

            foreach ($headerKeys as $headerKey) {
                $v = Arr::get($event->requestHeaders, strtolower($headerKey), []);

                $value = \is_string($v) ? $v : Arr::first($v);
                if ($value === null) {
                    continue;
                }
                $recordHeader[$headerKey] = $value;
            }

            $this->logger->info($event->serverName, $event->toArray([
                'record_header' => $recordHeader,
            ]));
        }
    }
}
