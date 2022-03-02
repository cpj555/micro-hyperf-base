<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Events;

use Losingbattle\Rpc\Message\Request;
use Losingbattle\Rpc\Message\Response;

class RpcClientRequestExecuted
{
    /**
     * 请求方法.
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * 请求耗时秒.
     *
     * @var float
     */
    public $time;

    /**
     * RpcClientRequestExecuted constructor.
     * @param $method
     * @param $traceId
     * @param $depth
     * @param $param
     * @param $responseBody
     * @param $time
     * @param mixed $response
     * @param Request $request
     */
    public function __construct(Request $request, ?Response $response, $time)
    {
        $this->request = $request instanceof Request ? clone $request : new Request();
        $this->response = $response instanceof Response ? clone $response : new Response();
        $this->time = $time;
    }

    public function toArray()
    {
        return [
            'time' => $this->time,
            'path' => $this->request->getUri()->getPath(),
            'method' => $this->request->getMethod(),
            'headers' => $this->request->getHeaders(),
            'param' => $this->request->getParsedBody(),
            'responseBody' => $this->response->getParsedBody(),
        ];
    }
}
