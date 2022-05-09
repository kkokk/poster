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
use Kkokk\Poster\Interfaces\MyPoster;
use Kkokk\Poster\Lang\Poster;//
use Kkokk\Poster\Lang\PosterAbstract;//

class PosterManager 
{

    protected static $options;   // 参数
    protected static $connector; // 实例化类
    protected static $className; // 实现调用类名

	public function __construct($options=[])
	{
        if (!empty($options)) {
            self::$options = $options;
        }
	}

    public function __call($method, $params)
    {
        $lang = new self();
        self::$className = __NAMESPACE__ . '\\Lang\\AbstractTest'; // 使用抽象类实现
        return $lang->create($method, $params);
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     * @throws InvalidManagerException
     */
    public static function __callStatic($method, $params)
    {
        self::$options = $params;
        self::$className = __NAMESPACE__ . '\\Lang\\'.$method; // 使用接口类实现 只是测试不同方式实现
        return self::buildConnector();
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws LangException
     */
    private function create(string $method, array $params)
    {
        return call_user_func_array([self::buildConnector(),$method], $params);
    }

    private static function buildConnector()
    {
        if (!isset(self::$connector)) {

            if (!class_exists(self::$className)) {
                throw new SystemException("the class name does not exist . class : " . self::$className);
            }

            self::$connector = new self::$className(self::$options);
        }
        return self::$connector;
    }
}