<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 13:43
 */

namespace Kkokk\Poster\Image;

class PosterManager
{
    protected $extensions = [];

    protected $factory;

    protected $path;

    function __construct($path = null) // 兼容老版本设置路径
    {
        $this->path = $path;
        $this->factory = new ExtensionFactory;
    }

    public function extension($name = null)
    {
        $name = $this->parseConnectionName($name);

        if (!isset($this->extensions[$name])) {
            $this->extensions[$name] = $this->configure($this->makeExtension($name));
        }

        return $this->extensions[$name];
    }

    protected function configure(Extension $extension)
    {
        return $extension;
    }

    protected function parseConnectionName($name)
    {
        if (empty($name)) return $this->supportedExtensions()[0];
        return $name;
    }

    /**
     * 创建一个拓展实例
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/22
     * Time: 13:58
     * @param $name
     */
    protected function makeExtension($name)
    {
        return $this->factory->make($name, $this->path);
    }

    /**
     * 获取所有支持拓展。
     *
     * @return array
     */
    public function supportedExtensions()
    {
        return ['gd', 'imagick'];
    }

    /**
     * 将方法动态传递给默认拓展。
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->extension()->$method(...$parameters);
    }
}