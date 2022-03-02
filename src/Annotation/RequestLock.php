<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationInterface;
use Hyperf\Utils\Str;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class RequestLock extends AbstractAnnotation implements AnnotationInterface
{
    public $ttl;

    public $endRelease;

    public $requestArgs;

    public $message;

    public function __construct($value = null)
    {
        parent::__construct($value);

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
