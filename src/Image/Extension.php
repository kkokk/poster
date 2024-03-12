<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 18:01
 */

namespace Kkokk\Poster\Image;

use Kkokk\Poster\Exception\PosterException;

class Extension implements ExtensionInterface
{

    /**
     * @var \Kkokk\Poster\Image\Drivers\DriverInterface $driver
     */
    protected $driver;

    /**
     * @var string
     */
    protected $path;

    function __construct($driver, $path)
    {
        $this->driver = $driver;
        $this->path = $path;
    }

    public function config($params = [])
    {
        return $this->query()->config($params);
    }

    public function buildIm($w, $h, $rgba = [], $alpha = false)
    {
        return $this->query()->buildIm($w, $h, $rgba, $alpha);
    }

    public function buildImDst($src, $w = 0, $h = 0)
    {
        return $this->query()->buildImDst($src, $w, $h);
    }

    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, \Closure $callback = null)
    {
        return $this->query()->buildBg($w, $h, $rgba, $alpha, $dst_x, $dst_y, $src_x, $src_y, $callback);
    }

    public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0)
    {
        return $this->getDriverInstance()->createQr($text, $outfile, $level, $size, $margin, $saveAndPrint);
    }

    public function getPoster($query, $path)
    {
        return $this->getDriverInstance($query)->getData($path);
    }

    public function setPoster($query)
    {
        return $this->getDriverInstance($query)->setData();
    }

    public function stream($query)
    {
        return $this->getDriverInstance($query)->getStream();
    }

    public function baseData($query)
    {
        return $this->getDriverInstance($query)->getBaseData();
    }

    public function getIm($query)
    {
        return $this->getDriverInstance($query)->getIm();
    }

    public function getImInfo($query){
        return $this->getDriverInstance($query)->getImInfo();
    }

    public function blob($query)
    {
        return $this->getDriverInstance($query)->blob();
    }

    public function tmp($query)
    {
        return $this->getDriverInstance($query)->tmp();
    }

    public function crop($x = 0, $y = 0, $width = 0, $height = 0) {
        return $this->query()->crop($x, $y, $width, $height);
    }

    public function query()
    {
        return new Builder(
            $this,
            $this->getQueryInstance(),
            $this->path
        );
    }

    /**
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/24
     * Time: 15:45
     * @param $query
     * @return \Kkokk\Poster\Image\Drivers\Driver
     * @throws PosterException
     */
    protected function getDriverInstance($query = [])
    {
        return $this->run($query, function ($query) {
            return $this->driver->execute($query);
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