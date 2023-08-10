<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 18:04
 */

namespace Kkokk\Poster\Html;

use Kkokk\Poster\Html\Queries\Query;

class Builder
{

    protected $channel;

    protected $query;

    function __construct(HtmlInterface $channel, Query $query)
    {
        $this->channel = $channel;
        $this->query = $query;
    }

    public function load($html)
    {
        $this->query->buildQuery('load', [$html]);
        return $this;
    }

    public function transparent($transparent = true)
    {
        $this->query->buildQuery('transparent', [$transparent]);
        return $this;
    }

    public function size($width = 0, $height = 0)
    {
        $this->query->buildQuery('size', [$width, $height]);
        return $this;
    }

    public function crop($crop_w = 0, $crop_h = 0, $crop_x = 0, $crop_y = 0)
    {
        $this->query->buildQuery('crop', [$crop_w, $crop_h, $crop_x, $crop_y]);
        return $this;
    }

    public function type($type = 'png')
    {
        $this->query->buildQuery('type', [$type]);
        return $this;
    }

    public function command($command = '')
    {
        $this->query->buildQuery('command', [$command]);
        return $this;
    }

    public function output($path, $type = '')
    {
        $this->query->buildQuery('output', [$path, $type]);
        return $this;
    }

    public function quality($quality)
    {
        $this->query->buildQuery('quality', [$quality]);
        return $this;
    }

    public function render()
    {
        return $this->channel->render($this->query->getQuery());
    }
}