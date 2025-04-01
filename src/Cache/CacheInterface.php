<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 10:29
 */

namespace Kkokk\Poster\Cache;

interface CacheInterface
{
    /**
     * 从缓存中检索项目
     * User: lang
     * Date: 2025/4/1
     * Time: 10:31
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * 检索并删除
     * User: lang
     * Date: 2025/4/1
     * Time: 10:31
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function pull($key, $default = null);

    /**
     * 存储项目在缓存中
     * User: lang
     * Date: 2025/4/1
     * Time: 10:31
     * @param                        $key
     * @param                        $value
     * @param \DateInterval|null|int $ttl
     * @return mixed
     */
    public function put($key, $value, $ttl = null);

    /**
     * 从缓存中删除项目
     * User: lang
     * Date: 2025/4/1
     * Time: 10:32
     * @param string $key
     * @return mixed
     */
    public function forget($key);

    /**
     * 清空缓存
     * User: lang
     * Date: 2025/4/1
     * Time: 10:33
     * @return bool
     */
    public function flush();

    /**
     * 确定项目存在性
     * User: lang
     * Date: 2025/4/1
     * Time: 10:33
     * @param string $key
     * @return mixed
     */
    public function has($key);
}