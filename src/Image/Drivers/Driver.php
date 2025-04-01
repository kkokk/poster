<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:28
 */

namespace Kkokk\Poster\Image\Drivers;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Gd\Qr;
use Kkokk\Poster\Image\Graphics\ImageGraphicsEngine;
use Kkokk\Poster\Image\Graphics\TextGraphicsEngine;

class Driver
{
    /** @var \Kkokk\Poster\Image\Gd\Canvas|\Kkokk\Poster\Image\Imagick\Canvas 画布 */
    protected $canvas;

    /** @var resource 画布 */
    protected $image;

    /** @var string 路径和文件名 */
    protected $path;

    /** @var array 基础配置 */
    protected $configs = [];

    public function getCanvas()
    {
        return $this->canvas;
    }

    public function getImage()
    {
        return $this->canvas->getImage();
    }

    public function getWidth()
    {
        return $this->canvas->getWidth();
    }

    public function getHeight()
    {
        return $this->canvas->getHeight();
    }

    public function getData($path = '')
    {
        return $this->canvas->getData($path ?: $this->path);
    }

    public function getStream($type)
    {
        return $this->canvas->getStream($type);
    }

    public function getBaseData()
    {
        return $this->canvas->getBaseData();
    }

    public function setData()
    {
        return $this->canvas->setData();
    }

    public function blob()
    {
        return $this->canvas->blob();
    }

    public function tmp()
    {
        return $this->canvas->tmp();
    }

    /**
     * 设置基本配置
     * @Author lang
     * @Email  : 732853989@qq.com
     * Date: 2023/2/12
     * Time: 下午10:09
     * @param array $configs
     */
    public function setConfig($configs = [])
    {
        $this->configs = $configs;
    }

    public function setCanvasConfig(ImageGraphicsEngine $canvas)
    {
        $canvas->config($this->configs);
    }

    /**
     * 设置文件路径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:55
     * @param $path
     * @throws PosterException
     */
    public function setFilePath($path)
    {
        $this->path = $path;
    }

    /**
     * 生成二维码
     * @Author lang
     * @Date   2020-10-14T10:59:28+0800
     * @param  [type]                   $text         [二维码包含的内容，可以是链接、文字、json字符串等等]
     * @param  [type]                   $outfile      [默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径]
     * @param  [type]                   $level        [容错级别，默认为L]
     *      可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)。
     *      这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别
     * @param  [type]                   $size         [控制生成图片的大小，默认为4]
     * @param  [type]                   $margin       [控制生成二维码的空白区域大小]
     * @param  [type]                   $saveAndPrint [保存二维码图片并显示出来，$outfile必须传递图片路径]
     * @return mixed
     */
    public function createQr($text, $outfile, $level, $size, $margin, $saveAndPrint)
    {
        $qr = new Qr($text, $level, $size, $margin);
        if ($outfile) {
            $qr->getData($outfile);
            if ($saveAndPrint) {
                return $qr->getStream();
            }
            return ['url' => $outfile];
        } else {
            return $qr->getStream();
        }
    }

    public function execute($query = [], Driver $driver = null)
    {
        if (empty($driver)) {
            $driver = $this;
        }
        foreach ($query as $item) {
            $driver->run($item, $driver);
        }
        return $driver;
    }

    public function run($item, Driver $driver)
    {
        switch ($item['type']) {
            case 'im':
                $driver->Im(...$item['params']);
                break;
            case 'imDst':
                $driver->ImDst(...$item['params']);
                break;
            case 'bg':
                $driver->Bg(...$item['params']);
                break;
            case 'config':
                $driver->setConfig($item['params']);
                break;
            case 'path':
                $driver->setFilePath($item['params']);
                break;
            case 'image':
                $driver->CopyImage(...$item['params']);
                break;
            case 'text':
                $driver->CopyText(...$item['params']);
                break;
            case 'line':
                $driver->CopyLine(...$item['params']);
                break;
            case 'arc':
                $driver->CopyArc(...$item['params']);
                break;
            case 'qrs':
                $driver->CopyQr(...$item['params']);
                break;
            case 'qr':
                $driver->createQr(...$item['params']);
                break;
            case 'crop':
                $driver->crop(...$item['params']);
                break;
        }
    }

    /**
     * 获取图片信息
     * Author: lang
     * Date: 2024/3/12
     * Time: 11:08
     * @return array
     */
    public function getCanvasInfo()
    {
        return [
            'canvas' => $this->canvas,
            'type'   => $this->canvas->getType(),
            'width'  => $this->canvas->getWidth(),
            'height' => $this->canvas->getHeight(),
        ];
    }

    protected function maskBackgroundResolve($maskRgba)
    {
        $rgba = isset($maskRgba['color']) ? $maskRgba['color'] : [[0, 0, 0]];
        $transparency = isset($maskRgba['alpha']) ? $maskRgba['alpha'] : 1;
        $to = isset($maskRgba['to']) ? $maskRgba['to'] : 'bottom';
        $radius = isset($maskRgba['radius']) ? $maskRgba['radius'] : 0;
        $contentAlpha = isset($maskRgba['content_alpha']) ? $maskRgba['content_alpha'] : false;
        return [$rgba, $transparency, $to, $radius, $contentAlpha];
    }

    public function __clone()
    {
        $this->canvas = clone $this->canvas;
    }
}