<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 17:17
 */

namespace Kkokk\Poster\Html;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Html\Drivers\WkDriver;

class HtmlFactory
{
    public function make($name)
    {
        return $this->createInstance($name);
    }

    protected function createDriver($name)
    {
        switch ($name) {
            case 'wk':
                return new WkDriver();
        }

        throw new PosterException("Unsupported driver [{$name}].");
    }

    private function createInstance($name)
    {
        switch ($name) {
            case 'wk':
                return new WkHtml($this->createDriver($name));
        }

        throw new PosterException("Unsupported channel [{$name}].");
    }
}