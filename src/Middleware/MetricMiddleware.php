<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Middleware;

use Losingbattle\MicroBase\Exception\AlertException;
use Losingbattle\MicroBase\Exception\BaseException;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Metric\Timer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MetricMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $labels = [
            'request_status' => '500', //default to 500 in case uncaught exception occur
            'request_path' => $this->getPath($request),
            'request_method' => $request->getMethod(),
        ];
        $timer = new Timer('http_requests', $labels);
        try {
            $response = $handler->handle($request);
        } catch (\Throwable $throwable) {
            if (! $throwable instanceof BaseException || $throwable instanceof AlertException) {
                $labels['request_status'] = '500';
            } else {
                $labels['request_status'] = '200';
            }
            $timer->end($labels);
            throw $throwable;
        }

        $labels['request_status'] = (string) $response->getStatusCode();
        $timer->end($labels);
        return $response;
    }

    protected function getPath(ServerRequestInterface $request): string
    {
        $dispatched = $request->getAttribute(Dispatched::class);
        if (! $dispatched) {
            return $request->getUri()->getPath();
        }
        if (! $dispatched->handler) {
            return $request->getUri()->getPath();
        }
        return $dispatched->handler->route;
    }
}
