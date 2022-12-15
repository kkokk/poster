<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/15
 * Time: 14:37
 */

namespace Kkokk\Poster\Cache;
use Illuminate\Support\Facades\Cache as LaravelCache;
use think\Cache as ThinkCache5;
use think\facade\Cache as ThinkCache6;

class Repository
{
    public function put(string $key, $value, $ttl = null){
        $res = false;
        if (class_exists(LaravelCache::class)) {
            $res = LaravelCache::put($key, $value, $ttl);
        } elseif(class_exists(ThinkCache5::class)){
            $res = LaravelCache::set($key, $value, $ttl);
        } elseif(class_exists(ThinkCache6::class)){
            $res = LaravelCache::set($key, $value, $ttl);
        }

        return $res;
    }

    public function pull(string $key, mixed $default = null){

        $value = null;
        if (class_exists(LaravelCache::class)) {
            $value = LaravelCache::pull($key);
        } elseif(class_exists(ThinkCache5::class)){
            $value = ThinkCache5::pull($key);
        } elseif(class_exists(ThinkCache6::class)){
            $value = ThinkCache6::pull($key);
        }

        return $value;
    }
}
