<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Exception;

use Losingbattle\MicroBase\Constants\Code;
use Exception;
use Throwable;

class BaseException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $responseMessageCodeConstants = config('response.code_constants_class', Code::class);
            $message = $responseMessageCodeConstants::getMessage($code);
        }

        parent::__construct($message, $code, $previous);
    }
}
