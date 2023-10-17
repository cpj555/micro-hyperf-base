<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Provider;

use Hyperf\Codec\Json;
use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Constants\HeaderKeys;
use Losingbattle\MicroBase\Constants\HttpStatusCode;
use Losingbattle\MicroBase\Contract\TraceIdGeneratorInterface;
use Losingbattle\MicroBase\Events\HttpClientRequestExecuted;
use Losingbattle\MicroBase\Exception\ProviderException;
use GuzzleHttp\Client;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Context\ApplicationContext;
use Psr\EventDispatcher\EventDispatcherInterface;

abstract class BaseProvider
{
    /**
     * @var Client
     */
    protected $client;

    protected $throw = true;

    /**
     * @var EventDispatcherInterface|mixed
     */
    protected $eventDispatcher;

    protected TraceIdGeneratorInterface $traceIdGenerator;

    //目标服务器不可用标识
    protected $errMatchStr = [
        'Request timed out' => '请求超时',
    ];

    private $options = [
        'headers' => [],
        'timeout' => 2,
    ];

    public function __construct()
    {
        $container = ApplicationContext::getContainer();
        $this->client = $container->get(ClientFactory::class)->create();
        $this->traceIdGenerator = $container->get(TraceIdGeneratorInterface::class);
        $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
    }

    public static function make()
    {
        return new static();
    }

    protected function setTimeout($timeout)
    {
        $this->options['timeout'] = $timeout;
        return $this;
    }

    protected function setHeader(array $header)
    {
        $this->options['headers'] = array_merge($this->getDefaultHeaders(), $header);
        return $this;
    }

    protected function setThrow(bool $throw)
    {
        $this->throw = $throw;
        return $this;
    }

    protected function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => $query]);
    }

    protected function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }

    protected function httpPostMultipart(string $url, array $data = []): float|object|array|bool|int|string|null
    {
        $settleData = [];
        foreach ($data as $key => $value) {
            $settleData[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }

        return $this->request($url, 'POST', ['multipart' => $settleData]);
    }

    protected function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    private function getDefaultHeaders(): array
    {
        $this->options['headers'][HeaderKeys::X_TRACE_ID] = $this->traceIdGenerator->generate();

        return $this->options['headers'];
    }

    private function request(string $url, $method, array $config)
    {
        $beg_time = microtime(true);
        $responseBody = null;
        $originErrMessage = null;
        $errMessage = null;

        $options = array_merge($this->options, $config);
        try {
            $response = $this->client->request($method, $url, $options);
            $responseBody = $response->getBody()->getContents();

            if ($response->getStatusCode() === HttpStatusCode::OK) {
                return Json::decode($responseBody, true);
            }
            throw new ProviderException(Code::ERROR_REQUEST_SERVICE);
        } catch (\Exception $ex) {
            $originErrMessage = $ex->getMessage();
            if (isset(HttpStatusCode::STATUS_MAP[$ex->getCode()])) {
                $errMessage = HttpStatusCode::STATUS_MAP[$ex->getCode()];
            } else {
                $errMessage = '网络请求错误';
            }
            if ($this->throw === false) {
                return [];
            }
            $errMatchStrKey = array_keys($this->errMatchStr);
            foreach ($errMatchStrKey as $msg) {
                if (stripos($ex->getMessage(), $msg) !== false) {
                    $errMessage = $this->errMatchStr[$msg];
                    break;
                }
            }
            throw new ProviderException($ex->getCode(), $errMessage);
        } finally {
            $this->eventDispatcher->dispatch(new HttpClientRequestExecuted(
                $url,
                $method,
                $options,
                $responseBody,
                $originErrMessage,
                round(microtime(true) - $beg_time, 3)
            ));
        }
    }
}
