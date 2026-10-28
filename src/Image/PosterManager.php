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

    /**
     * 创建独立扩展及驱动，保留管理器的构造路径，不继承已执行任务的状态。
     *
     * @param string|null $name
     * @return Extension
     */
    public function fresh($name = null)
    {
        return $this->configure($this->makeExtension($this->parseConnectionName($name)));
    }

    /**
     * 移除指定扩展的缓存引用；null 表示移除当前管理器的全部扩展。
     * 已创建的 Builder 仍使用原扩展，重置不会改变进行中的任务。
     *
     * @param string|null $name
     * @return $this
     */
    public function reset($name = null)
    {
        if ($name === null) {
            $this->extensions = [];
        } else {
            unset($this->extensions[$this->parseConnectionName($name)]);
        }
        return $this;
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
