<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Events;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OnRequestExecuted
{
    /**
     * 服务名称.
     * @var string
     */
    public $serverName;

    /**
     * 请求路径.
     * @var string
     */
    public $path;

    /**
     * 请求方式.
     * @var string
     */
    public $method;

    /**
     * 请求头.
     * @var array
     */
    public $requestHeaders;

    /**
     * query请求体.
     * @var string
     */
    public $queryString;

    /**
     * 请求体.
     * @var string
     */
    public $requestBody;

    /**
     * 回复体.
     * @var
     */
    public $responseBody;

    /**
     * 请求耗时秒.
     *
     * @var float
     */
    public $time;

    /**
     * OnRequestExecuted constructor.
     * @param $requestBody
     * @param $responseBody
     * @param $time
     * @param mixed $serverName
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response, $time, $serverName = 'http_server')
    {
        $this->path = $request->getUri()->getPath();
        $this->method = $request->getMethod();
        $this->requestHeaders = $request->getHeaders();
        $this->queryString = $request->getQueryParams();
        $this->requestBody = $request->getParsedBody();
        $this->responseBody = json_decode($response->getBody()->getContents(), true) ?: $this->responseBody;
        $this->time = $time;
        $this->serverName = $serverName;
    }

    public function toArray($items = [])
    {
        return array_merge([
            'time' => $this->time,
            'path' => $this->path,
            'method' => $this->method,
            'queryParams' => $this->queryString,
            'requestBody' => $this->requestBody,
            'responseBody' => $this->responseBody,
        ], $items);
    }
}
