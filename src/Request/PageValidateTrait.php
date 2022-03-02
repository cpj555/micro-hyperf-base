<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Request;

trait PageValidateTrait
{
    public function getPage()
    {
        return $this->get('page');
    }

    public function getLimit()
    {
        return $this->get('limit');
    }

    public function getPageRules(): array
    {
        return [
            ['page', 'number', 'default' => 1],
            ['limit', 'number', 'default' => 20],
        ];
    }

    public function getPageScenariosValues()
    {
        return ['page', 'limit'];
    }
}
