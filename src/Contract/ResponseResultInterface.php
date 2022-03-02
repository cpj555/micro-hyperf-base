<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Contract;

interface ResponseResultInterface
{
    public function return($result, string $message, string $returnEmptyType);

    public function returnError(int $code, \Throwable $throwable);

    public function returnErrorData($code, array $data, string $message);
}
