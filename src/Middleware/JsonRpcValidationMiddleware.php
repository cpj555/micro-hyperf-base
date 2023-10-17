<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Middleware;

use Closure;
use Losingbattle\MicroBase\Exception\BusinessException;
use Losingbattle\MicroBase\Request\FormRequest;
use FastRoute\Dispatcher;
use Hyperf\Di\ReflectionManager;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Server\Exception\ServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonRpcValidationMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @throws BusinessException
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);

        if (! $dispatched instanceof Dispatched) {
            throw new ServerException(sprintf('The dispatched object is not a %s object.', Dispatched::class));
        }

        if ($this->shouldHandle($dispatched)) {
            [$requestHandler, $method] = $this->prepareHandler($dispatched->handler->callback);
            $reflectionMethod = ReflectionManager::reflectMethod($requestHandler, $method);
            $parameters = $reflectionMethod->getParameters();
            foreach ($parameters as $key => $parameter) {
                if ($parameter->getType() === null) {
                    continue;
                }

                $classname = $parameter->getType()->getName();

                if ($this->isSubOfFormRequest($classname)) {
                    /** @var FormRequest $formRequest */
                    $formRequest = $this->container->get($classname);
                    $formRequest->load(current($request->getParsedBody()))->setScene($method)->check();
                }
            }
        }

        return $handler->handle($request);
    }

    public function isSubOfFormRequest(string $classname): bool
    {
        if (class_exists($classname)) {
            return is_subclass_of($classname, FormRequest::class);
        }
        return false;
    }

    protected function shouldHandle(Dispatched $dispatched): bool
    {
        return $dispatched->status === Dispatcher::FOUND && ! $dispatched->handler->callback instanceof Closure;
    }

    /**
     * @param array|string $handler
     * @see \Hyperf\HttpServer\CoreMiddleware::prepareHandler()
     */
    protected function prepareHandler($handler): array
    {
        if (\is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                return explode('@', $handler);
            }
            return explode('::', $handler);
        }
        if (\is_array($handler) && isset($handler[0], $handler[1])) {
            return $handler;
        }
        throw new \RuntimeException('Handler not exist.');
    }
}
