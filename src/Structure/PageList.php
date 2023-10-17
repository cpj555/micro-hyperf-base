<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Structure;

use Hyperf\Collection\Collection;

class PageList extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
        if (! \count($items)) {
            $this->setCount(0);
            $this->setPageCount(0);
            $this->setList(Collection::make());
        }
    }

    public function setCount(int $count)
    {
        $this->offsetSet('count', $count);
        return $this;
    }

    public function getCount(): int
    {
        return $this->offsetGet('count');
    }

    public function setPageCount(int $pageCount): void
    {
        $this->offsetSet('page_count', $pageCount);
    }

    public function getPageCount(): int
    {
        return $this->offsetGet('page_count');
    }

    public function setList(Collection $list)
    {
        $this->offsetSet('list', $list);
        return $this;
    }

    public function getList(): Collection
    {
        return $this->offsetGet('list');
    }
}
