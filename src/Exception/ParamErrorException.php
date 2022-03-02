<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Exception;

use Losingbattle\MicroBase\Constants\Code;
use Throwable;

class ParamErrorException extends BusinessException
{
    public function __construct(string $message = '', int $code = Code::ERROR_PARAMS, Throwable $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
