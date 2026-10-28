<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 10:44
 */

namespace Kkokk\Poster\Cache;

use Kkokk\Poster\Exception\PosterException;

class PredisCacheAdapter extends AbstractCacheAdapter
{
    /**
     * @var \Predis\Client
     */
    protected $redis;

    public function __construct($redis)
    {
        if (!$redis instanceof \Predis\Client) {
            throw new PosterException('Expected an instance of \Predis\Client');
        }
        $this->redis = $redis;
    }

    public function get($key, $default = null)
    {
        $value = $this->redis->get($key);
        return $value !== null ? $value : $default;
    }

    public function pull($key, $default = null)
    {
        // 单个脚本原子读取并删除，兼容尚不支持 GETDEL 的 Redis。
        // key 通过 KEYS 参数传递，供客户端应用前缀，不能拼入脚本文本。
        $script = "local value = redis.call('GET', KEYS[1]); "
            . "if value then redis.call('DEL', KEYS[1]); end; return value";
        $value = $this->redis->eval($script, 1, $key);
        return $value !== null && $value !== false ? $value : $default;
    }

    public function put($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            $result = $this->redis->set($key, $value);
        } else {
            $result = $this->redis->setex($key, $ttl, $value);
        }
        return $result !== null && $result !== false && (string) $result === 'OK';
    }

    public function forget($key)
    {
        return $this->redis->del($key) > 0;
    }

    public function flush()
    {
        return $this->redis->flushdb();
    }

    public function has($key)
    {
        return $this->redis->exists($key) > 0;
    }
}