<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Events;

class OnReceiveExecuted
{
    /**
     * 请求路径.
     * @var string
     */
    public $path;

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
     * @param $path
     * @param $requestId
     * @param $requestBody
     * @param $responseBody
     * @param $time
     */
    public function __construct($path, $requestBody, $responseBody, $time)
    {
        $this->path = $path;
        $this->requestBody = $requestBody;
        $this->responseBody = $responseBody;
        $this->time = $time;
    }

    public function toArray()
    {
        return [
            'time' => $this->time,
            'url' => $this->path,
            'requestBody' => $this->requestBody,
            'responseBody' => $this->responseBody,
        ];
    }
}
