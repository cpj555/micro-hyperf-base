<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Model;

use Losingbattle\MicroBase\Contract\SubRepositoryInterface;
use Losingbattle\MicroBase\Exception\CodeException;
use Hyperf\Database\Model\Builder;
use Hyperf\Context\Context;

abstract class AbstractSubRepository implements SubRepositoryInterface
{
    public function setIndex($index)
    {
        Context::set($this->getContextIndex(), $index);
        return $this;
    }

    public function getIndex(): string
    {
        $index = Context::get($this->getContextIndex());
        if (! $index) {
            throw new CodeException('please set subindex');
        }
        return $index;
    }

    public function getBuilder(): Builder
    {
        $table = $this->getModelQuery()->getModel()->getTable() . '_' . $this->getIndex();
        $model = $this->getModelQuery()->getModel()->setTable($table);
        return $this->getModelQuery()->setModel($model);
    }

    public function getContextIndex(): string
    {
        return static::class . 'index';
    }

    abstract protected function getModelQuery(): Builder;
}
