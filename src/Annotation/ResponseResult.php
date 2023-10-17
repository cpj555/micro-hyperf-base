<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Annotation;

use Attribute;
use Losingbattle\MicroBase\Constants\ResponseConstants;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class ResponseResult extends AbstractAnnotation
{
    public string $returnEmptyType = ResponseConstants::TYPE_OBJECT;

    public ?string $message;
}
