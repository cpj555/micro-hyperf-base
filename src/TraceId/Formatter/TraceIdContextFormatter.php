<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\TraceId\Formatter;

use Losingbattle\MicroBase\Constants\Env;
use Losingbattle\MicroBase\TraceId;
use Hyperf\Context\Context;
use Monolog\Formatter\LineFormatter;

class TraceIdContextFormatter extends LineFormatter
{
    public const SIMPLE_FORMAT = "%datetime% [ %channel% ] %level_name% %trace_id% - %message% %context% %extra% \n";

    public function format(array $record): string
    {
        $output = parent::format($record);

        $output = str_replace('%trace_id%', Context::get(TraceId\Constants::CONTEXT_TRACE_ID_KEY), $output);
//        $output = str_replace('%app_name%', env('APP_NAME'), $output);

        if (env('ENV') !== Env::PROD) {
            echo $output;
        }
        return $output;
    }
}
