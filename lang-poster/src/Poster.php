<?php
namespace Kkokk\LangPoster;
/**
 * 生成器
 */
class Poster 
{
	
	public function __construct()
	{
		# code...
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
        return $lang->create($method, $params);
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws LangException
     */
    private function create(string $method, array $params)
    {
        $className = __NAMESPACE__ . '\\Tools\\' . $method;
        if (!class_exists($className)) {
            throw new InvalidManagerException("the method name does not exist . method : {$method}");
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
        if ($lang instanceof IVideo) {
            return $lang;
        }
        throw new InvalidManagerException("this method does not integrate IVideo . namespace : {$className}");
    }
}