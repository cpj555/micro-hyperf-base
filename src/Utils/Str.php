<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Utils;

class Str
{
    public static function replaceSpecial2Empty(string $string)
    {
        $special = ["\t", "\n", "\r"];
        return str_replace($special, '', $string);
    }

    public static function existsInUri(string $uri, array $filterUri)
    {
        foreach ($filterUri as $item) {
            if (strpos($uri, $item) !== false) {
                return true;
            }
        }

        return false;
    }
}
