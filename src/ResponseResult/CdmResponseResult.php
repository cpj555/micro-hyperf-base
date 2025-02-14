<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\ResponseResult;

use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Contract\ResponseResultInterface;
use Losingbattle\MicroBase\Utils\Response;

class CdmResponseResult implements ResponseResultInterface
{
    public function return($result, string $message, string $returnEmptyType): \Psr\Http\Message\ResponseInterface
    {
        return Response::withJson($result, $message, Code::SUCCESS, $returnEmptyType);
    }

    public function returnError(int $code, \Throwable $throwable): \Psr\Http\Message\ResponseInterface
    {
        return Response::withError($code, $throwable->getMessage());
    }

    public function returnErrorData($code, array $data, string $message): \Psr\Http\Message\ResponseInterface
    {
        return Response::withJson($data, $message, $code);
    }
}
