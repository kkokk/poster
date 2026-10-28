<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 10:44
 */

namespace Kkokk\Poster\Cache;

use Kkokk\Poster\Exception\PosterException;

class RedisCacheAdapter extends AbstractCacheAdapter
{
    /**
     * @var \Redis
     */
    protected $redis;

    public function __construct($redis)
    {
        if (!$redis instanceof \Redis) {
            throw new PosterException('Expected an instance of Redis');
        }
        $this->redis = $redis;
    }

    public function get($key, $default = null)
    {
        $value = $this->redis->get($key);
        return $value !== false ? $value : $default;
    }

    public function pull($key, $default = null)
    {
        // GET 与 DEL 在同一事务中执行，同时沿用客户端的前缀和序列化配置。
        // 不能加入调用方已有的事务/流水线，否则无法立即获得消费结果。
        if (method_exists($this->redis, 'getMode') && $this->redis->getMode() !== \Redis::ATOMIC) {
            throw new PosterException('Redis pull requires a connection outside transactions and pipelines');
        }
        if ($this->redis->multi(\Redis::MULTI) === false) {
            throw new PosterException('Cannot start Redis pull transaction');
        }
        try {
            $this->redis->get($key);
            $this->redis->del($key);
            $result = $this->redis->exec();
        } catch (\Exception $e) {
            // 排队阶段失败时退出本适配器创建的事务，不覆盖原始错误。
            try { $this->redis->discard(); } catch (\Exception $ignored) {}
            throw $e;
        }
        if (!is_array($result) || count($result) !== 2) {
            throw new PosterException('Redis pull transaction failed');
        }
        return $result[0] !== false ? $result[0] : $default;
    }

    public function put($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            $result = $this->redis->set($key, $value);
        } else {
            $result = $this->redis->setex($key, $ttl, $value);
        }
        return $result === true;
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