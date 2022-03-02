<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Utils;

abstract class Common
{
    public static function printf(string $data): void
    {
        echo "\033[36m";
        for ($i = 0; $i < \strlen($data); ++$i) {
            $ascii = \ord($data[$i]);
            if ($ascii > 31) {
                $chr = $data[$i];
            } else {
                $chr = ' ';
            }
            printf("%4d: %08b : 0x%02x : %d : %s\n", $i, $ascii, $ascii, $ascii, $chr);
        }
        echo "\033[0m";
    }
}
