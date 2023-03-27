<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/27
 * Time: 11:48
 */

namespace Kkokk\Poster\Image\Traits;


use Kkokk\Poster\Exception\PosterException;

trait ImagickTrait
{
    /**
     * 返回图片流或者图片
     * @Author lang
     * @Date   2020-08-14T14:29:57+0800
     * @return void|array
     */
    protected function returnImage($type, $outfile = true)
    {
        if (!isset($this->im) || empty($this->im)) throw new PosterException('没有创建任何资源');
        if ($outfile) {
            $this->dirExists($this->pathname);
            if (strripos($this->filename, '.') === false) {
                $this->filename = $this->filename . '.' . $type;
            }
            $this->im->writeImage($this->path . $this->pathname . '/' . $this->filename);
            return ['url' => $this->pathname . '/' . $this->filename];
        }
        header('Content-Type:Image/' . $type);
        echo $this->im->getImageBlob();
    }

    /**
     * 创建文字绘画类
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 16:58
     * @return \ImagickDraw
     */
    public function createTextImagickDraw()
    {
        if (empty($this->ImagickDraw)) {
            $this->ImagickDraw = new \ImagickDraw();
            $this->ImagickDraw->settextencoding('UTF-8');
        }
        return $this->ImagickDraw;
    }

    public function createImagickDraw(){
        return new \ImagickDraw();
    }

    public function createImagick($src = '')
    {
        $Imagick = new \Imagick();
        if ($src) {
            $stream = @file_get_contents($src, NULL);
            if (empty($stream)) throw new PosterException('image resources cannot be empty (' . $src . ')');
            $Imagick->readImageBlob($stream);
        }
        return $Imagick;
    }

    /**
     * 创建画布
     */
    public function createIm($w, $h, $rgba, $alpha = false)
    {
        $color = $alpha ? $this->createColorAlpha($rgba) : $this->createColor($rgba);
        $image = new \Imagick();
        $image->newImage($w, $h, $color, $this->type);//设置画布的信息以及画布的格式
        if (!empty($this->dpi)) {
            $image->resampleImage($this->dpi[0], $this->dpi[1], \Imagick::RESOLUTION_PIXELSPERINCH, 0); //设置画布的dpi
        }
        return $image;
    }

    /**
     * 获取颜色值，可设置透明度
     */
    public function createColorAlpha($rgba = [255, 255, 255, 127])
    {

        if (empty($rgba)) $rgba = [255, 255, 255, 127];
        if (count($rgba) != 4) throw new PosterException('The length of the rgba parameter is 4');
        foreach ($rgba as $k => $value) {
            if (!is_int($rgba[$k])) {
                throw new PosterException('The value must be an integer');
            } elseif ($k < 3 && ($rgba[$k] > 255 || $rgba[$k] < 0)) {
                throw new PosterException('The color value is between 0-255');
            } elseif ($k == 3 && ($rgba[$k] > 127 || $rgba[$k] < 0)) {
                throw new PosterException('The alpha value is between 0-127');
            }
        }

        $rgba[3] = sprintf("%.2f", (128 - $rgba[3]) / 127);

        return new \ImagickPixel("rgba($rgba[0], $rgba[1], $rgba[2], $rgba[3])");
    }

    /**
     * 获取颜色值，没有透明度
     */
    public function createColor($rgba = [255, 255, 255, 1])
    {

        if (empty($rgba)) $rgba = [255, 255, 255, 1];
        if (count($rgba) < 4) throw new PosterException('The length of the rgba parameter is 4');
        foreach ($rgba as $k => $value) {
            if (!is_int($rgba[$k])) {
                throw new PosterException('The text value must be an integer');
            } elseif ($k < 3 && ($rgba[$k] > 255 || $rgba[$k] < 0)) {
                throw new PosterException('The text color value is between 0-255');
            }
        }

        return new \ImagickPixel("rgb($rgba[0], $rgba[1], $rgba[2])");
    }
}