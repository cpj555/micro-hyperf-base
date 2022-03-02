<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Contract;

interface SubRepositoryInterface
{
    public function setIndex($index);

    public function getBuilder();

    public function getIndex(): string;

    public function getContextIndex(): string;
}
