<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationInterface;
use Hyperf\Stringable\Str;

#[Attribute( Attribute::TARGET_METHOD)]
class RequestLock extends AbstractAnnotation implements AnnotationInterface
{
    public int $ttl;

    public bool $endRelease;

    public false|array $requestArgs;

    public string $message;

    public function __construct($value = null)
    {

        if (isset($value['requestArgs'])) {
            $requestArgs = [];
            if (\is_string($value['requestArgs'])) {
                // Explode a string to a array
                $requestArgs = explode(',', Str::lower(str_replace(' ', '', $value['requestArgs'])));
            } else {
                foreach ($value['requestArgs'] as $requestArg) {
                    $requestArgs[] = Str::lower(str_replace(' ', '', $requestArg));
                }
            }
            $this->requestArgs = $requestArgs;
        }
    }
}
