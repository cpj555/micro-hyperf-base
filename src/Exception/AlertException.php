<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Exception;

use Losingbattle\MicroBase\Constants\Code;
use Throwable;

class AlertException extends BaseException
{
    public function __construct(string $message = '', int $code = Code::ALERT_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
