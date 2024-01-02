<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Rewrite;

use Losingbattle\MicroBase\Constants\HeaderKeys;
use Losingbattle\MicroBase\Contract\TraceIdGeneratorInterface;
use Hyperf\Context\Context;
use Hyperf\Dispatcher\HttpDispatcher;
use Hyperf\ExceptionHandler\ExceptionHandlerDispatcher;
use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Hyperf\HttpMessage\Server\Response as Psr7Response;
use Hyperf\HttpServer\ResponseEmitter;
use Hyperf\HttpServer\Server;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

class HttpServer extends Server
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var TraceIdGeneratorInterface
     */
    private $traceIdGenerator;

    public function __construct(ContainerInterface $container, HttpDispatcher $dispatcher, ExceptionHandlerDispatcher $exceptionHandlerDispatcher, ResponseEmitter $responseEmitter)
    {
        parent::__construct($container, $dispatcher, $exceptionHandlerDispatcher, $responseEmitter);
        $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
        if ($container->has(TraceIdGeneratorInterface::class)) {
            $this->traceIdGenerator = $container->get(TraceIdGeneratorInterface::class);
        }
    }

    protected function initRequestAndResponse($request, $response): array
    {
        Context::set(ResponseInterface::class, $psr7Response = new Psr7Response());

        if ($request instanceof ServerRequestInterface) {
            $psr7Request = $request;
        } else {
            $psr7Request = Psr7Request::loadFromSwooleRequest($request);
        }

        Context::set(ServerRequestInterface::class, $psr7Request);

        if ($traceId = $psr7Request->getHeaderLine(HeaderKeys::X_TRACE_ID)) {
            Context::set(\Losingbattle\MicroBase\TraceId\Constants::CONTEXT_TRACE_ID_KEY, Uuid::fromString($traceId)->getHex()->toString());
        }

        return [$psr7Request, $psr7Response];
    }
}
