<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/15
 * Time: 14:11
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Cache\AbstractCacheAdapter;
use Kkokk\Poster\Cache\CacheRepository;

/**
 * User: lang
 * @method static setAdapter(AbstractCacheAdapter $adapter)
 * @method static put($key, $default = null, $ttl = 0)
 * @method static get($key, $default = null)
 * @method static pull($key, $default = null)
 * @method static has($key)
 * @method static forget($key)
 * @method static flush()
 * @extends Facade<CacheRepository>
 * @package  Kkokk\Poster\Facades
 * @mixin    CacheRepository
 * @see      \Kkokk\Poster\Cache\CacheInterface
 * @class    Cache
 */
class Cache extends Facade
{
    protected static function getFacadeModel()
    {
        return 'cache';
    }
}