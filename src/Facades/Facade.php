<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/15
 * Time: 14:21
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Exception\PosterException;

abstract class Facade
{

    protected static $resolvedInstance = [];
    protected static $store = [
        'cache' => \Kkokk\Poster\Cache\Repository::class,
        'poster' => \Kkokk\Poster\Image\PosterManager::class,
        'captcha' => \Kkokk\Poster\Lang\Captcha::class,
        'html' => \Kkokk\Poster\Html\HtmlManager::class,
    ];

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

    // 设置实例
    protected static function setInstance($model)
    {
        if (is_object($model)) {
            return $model;
        }

        if (!isset($resolvedInstance[$model])) {
            // 单例
            static::$resolvedInstance = new self::$store[$model];
        }

        return static::$resolvedInstance;
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
