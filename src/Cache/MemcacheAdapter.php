<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 11:01
 */

namespace Kkokk\Poster\Cache;

use Kkokk\Poster\Exception\PosterException;

class MemcacheAdapter extends AbstractAdapter
{
    /**
     * @var \Memcache
     */
    protected $memcache;

    public function __construct($memcache)
    {
        if (!$memcache instanceof \Memcache) {
            throw new PosterException('Expected an instance of Memcache');
        }
        $this->memcache = $memcache;
    }

    public function get($key, $default = null)
    {
        $value = $this->memcache->get($key);
        return $value !== false ? $value : $default;
    }

    public function pull($key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }

    public function put($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            $ttl = 0; // 默认永不过期
        }
        return $this->memcache->set($key, $value, 0, $ttl);
    }

    public function forget($key)
    {
        return $this->memcache->delete($key);
    }

    public function flush()
    {
        return $this->memcache->flush();
    }

    public function has($key)
    {
        return $this->get($key) !== null;
    }
}