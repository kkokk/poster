<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 10:44
 */

namespace Kkokk\Poster\Cache;

use Kkokk\Poster\Exception\PosterException;

class PredisAdapter extends AbstractAdapter
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
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }

    public function put($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            $this->redis->set($key, $value);
        } else {
            $this->redis->setex($key, $ttl, $value);
        }
        return true;
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