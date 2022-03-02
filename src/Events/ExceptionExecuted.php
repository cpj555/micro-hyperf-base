<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Events;

use Hyperf\HttpServer\Contract\RequestInterface;
use Throwable;

class ExceptionExecuted
{
    /**
     * @var Throwable
     */
    public $throwable;

    /**
     * @var RequestInterface
     */
    public $request;

    public function __construct(Throwable $throwable, RequestInterface $request)
    {
        $this->throwable = $throwable;
        $this->request = $request;
    }
}
