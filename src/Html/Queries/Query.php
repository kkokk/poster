<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 18:05
 */

namespace Kkokk\Poster\Html\Queries;

abstract class Query
{
    protected $query = [];

    abstract public function makeQuery();

    public function getQuery()
    {
        return static::makeQuery();
    }

    public function buildQuery($type, $params)
    {
        $this->query[] = ['type' => $type, 'params' => $params];
    }
}