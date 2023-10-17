<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Aspect;

use Losingbattle\MicroBase\Annotation\RequestLock;
use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Exception\BusinessException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;

class RequestLockAspect extends AbstractAspect
{
    public array $annotations = [
        RequestLock::class,
    ];

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    private $config;

    private Redis $redis;

    public function __construct(ConfigInterface $config, RequestInterface $request, Redis $redis)
    {
        $this->request = $request;
        $this->config = $this->parseConfig($config);
        $this->redis = $redis;
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var RequestLock $requestLockAnnotation */
        $requestLockAnnotation = $proceedingJoinPoint->getAnnotationMetadata()->method[RequestLock::class];

        $lockArgs = $requestLockAnnotation->requestArgs ? $this->request->inputs($requestLockAnnotation->requestArgs, $this->request->all()) : $this->request->all();

        $arg = [$proceedingJoinPoint->getReflectMethod()->class, $proceedingJoinPoint->getReflectMethod()->name, md5(json_encode($lockArgs))];

        $lockKeyName = sprintf('%s:%s', $this->config['keyPrefix'], implode('-', $arg));

        if (! is_numeric($requestLockAnnotation->ttl)) {
            $bool = $this->redis->set($lockKeyName, 1, ['NX']);
        } else {
            $bool = $this->redis->set($lockKeyName, 1, ['NX', 'EX' => $requestLockAnnotation->ttl]);
        }

        if (! $bool) {
            throw new BusinessException(Code::ERROR_BUSY_BUSINESS, $requestLockAnnotation->message ?? '');
        }

        try {
            $p = $proceedingJoinPoint->process();
        } catch (\Throwable $exception) {
            throw $exception;
        } finally {
            if ($requestLockAnnotation->endRelease === true) {
                $this->redis->del($lockKeyName);
            }
        }

        return $p;
    }

    protected function parseConfig(ConfigInterface $config)
    {
        if ($config->has('request_lock')) {
            return $config->get('request_lock');
        }

        return [
            'keyPrefix' => env('APP_NAME') . ':key_operation_lock',
        ];
    }
}
