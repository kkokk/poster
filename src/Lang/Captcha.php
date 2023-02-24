<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:10
 */

namespace Kkokk\Poster\Lang;

use Kkokk\Poster\Exception\PosterException;

class Captcha
{
    protected $className = 'slider';

    public function type($type = 'slider'){
        $this->className = ucfirst($type);
        return $this;
    }

    function __call($method, $arguments)
    {
        $className = '\\Kkokk\\Poster\Captcha\\' . $this->className;

        if(!class_exists($className)) throw new PosterException('class not found');

        $instance = new $className;

        return $instance->$method(...$arguments);

    }
}