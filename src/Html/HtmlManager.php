<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 16:55
 */

namespace Kkokk\Poster\Html;

class HtmlManager
{
    protected $channels = [];

    private $factory;

    function __construct()
    {
        $this->factory = new HtmlFactory;
    }

    public function channel($name = null)
    {
        $name = $this->parseConnectionName($name);

        if (!isset($this->channels[$name])) {
            $this->channels[$name] = $this->configure($this->makeChannel($name));
        }

        return $this->channels[$name];
    }

    protected function configure(Html $Html)
    {
        return $Html;
    }

    protected function parseConnectionName($name)
    {
        if (empty($name)) return $this->supportedChannels()[0];
        return $name;
    }

    protected function makeChannel($name)
    {
        return $this->factory->make($name);
    }

    /**
     * 获取所有支持通道。
     *
     * @return array
     */
    public function supportedChannels()
    {
        return ['wk'];
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->channel()->$method(...$parameters);
    }
}