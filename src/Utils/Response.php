<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Utils;

use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Constants\ResponseConstants;
use Losingbattle\MicroBase\Exception\CodeException;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Context\ApplicationContext;
use Hyperf\Collection\Collection;

class Response
{
    public static function withJson($array = null, string $message = '', int $code = Code::SUCCESS, string $returnEmptyType = ResponseConstants::TYPE_OBJECT)
    {
        /** @var \Hyperf\HttpServer\Response $response */
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);

        if ($array instanceof Collection) {
            $array = $array->toArray();
        }

        switch ($returnEmptyType) {
            case ResponseConstants::TYPE_OBJECT:
                $emptyType = new \stdClass();
                break;
            case ResponseConstants::TYPE_ARRAY:
                $emptyType = [];
                break;
            case ResponseConstants::TYPE_NULL:
                $emptyType = null;
                break;
            default:
                throw new CodeException('undefined returnEmptyType');
        }

        if ($array === null) {
            $array = $emptyType;
        }

        $responseMessageKey = config('response.message_key', 'message');

        $data = [
            'code' => $code,
            'data' => \is_array($array) ? (\count($array) ? $array : $emptyType) : $array,
            $responseMessageKey => $message ? $message : Code::getMessage(Code::SUCCESS),
        ];

        return $response->json($data);
    }

    public static function withError($code, string $message = '')
    {
        $message = $message ?  : Code::getMessage($code);
        return self::withJson(null, $message, $code);
    }
}
