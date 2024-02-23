<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\TraceId\Formatter;

use Losingbattle\MicroBase\Constants\Env;
use Losingbattle\MicroBase\Contract\TraceIdGeneratorInterface;
use Hyperf\Context\ApplicationContext;
use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;
use function Hyperf\Support\env;

class TraceIdFormatter extends LineFormatter
{
    public const SIMPLE_FORMAT = "%datetime% [ %channel% ] %level_name% %trace_id% - %message% %context% %extra% \n";

    public function format(LogRecord $record): string
    {
        $container = ApplicationContext::getContainer();

        $output = parent::format($record);

        if ($container->has(TraceIdGeneratorInterface::class)) {
            $traceIdGenerator = $container->get(TraceIdGeneratorInterface::class);
            $traceId = $traceIdGenerator->generate();
            $output = str_replace('%trace_id%', $traceId, $output);
        }

//        $output = str_replace('%app_name%', env('APP_NAME'), $output);

        if (env('ENV') !== Env::PROD) {
            echo $output;
        }
        return $output;
    }
}
