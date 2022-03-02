<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Constants;

class HttpStatusCode
{
    public const OK = 200;

    public const UNAUTHORIZED = 401;

    public const FORBIDDEN = 403;

    public const NOT_FOUND = 404;

    public const INTERNEL_SERVER_ERROR = 500;

    public const BAD_GATEWAY = 502;

    public const GATEWAY_TIMEOUT = 504;

    public const SERVICE_UNAVAILABLE = 503;

    public const STATUS_MAP = [
        self::INTERNEL_SERVER_ERROR => '服务器内部错误',
        self::BAD_GATEWAY => '目标服务器不可用',
    ];
}
