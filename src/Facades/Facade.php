<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/15
 * Time: 14:21
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Exception\PosterException;

/**
 * User: lang
 * @template StoreRepository
 * @package  Kkokk\Poster\Facades
 * @class    Facade
 */
abstract class Facade
{
    protected static $resolvedInstance = [];
    protected static $store = [
        'cache'   => \Kkokk\Poster\Cache\CacheRepository::class,
        'poster'  => \Kkokk\Poster\Image\PosterManager::class,
        'captcha' => \Kkokk\Poster\Lang\Captcha::class,
        'html'    => \Kkokk\Poster\Html\HtmlManager::class,
    ];

    /**
     * User: lang
     * Date: 2025/4/1
     * Time: 13:22
     * @return StoreRepository
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    protected static function getInstance()
    {
        return static::setInstance(static::getFacadeModel());
    }

    /**
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/10
     * Time: 14:14
     * @return string
     * @throws PosterException
     */
    protected static function getFacadeModel()
    {
        throw new PosterException('未获取到模型');
    }

    /**
     * User: lang
     * Date: 2025/4/1
     * Time: 13:23
     * @param $model
     * @return StoreRepository
     */
    protected static function setInstance($model)
    {
        if (is_object($model)) {
            return $model;
        }

        if (!isset(static::$resolvedInstance[$model])) {
            // 按模型缓存实例，避免不同 Facade 相互覆盖或丢失配置。
            static::$resolvedInstance[$model] = new self::$store[$model];
        }

        return static::$resolvedInstance[$model];
    }

    /**
     * 创建独立实例，不读取或替换 Facade 中已缓存的配置。
     *
     * @return StoreRepository
     */
    public static function fresh()
    {
        $model = static::getFacadeModel();
        return new static::$store[$model];
    }

    /**
     * 清除当前 Facade 的实例引用，下一次调用使用默认配置。
     * 已交给调用方的对象仍然有效；不会清空缓存后端的数据。
     *
     * @return void
     */
    public static function reset()
    {
        unset(static::$resolvedInstance[static::getFacadeModel()]);
    }

    public static function __callStatic($method, $args)
    {

        $instance = static::getInstance();

        if (!$instance) {
            throw new PosterException('未找到相关实例与方法');
        }

        return $instance->$method(...$args);
    }
}
