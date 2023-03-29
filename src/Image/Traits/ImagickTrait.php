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

    protected function setDPI()
    {
        if (!isset($this->im) || empty($this->im)) throw new PosterException('没有创建任何资源');
        if (!empty($this->dpi)) {
            $this->im->resampleImage($this->dpi[0], $this->dpi[1], ($this->im)::RESOLUTION_PIXELSPERINCH, 0); //设置画布的dpi
        }
    }

    /**
     * 返回图片流或者图片
     * @Author lang
     * @Date   2020-08-14T14:29:57+0800
     * @return void|array
     */
    protected function returnImage($type, $outfile = true)
    {
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

    protected function setImage($source)
    {

        if (strpos($source, 'http') === 0) {
            throw new PosterException("unable to set the remote source {$source}");
        }

        if (!empty($source)) {
            return $this->im->writeImage($source);
        }

        throw new PosterException("source not found {$source}");
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

    public function createImagickDraw()
    {
        return new \ImagickDraw();
    }

    public function createImagick($src = '')
    {
        $Imagick = new \Imagick();
        if ($src) {
            if (strpos($src, 'http') === 0) {
                $stream = @file_get_contents($src, NULL);
                if (empty($stream)) throw new PosterException('image resources cannot be empty (' . $src . ')');
                $Imagick->readImageBlob($stream);
            } else {
                $Imagick->readImage($src);
            }

        }
        return $Imagick;
    }

    /**
     * 创建画布
     */
    public function createIm($w, $h, $rgba, $alpha = false)
    {
        $color = $alpha ? $this->createColorAlpha($rgba) : $this->createColor($rgba);
        $image = $this->createImagick();
        $image->newImage($w, $h, $color, $this->type);//设置画布的信息以及画布的格式
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

    /**
     * 剪裁图片并复制（复制指定坐标内容）
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/28
     * Time: 11:44
     * @param mixed $source 读取原图片
     */
    public function cropImage(\Imagick $source, $src_x, $src_y)
    {
        // 裁剪原图片，仅保留指定坐标的内容
        if ($src_x > 0 || $src_y > 0) {
            $width = $source->getImageWidth();
            $height = $source->getImageHeight();
            $source->cropImage($width - $src_x, $height - $src_y, $src_x, $src_y);
        }

    }

    /**
     * 渐变色，目前只支持两种颜色渐变，暂时只支持从上往下
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/28
     * Time: 17:04
     * @param \Imagick $source
     * @param $rgbaColor
     * @param $rgbaCount
     * @param $to
     * @param $w
     * @param $h
     * @throws \ImagickException
     */
    public function calcColorDirection(\Imagick $source, $rgbaColor, $rgbaCount, $to, $w, $h)
    {
        // if(!empty($rgbaColor)){
        //     for ($i = 0; $i < $rgbaCount - 1; $i++) {
        //         if(isset($rgbaColor[$i + 1]))
        //         {
        //             $rgbBox[] = [
        //                 "rgb(" . $rgbaColor[$i][0] . "," . $rgbaColor[$i][1] . "," . $rgbaColor[$i][2] . ")",
        //                 "rgb(" . $rgbaColor[$i + 1][0] . "," . $rgbaColor[$i + 1][1] . "," . $rgbaColor[$i + 1][2] . ")"
        //             ];
        //         } else {
        //             $rgbBox[] = ["rgb(" . $rgbaColor[$i][0] . "," . $rgbaColor[$i][1] . "," . $rgbaColor[$i][2] . ")"];
        //         }
        //
        //     }
        //
        //     foreach ($rgbBox as $box){
        //         if(count($box) == 1) {
        //             $source->newPseudoImage($w, $h, "gradient:$box[0]");
        //         } else {
        //             $source->newPseudoImage($w, $h, "gradient:$box[0]-$box[1]");
        //         }
        //     }
        // }
        if ($rgbaCount == 1) {
            $rgb1 = "rgb(" . $rgbaColor[0][0] . "," . $rgbaColor[0][1] . "," . $rgbaColor[0][2] . ")";
            $source->newPseudoImage($w, $h, "gradient:$rgb1-$rgb1");
        } elseif ($rgbaCount > 1) {
            $rgb1 = "rgb(" . $rgbaColor[0][0] . "," . $rgbaColor[0][1] . "," . $rgbaColor[0][2] . ")";
            $rgb2 = "rgb(" . $rgbaColor[1][0] . "," . $rgbaColor[1][1] . "," . $rgbaColor[1][2] . ")";
            $source->newPseudoImage($w, $h, "gradient:$rgb1-$rgb2");
        }

    }

    protected function fontWeight($draw, $weight, $fontSize, $angle, $dst_x, $dst_y, $contents)
    {
        for ($i = 0; $i < $weight; $i++) {

            list($really_dst_x, $really_dst_y) = $this->calcWeight($i, $weight, $fontSize, $dst_x, $dst_y);

            if ($this->type == 'gif') {
                foreach ($this->im as $frame) {
                    $frame->annotateImage($draw, $really_dst_x, $really_dst_y, $angle, $contents);
                }
            } else {
                $this->im->annotateImage($draw, $really_dst_x, $really_dst_y, $angle, $contents);
            }
        }
    }

}