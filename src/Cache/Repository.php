<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/15
 * Time: 14:37
 */

namespace Kkokk\Poster\Cache;

use Kkokk\Poster\Exception\PosterException;
use Illuminate\Support\Facades\Cache as LaravelCache;
use think\Cache as ThinkCache5;
use think\facade\Cache as ThinkCache6;

class Repository
{
    function __call($method, $params)
    {
        if (class_exists(LaravelCache::class)) {
            $connector = LaravelCache::class;
        } elseif (class_exists(ThinkCache6::class)) {
            $connector = ThinkCache6::class;
            $method = str_replace('put', 'set', $method);
        } elseif (class_exists(ThinkCache5::class)) {
            $connector = ThinkCache5::class;
            $method = str_replace('put', 'set', $method);
        } else {
            throw new PosterException('no cacheDriver');
        }
        return call_user_func_array([$connector, $method], $params);
    }

}
