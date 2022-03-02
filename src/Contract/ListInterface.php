<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Contract;

interface ListInterface
{
    public static function makeList($items = []);

    public static function getItem();
}
