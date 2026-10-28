<?php

namespace Kkokk\Poster\Cache;

use Kkokk\Poster\Exception\PosterException;

/** 可选装饰器：隔离键名，并禁止将局部清理误转为整个后端清理。 */
class NamespacedCacheAdapter extends AbstractCacheAdapter
{
    private $adapter;
    private $prefix;

    public function __construct(CacheInterface $adapter, $namespace)
    {
        if (!is_string($namespace) || $namespace === '') {
            throw new PosterException('Cache namespace must be a non-empty string');
        }
        $this->adapter = $adapter;
        // 长度标记避免命名空间中出现分隔符时产生相同的物理键。
        $this->prefix = 'poster:' . strlen($namespace) . ':' . $namespace . ':';
    }

    public function get($key, $default = null)
    {
        return $this->adapter->get($this->prefix . $key, $default);
    }

    public function pull($key, $default = null)
    {
        // 原子性由底层提供，不拆成 get/forget。
        return $this->adapter->pull($this->prefix . $key, $default);
    }

    public function put($key, $value, $ttl = null)
    {
        return $this->adapter->put($this->prefix . $key, $value, $ttl);
    }

    public function forget($key)
    {
        return $this->adapter->forget($this->prefix . $key);
    }

    public function has($key)
    {
        return $this->adapter->has($this->prefix . $key);
    }

    public function flush()
    {
        // 通用后端没有安全枚举/批量清理契约，不能调用底层 flush。
        throw new PosterException('Namespaced cache does not support flush; use forget for known keys');
    }
}