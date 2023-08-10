<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 17:00
 */

namespace Kkokk\Poster\Html;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Html\Drivers\DriverInterface;

class Html implements HtmlInterface
{
    protected $driver;

    function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function query()
    {
        return new Builder(
            $this,
            $this->getQueryInstance()
        );
    }

    public function load($html)
    {
        return $this->query()->load($html);
    }

    public function render($query)
    {
        return $this->run($query, function ($query) {
            return $this->driver->render($query);
        });
    }

    protected function run($query, \Closure $callback)
    {
        try {
            $result = $callback($query);
        } catch (\Exception $e) {
            throw new PosterException($e->getMessage(), 0, $e);
        }

        return $result;
    }
}