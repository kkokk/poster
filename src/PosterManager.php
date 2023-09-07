<?php

namespace Kkokk\Poster;

/**
 * PHP海报生成，添加水印
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 10:38:17
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-08 10:23:08
 */

use Kkokk\Poster\Exception\SystemException;

class PosterManager
{

    protected static $options;   // 参数
    protected static $connector; // 实例化类
    protected static $className; // 实现调用类名

    public function __construct($options = [])
    {
        if (!empty($options)) {
            self::$options = !is_array($options) ? [$options] : $options;
        }
    }

    public function __call($method, $params)
    {
        $lang = new self();
        // self::$className = __NAMESPACE__ . '\\Lang\\AbstractTest'; // 使用抽象类实现
        self::$className = __NAMESPACE__ . '\\Lang\\Poster';
        return $lang->create($method, $params);
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     * @throws SystemException
     */
    public static function __callStatic($method, $params)
    {
        self::$options = $params;
        self::$className = __NAMESPACE__ . '\\Lang\\' . $method; // 使用接口类实现 只是测试不同方式实现
        return self::buildConnector();
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws SystemException
     */
    private function create($method, $params)
    {
        return call_user_func_array([self::buildConnector(), $method], $params);
    }

    private static function buildConnector()
    {
        if (!isset(self::$connector[self::$className])) {

            if (!class_exists(self::$className)) {
                throw new SystemException("the class name does not exist . class : " . self::$className);
            }
            if (empty(self::$options)) {
                self::$connector[self::$className] = new self::$className(self::$options);
            } else {
                self::$connector[self::$className] = new self::$className(...self::$options);
            }
        }
        return self::$connector[self::$className];
    }
}