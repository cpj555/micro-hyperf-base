<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Listener;

use Hyperf\Collection\Arr;
use Hyperf\HttpServer\Event\RequestTerminated;
use Losingbattle\MicroBase\Constants\HeaderKeys;
use Losingbattle\MicroBase\Utils\Str;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
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
            RequestTerminated::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof RequestTerminated) {
            if ($event->request->getMethod() === 'OPTIONS') {
                return;
            }
            //包含关键字不记录请求体
            if (Str::existsInUri($event->request->getUri()->getPath(), ['liveness', 'favicon', 'heartbeat'])) {
                return;
            }

            $recordHeader = [];
            $headerKeys = \Hyperf\Config\config('request.listener.record_header_keys', []);
            $headerKeys = array_merge($headerKeys, [
                HeaderKeys::X_FORWARDED_FOR, HeaderKeys::X_REAL_IP,
            ]);

            foreach ($headerKeys as $headerKey) {
                $v = Arr::get($event->request->getHeaders(), strtolower($headerKey), []);

                $value = \is_string($v) ? $v : Arr::first($v);
                if ($value === null) {
                    continue;
                }
                $recordHeader[$headerKey] = $value;
            }

            $this->logger->info($event->server, [
                'record_header' => $recordHeader,
//                'time' => $this->,
                'path' => $event->request->getUri()->getPath(),
                'method' => $event->request->getMethod(),
                'queryParams' => $event->request->getQueryParams(),
                'requestBody' => $event->request->getParsedBody(),
                'responseBody' =>json_decode($event->response->getBody()->getContents(), true) ?: $event->response->getBody()->getContents(),
            ]);
        }
    }
}
