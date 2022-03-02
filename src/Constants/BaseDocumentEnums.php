<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Constants;

class BaseDocumentEnums
{
    public const TRUE_TEXT = '是';

    public const FALSE_TEXT = '否';

    public const TF_MAPPING = [
        self::TRUE_TEXT => 1,
        self::FALSE_TEXT => 0,
    ];
}
