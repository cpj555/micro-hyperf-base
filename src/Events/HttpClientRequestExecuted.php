<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Events;

class HttpClientRequestExecuted
{
    /**
     * @var string
     */
    public $client;

    /**
     * 请求路径.
     * @var string
     */
    public $url;

    /**
     * 请求方式.
     * @var string
     */
    public $method;

    /**
     * 请求参数.
     * @var array
     */
    public $options;

    /**
     * 回复体.
     * @var
     */
    public $responseBody;

    /**
     * @var string
     */
    public $originErrMessage;

    /**
     * 请求耗时秒.
     *
     * @var float
     */
    public $time;

    /**
     * OnRequestExecuted constructor.
     * @param $logger
     * @param $url
     * @param $method
     * @param $config
     * @param $responseBody
     * @param $time
     * @param string $client
     * @param mixed $options
     * @param mixed $originErrMessage
     */
    public function __construct($url, $method, $options, $responseBody, $originErrMessage, $time, $client = 'http-client')
    {
        $this->url = $url;
        $this->method = $method;
        $this->options = $options;
        $this->responseBody = $responseBody;
        $this->originErrMessage = $originErrMessage;
        $this->time = $time;
        $this->client = $client;
    }

    public function toArray()
    {
        return [
            'time' => $this->time,
            'url' => $this->url,
            'method' => $this->method,
            'config' => $this->options,
            'responseBody' => $this->responseBody,
            'originErrMessage' => $this->originErrMessage,
        ];
    }
}
