<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Exception;

use Throwable;

class SystemException extends BaseException
{
    public function __construct(int $code, string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
