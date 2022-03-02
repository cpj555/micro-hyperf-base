<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase;

use Losingbattle\MicroBase\Contract\NodeInterface;
use Losingbattle\MicroBase\Contract\ResponseResultInterface;
use Losingbattle\MicroBase\Contract\TraceIdGeneratorInterface;
use Losingbattle\MicroBase\Node\NodeFactory;
use Losingbattle\MicroBase\ResponseResult\ResponseResultFactory;
use Losingbattle\MicroBase\Rewrite\CoreMiddleware;
use Losingbattle\MicroBase\TraceId\TraceIdGeneratorFactory;
use Losingbattle\MicroBase\Visitor\CommentIgnoreVisitor;
use Hyperf\Di\Aop\AstVisitorRegistry;

class ConfigProvider
{
    public function __invoke(): array
    {
        if (! AstVisitorRegistry::exists(CommentIgnoreVisitor::class)) {
            AstVisitorRegistry::insert(CommentIgnoreVisitor::class, PHP_INT_MAX / 2);
        }

        return [
            'dependencies' => [
                \Hyperf\HttpServer\CoreMiddleware::class => CoreMiddleware::class,
                NodeInterface::class => NodeFactory::class,
                TraceIdGeneratorInterface::class => TraceIdGeneratorFactory::class,
                ResponseResultInterface::class => ResponseResultFactory::class,
            ],
            'aspects' => [
            ],
            'processes' => [
            ],
            'listeners' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'class_map' => [
                        \Hyperf\Database\Query\Grammars\Grammar::class => BASE_PATH . '/vendor/Losingbattle/micro-base/class_map/hyperf/database/src/Query/Grammars/Grammar.php',
                        \Hyperf\Database\Query\Builder::class => BASE_PATH . '/vendor/Losingbattle/micro-base/class_map/hyperf/database/src/Query/Builder.php',
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for request.',
                    'source' => __DIR__ . '/../publish/request.php',
                    'destination' => BASE_PATH . '/config/autoload/request.php',
                ],
                [
                    'id' => 'config',
                    'description' => 'The config for response.',
                    'source' => __DIR__ . '/../publish/response.php',
                    'destination' => BASE_PATH . '/config/autoload/response.php',
                ],
                [
                    'id' => 'config',
                    'description' => 'The config for trace_id.',
                    'source' => __DIR__ . '/../publish/trace_id.php',
                    'destination' => BASE_PATH . '/config/autoload/trace_id.php',
                ],
            ],
        ];
    }
}
