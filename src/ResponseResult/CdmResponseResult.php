<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\ResponseResult;

use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Contract\ResponseResultInterface;
use Losingbattle\MicroBase\Utils\Response;

class CdmResponseResult implements ResponseResultInterface
{
    public function return($res, string $message, string $returnEmptyType)
    {
        return Response::withJson($res, $message, Code::SUCCESS, $returnEmptyType);
    }

    public function returnError(int $code, \Throwable $throwable)
    {
        return Response::withError($code, $throwable->getMessage());
    }

    public function returnErrorData($code, array $data, string $message)
    {
        return Response::withJson($data, $message, $code);
    }
}
