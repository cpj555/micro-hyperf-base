<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Exception\Handler;

use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Contract\ResponseResultInterface;
use Losingbattle\MicroBase\Events\ExceptionExecuted;
use Losingbattle\MicroBase\Exception\AlertException;
use Losingbattle\MicroBase\Exception\BaseException;
use Losingbattle\MicroBase\ResponseResult\OrResponseResult;
use Hyperf\HttpMessage\Exception\BadRequestHttpException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class RpcExceptionHandler extends HttpExceptionHandler
{

    /** @var ContainerInterface */
    private $container;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ResponseResultInterface
     */
    private $reponseResult;

    public function __construct(ContainerInterface $container,EventDispatcherInterface $eventDispatcher)
    {
        $this->container = $container;
        $this->reponseResult = $this->container->get(OrResponseResult::class);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 0 为 success 码 未捕捉的转为500
        if ($throwable->getCode() === 0) {
            $code = Code::SERVER_ERROR;
            $throwable = new \ErrorException($throwable->getMessage(), $code);
        } else {
            $code = $throwable->getCode();
        }
        //实例化psrRequest失败时直接返回
        if ($throwable instanceof BadRequestHttpException) {
            $this->stopPropagation();
            return $this->reponseResult->returnError($code, $throwable);
        }

        switch (true) {
            case ! $throwable instanceof BaseException:
            case $throwable instanceof AlertException:
            case $throwable instanceof \ErrorException:
            /** @var RequestInterface $r */
            $r = $this->container->get(RequestInterface::class);
            $this->eventDispatcher->dispatch(new ExceptionExecuted($throwable, $r));
            break;
        }
        $this->stopPropagation();
        return $this->reponseResult->returnError($code, $throwable);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
