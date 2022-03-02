<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Rewrite;

use Losingbattle\MicroBase\Constants\HeaderKeys;
use Losingbattle\MicroBase\Contract\TraceIdGeneratorInterface;
use Losingbattle\MicroBase\Events\OnRequestExecuted;
use Hyperf\Context\Context;
use Hyperf\Dispatcher\HttpDispatcher;
use Hyperf\ExceptionHandler\ExceptionHandlerDispatcher;
use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Hyperf\HttpMessage\Server\Response as Psr7Response;
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\ResponseEmitter;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Server;
use Hyperf\Utils\Coordinator\Constants;
use Hyperf\Utils\Coordinator\CoordinatorManager;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

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

    public function onRequest($request, $response): void
    {
        $beg_time = microtime(true);
        try {
            CoordinatorManager::until(Constants::WORKER_START)->yield();

            [$psr7Request, $psr7Response] = $this->initRequestAndResponse($request, $response);

            $this->traceIdGenerator && $this->traceIdGenerator->generate();

            $psr7Request = $this->coreMiddleware->dispatch($psr7Request);
            /** @var Dispatched $dispatched */
            $dispatched = $psr7Request->getAttribute(Dispatched::class);
            $middlewares = $this->middlewares;
            if ($dispatched->isFound()) {
                $registedMiddlewares = MiddlewareManager::get($this->serverName, $dispatched->handler->route, $psr7Request->getMethod());
                $middlewares = array_merge($middlewares, $registedMiddlewares);
            }

            $psr7Response = $this->dispatcher->dispatch($psr7Request, $middlewares, $this->coreMiddleware);
        } catch (Throwable $throwable) {
            // Delegate the exception to exception handler.
            $psr7Response = $this->exceptionHandlerDispatcher->dispatch($throwable, $this->exceptionHandlers);
        } finally {
            // Send the Response to client.
            if (! isset($psr7Response)) {
                return;
            }

            if (isset($psr7Request, $psr7Response)) {
                $this->eventDispatcher->dispatch(
                    new OnRequestExecuted(
                        $psr7Request,
                        $psr7Response,
                        round(microtime(true) - $beg_time, 3),
                        $this->getServerName()
                    )
                );
            }

            if (isset($psr7Request) && $psr7Request->getMethod() === 'HEAD') {
                $this->responseEmitter->emit($psr7Response, $response, false);
            } else {
                $this->responseEmitter->emit($psr7Response, $response, true);
            }
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
