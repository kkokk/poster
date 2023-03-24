<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 10:56
 */

namespace Kkokk\Poster\Image\Queries;

class Query
{
    protected $query = [];

    public function clearQuery()
    {
        $this->query = [];
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($type, $params)
    {
        $this->query[] = ['type'=>$type, 'params'=> $params];
    }

    public function setCallbackQuery($w, $h, $query)
    {

    }

    public function getQrQuery(...$params)
    {
        $query = [];
        $query['type'] = 'qr';
        $query['params'] = $params;
        return [$query];
    }
}