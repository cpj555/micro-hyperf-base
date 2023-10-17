<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Rewrite;

use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Exception\SystemException;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    protected function handleNotFound(ServerRequestInterface $request):mixed
    {
        throw new SystemException(Code::HANDLE_NOT_FOUND);
    }

    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request): mixed
    {
        throw new SystemException(Code::METHOD_NOT_ALLOWED);
    }
}
