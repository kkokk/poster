<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:43
 */


namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Cache\AbstractCacheAdapter;
use Kkokk\Poster\Captcha\Strategies\CaptchaStrategy;
use Kkokk\Poster\Captcha\Strategies\CaptchaStrategyInterface;

class CaptchaManager
{
    protected $channels = [];

    protected $factory;

    protected $defaultDriver = 'gd';

    /**
     * @var AbstractCacheAdapter
     */
    protected $cacheAdapter = null;

    function __construct()
    {
        $this->factory = new CaptchaGeneratorFactory();
    }

    /**
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 14:58
     * @param null $name
     * @return CaptchaStrategy|CaptchaStrategyInterface
     */
    public function type($name = null)
    {
        $name = $this->parseChannelName($name);

        if (!isset($this->extensions[$name])) {
            $this->channels[$name] = $this->configure($this->makeGenerator($name));
        }

        return $this->channels[$name];
    }

    public function extension($driver = null)
    {
        if ($driver) {
            $this->defaultDriver = $driver;
        }
        return $this;
    }

    public function setCache(AbstractCacheAdapter $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
        return $this;
    }

    protected function configure(CaptchaStrategyInterface $generator)
    {
        return $generator;
    }

    protected function parseChannelName($name)
    {
        if (empty($name)) {
            return $this->supportedGenerators()[0];
        }
        return $name;
    }

    protected function makeGenerator($name)
    {
        return $this->factory->make($name, $this->defaultDriver, $this->cacheAdapter);
    }

    /**
     * 获取所有支持方法。
     *
     * @return array
     */
    public function supportedGenerators()
    {
        return ['slider', 'click', 'rotate', 'input'];
    }

    /**
     * 将方法动态传递给默认方法。
     *
     * @param string $method
     * @param array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->type()->$method(...$parameters);
    }
}