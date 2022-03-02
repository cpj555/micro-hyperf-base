<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Constants;

class Env
{
    /**
     * 本地环境 变量.
     */
    public const LOCAL = 'local';

    /**
     * 测试环境 变量.
     */
    public const QA = 'qa';

    /**
     * 压测环境 变量.
     */
    public const STRESS = 'stress';

    /**
     * 预发环境 变量.
     */
    public const PRE = 'pre';

    /**
     * 生产环境 变量.
     */
    public const PROD = 'prod';
}
