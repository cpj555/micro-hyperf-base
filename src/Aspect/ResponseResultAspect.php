<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Aspect;

use Losingbattle\MicroBase\Annotation\ResponseResult;
use Losingbattle\MicroBase\Constants\ResponseConstants;
use Losingbattle\MicroBase\Contract\ResponseResultInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

/**
 * @Aspect
 */
class ResponseResultAspect extends AbstractAspect
{
    public array $annotations = [
        ResponseResult::class,
    ];

    private ResponseResultInterface $responseResult;

    public function __construct(ResponseResultInterface $responseResult)
    {
        $this->responseResult = $responseResult;
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $res = $proceedingJoinPoint->process();
        /** @var ResponseResult $responseResult */
        $message = '';

        $returnEmptyType = ResponseConstants::TYPE_OBJECT;
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[ResponseResult::class])) {
            $responseResult = $proceedingJoinPoint->getAnnotationMetadata()->method[ResponseResult::class];
            $message = $responseResult->message ?? '';
            $returnEmptyType = $responseResult->returnEmptyType;
        }

        return $this->responseResult->return($res, $message, $returnEmptyType);
    }
}
