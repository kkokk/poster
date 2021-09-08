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
    public $Poster;
    public static $options = "";
    /** @var Connector */
    protected static $connector;

	public function __construct($options=[])
	{
        if (!empty($options)) {
            self::$options = $options;
        }
        // $this->Poster = new Poster;
	}

	/**
     * @param $method
     * @param $params
     * @return mixed
     * @throws InvalidManagerException
     */
    public static function __callStatic($method, $params)
    {   

        $lang = new self();
        return $lang->create($method, $params);
    }

    public function __call($method, $params)
    {   

        $lang = new self();
        return $lang->create_($method, $params);
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws LangException
     */
    private function create(string $method, array $params)
    {
        $className = __NAMESPACE__ . '\\Lang\\' . $method;
        if (!class_exists($className)) {
            throw new SystemException("the method name does not exist . method : {$method}");
        }
        return $this->make($className, $params);
    }

    /**
     * @param string $className
     * @param array $params
     * @return mixed
     * @throws InvalidManagerException
     */
    private function make(string $className, array $params)
    {
        $lang = new $className($params);


        if ($lang instanceof MyPoster) {
            return $lang;
        }
        throw new SystemException("this method does not integrate MyPoster . namespace : {$className}");
    }

    private static function buildConnector()
    {
        $options = [];
        $type    = !empty($options['connector']) ? $options['connector'] : 'AbstractTest';


        if (!isset(self::$connector)) {

            $className = false !== strpos($type, '\\') ? $type :  __NAMESPACE__.'\\Lang\\' .$type;

            if (!class_exists($className)) {
                throw new SystemException("the class name does not exist . class : {$type}");
            }

            self::$connector = new $className(self::$options);
        }
        return self::$connector;
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws LangException
     */
    private function create_(string $method, array $params)
    {   

        return call_user_func_array([self::buildConnector(),$method], $params);
    }
}