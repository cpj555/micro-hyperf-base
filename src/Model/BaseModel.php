<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Model;

use Losingbattle\MicroBase\Rewrite\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;

abstract class BaseModel extends Model
{
    public bool $timestamps = false;

    public function newModelBuilder($query)
    {
        return new Builder($query);
    }

    public function batchUpdate($argument, $key_name = 'id'): void
    {
        $sql = 'UPDATE  ' . config('databases.default.prefix', '') . static::getTable() . ' SET ';

        foreach (current($argument) as $key => $value) {
            $sql .= "{$key} = CASE {$key_name} ";
            foreach ($argument as $id => $item) {
                $sql .= sprintf("WHEN '%s' THEN '%s' ", $id, $item[$key]);
            }
            $sql .= 'END, ';
        }
        $sql = rtrim(trim($sql), ',');
        $ids = implode(',', array_keys($argument));
        $sql .= " WHERE {$key_name} IN ({$ids})";

        Db::affectingStatement($sql);
    }
}
