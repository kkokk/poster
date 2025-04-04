<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 11:01
 */

namespace Kkokk\Poster\Cache;

use Kkokk\Poster\Exception\PosterException;

class MemcachedCacheAdapter extends AbstractCacheAdapter
{
    /**
     * @var \Memcached
     */
    protected $memcached;

    public function __construct($memcached)
    {
        if (!$memcached instanceof \Memcached) {
            throw new PosterException('Expected an instance of Memcached');
        }
        $this->memcached = $memcached;
    }

    public function get($key, $default = null)
    {
        $value = $this->memcached->get($key);
        return $this->memcached->getResultCode() == \Memcached::RES_SUCCESS ? $value : $default;
    }

    public function pull($key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }

    public function put($key, $value, $ttl = 0)
    {
        return $this->memcached->set($key, $value, $ttl);
    }

    public function forget($key)
    {
        return $this->memcached->delete($key);
    }

    public function flush()
    {
        return $this->memcached->flush();
    }

    public function has($key)
    {
        return $this->memcached->get($key) !== false;
    }
}