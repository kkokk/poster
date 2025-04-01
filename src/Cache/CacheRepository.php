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

/**
 * User: lang
 * @method put($key, $default = null, $ttl = 0)
 * @method get($key, $default = null)
 * @method pull($key, $default = null)
 * @method has($key)
 * @method forget($key)
 * @method flush()
 * @package Kkokk\Poster\Cache
 * @class   CacheRepository
 */
class CacheRepository
{
    /**
     * @var \Kkokk\Poster\Cache\AbstractAdapter|null
     */
    protected $adapter = null;

    public function __construct($adapter = null)
    {
        if (!$adapter instanceof AbstractAdapter && $adapter !== null) {
            throw new PosterException('Expected an instance of AbstractAdapter');
        }
        $this->adapter = $adapter;
    }

    public function setAdapter($adapter)
    {
        if (!$adapter instanceof AbstractAdapter) {
            throw new PosterException('Expected an instance of AbstractAdapter');
        }
        $this->adapter = $adapter;
        return $this;
    }


    function __call($method, $params)
    {
        if ($this->adapter) {
            return call_user_func_array([$this->adapter, $method], $params);
        }

        // 兼容以前写法
        if (class_exists(LaravelCache::class)) {
            $connector = LaravelCache::class;
        } elseif (class_exists(ThinkCache6::class)) {
            $connector = ThinkCache6::class;
            $method = str_replace('put', 'set', $method);
        } elseif (class_exists(ThinkCache5::class)) {
            $connector = ThinkCache5::class;
            $method = str_replace('put', 'set', $method);
        } else {
            throw new PosterException('No cache driver');
        }
        return call_user_func_array([$connector, $method], $params);
    }

}
