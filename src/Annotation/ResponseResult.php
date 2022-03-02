<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Annotation;

use Losingbattle\MicroBase\Constants\ResponseConstants;
use Doctrine\Common\Annotations\Annotation\Target;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationInterface;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class ResponseResult extends AbstractAnnotation implements AnnotationInterface
{
    public $returnEmptyType = ResponseConstants::TYPE_OBJECT;

    public $message;
}
