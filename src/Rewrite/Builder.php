<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Rewrite;

use Losingbattle\MicroBase\Structure\PageList;

class Builder extends \Hyperf\Database\Model\Builder
{
    public function getPageList(int $page, int $limit, string $countKey = 'id'): PageList
    {
        $offset = ($page - 1) * $limit;
        $count = $this->count($countKey);

        $data['count'] = $count;
        $data['page_count'] = bcdiv((string) $count, (string) $limit, 0) + ($count % $limit === 0 ? 0 : 1);
        $data['list'] = $this->limit($limit)->offset($offset)->get();

        return PageList::make($data);
    }
}
