<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/27
 * Time: 11:48
 */

namespace Kkokk\Poster\Image\Traits;


use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Html\Drivers\DriverInterface;
use Kkokk\Poster\Image\Enums\ImageType;
use Kkokk\Poster\Image\Graphics\GdImageGraphicsEngine;
use Kkokk\Poster\Image\Graphics\ImagickImageGraphicsEngine;

trait ImagickTrait
{
    protected $ImagickDraw;

    protected function setDPI()
    {
        if (!isset($this->image) || empty($this->image)) {
            throw new PosterException('没有创建任何资源');
        }
        if (!empty($this->dpi)) {
            $this->image->resampleImage($this->dpi[0], $this->dpi[1], ($this->image)::RESOLUTION_PIXELSPERINCH,
                0); //设置画布的dpi
        }
    }

    /**
     * 返回图片流或者图片
     * User: lang
     * Date: 2024/11/28
     * Time: 10:34
     * @param $type
     * @param $outfile
     * @return string|string[]|void
     * @throws \ImagickException
     */
    protected function returnImage($type, $outfile = true)
    {
        if (in_array($type, ImageType::setQuantityTypes())) {
            $this->image->setImageCompressionQuality($this->getQuantity());
        }
        if ($outfile) {
            dir_exists($this->path . $this->pathname);
            if (strripos($this->filename, '.') === false) {
                $this->filename = $this->filename . '.' . $type;
            }
            if ($type == 'gif') {
                $this->image->writeImages($this->path . $this->pathname . DIRECTORY_SEPARATOR . $this->filename, true);
            } else {
                $this->image->writeImage($this->path . $this->pathname . DIRECTORY_SEPARATOR . $this->filename);
            }
            return ['url' => $this->pathname . DIRECTORY_SEPARATOR . $this->filename];
        }

        $imageBlob = $this->image->getImageBlob();

        if (PHP_SAPI === 'cli') {
            return $imageBlob;
        }

        header('Content-Type:Image/' . $type);
        echo $imageBlob;
    }

    protected function getBlob($image)
    {
        if (in_array($this->getType(), ImageType::setQuantityTypes())) {
            $image->setImageCompressionQuality($this->getQuantity());
        }
        return $image->getImageBlob();
    }

    protected function getTmp($type, $image)
    {
        if (in_array($type, ImageType::setQuantityTypes())) {
            $image->setImageCompressionQuality($this->getQuantity());
        }
        $output = tempnam(sys_get_temp_dir(), uniqid('imagickImage'));
        if ($type == 'gif') {
            $image->writeImages($output, true);
        } else {
            $image->writeImage($output);
        }
        return $output;
    }

    protected function setImage($source)
    {

        if (strpos($source, 'http') === 0) {
            throw new PosterException("Unable to set the remote source {$source}");
        }

        if (in_array($this->getType(), ImageType::setQuantityTypes())) {
            $this->image->setImageCompressionQuality($this->getQuantity());
        }

        if (!empty($source)) {
            if ($this->getType() == 'gif') {
                return $this->image->writeImages($source, true);
            }
            return $this->image->writeImage($source);
        }

        throw new PosterException("Source not found {$source}");
    }

    /**
     * 创建文字绘画类
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 16:58
     * @return \ImagickDraw
     */
    protected function createTextImagickDraw()
    {
        if (empty($this->ImagickDraw)) {
            $this->ImagickDraw = new \ImagickDraw();
            $this->ImagickDraw->settextencoding('UTF-8');
        }
        return $this->ImagickDraw;
    }

    protected function createImagickDraw()
    {
        return new \ImagickDraw();
    }

    protected function createImagick($src = '')
    {
        $Imagick = new \Imagick();
        if ($src) {
            if ($src instanceof DriverInterface) {
                $Imagick->readImageBlob($src->getImageBlob());
            } elseif ($src instanceof ImagickImageGraphicsEngine) {
                $Imagick->destroy();
                $Imagick = $src->getImage();
            } elseif ($src instanceof GdImageGraphicsEngine) {
                $Imagick->readImageBlob($src->blob());
            } elseif (strpos($src, 'http') === 0) {
                $stream = @file_get_contents($src, null);
                if (empty($stream)) {
                    throw new PosterException('Image resources cannot be empty (' . $src . ')');
                }
                $Imagick->readImageBlob($stream);
            } elseif (is_file_path($src)) {
                if (!file_exists($src)) {
                    throw new PosterException('Image resources not found (' . $src . ')');
                }
                $Imagick->readImage(get_real_path($src));
            } else {
                $Imagick->readImageBlob($src);
            }

        }
        return $Imagick;
    }

    /**
     * 获取颜色值，可设置透明度
     */
    protected function createColorAlpha($rgba = [255, 255, 255, 127])
    {

        if (empty($rgba)) {
            $rgba = [255, 255, 255, 127];
        }
        if (count($rgba) != 4) {
            throw new PosterException('The length of the rgba parameter is 4');
        }
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
     * 获取颜色值
     */
    protected function createColor($rgba = [255, 255, 255])
    {
        if (empty($rgba)) {
            $rgba = [255, 255, 255];
        }
        if (!is_array($rgba)) {
            $rgba = parse_color($rgba);
        }
        if (isset($rgba[3]) && !is_null($rgba[3])) {
            $rgba[3] = round(floor((128 - $rgba[3]) / 127 * 100) / 100, 2);
            return new \ImagickPixel("rgba($rgba[0], $rgba[1], $rgba[2], $rgba[3])");
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
    protected function cropImage(\Imagick $source, $src_x, $src_y)
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
     * @param          $rgbaColor
     * @param          $rgbaCount
     * @param          $to
     * @param          $w
     * @param          $h
     * @throws \ImagickException
     */
    protected function calcColorDirection(\Imagick $source, $rgbaColor, $rgbaCount, $to, $w, $h)
    {

        switch ($to) {
            case '':
            case 'bottom':
                break;
            case 'top':
                $rgbaColor = array_reverse($rgbaColor);
                break;
            case 'left':
                break;
            case 'right':
                break;
            case 'right bottom':
            case 'bottom right':
                break;
            case 'right top':
            case 'top right':
                break;
            case 'left bottom':
            case 'bottom left':
                break;
            case 'left top':
            case 'top left':
                break;
            default:
                // code...
                break;
        }

        if ($rgbaCount < 3) {
            $this->linearGradientDefault($source, $rgbaColor, $rgbaCount, $w, $h);
        } else {

            $picKey = 0;
            $chunk = ceil($h / ($rgbaCount - 1));
            foreach ($rgbaColor as $k => $v) {

                if ($k == $rgbaCount - 1) {
                    break;
                }

                $picsC = $this->createCanvas($w, $chunk);

                $this->linearGradientDefault($picsC, [$rgbaColor[$k], $rgbaColor[$k + 1]], $rgbaCount, $w, $chunk);

                $source->compositeImage($picsC, ($this->image)::COMPOSITE_DEFAULT, 0, $k * $chunk);

                $picKey++;
            }
        }
    }

    protected function linearGradientDefault(\Imagick $source, $rgbaColor, $rgbaCount, $w, $h)
    {
        if ($rgbaCount == 1) {
            $rgb1 = "rgb(" . $rgbaColor[0][0] . "," . $rgbaColor[0][1] . "," . $rgbaColor[0][2] . ")";
            $source->newPseudoImage($w, $h, "gradient:$rgb1-$rgb1");
        } elseif ($rgbaCount > 1) {
            $rgb1 = "rgb(" . $rgbaColor[0][0] . "," . $rgbaColor[0][1] . "," . $rgbaColor[0][2] . ")";
            $rgb2 = "rgb(" . $rgbaColor[1][0] . "," . $rgbaColor[1][1] . "," . $rgbaColor[1][2] . ")";
            $source->newPseudoImage($w, $h, "gradient:$rgb1-$rgb2");
        }
    }

    protected function fontWeight($draw, $weight, $fontSize, $angle, $DstX, $DstY, $contents)
    {
        for ($i = 0; $i < $weight; $i++) {

            list($really_dst_x, $really_dst_y) = calc_font_weight($i, $weight, $fontSize, $DstX, $DstY);

            if ($this->getType() == 'gif') {
                foreach ($this->image as $frame) {
                    $frame->annotateImage($draw, $really_dst_x, $really_dst_y, $angle, $contents);
                    // $this->image->nextImage();
                }
            } else {
                $this->image->annotateImage($draw, $really_dst_x, $really_dst_y, $angle, $contents);
            }
        }
    }

    protected function fontWeightArr($draw, $weight, $fontSize, $angle, $DstX, $DstY, $contentsArr, $color)
    {
        $DstX_old = $DstX;
        foreach ($contentsArr as $v) {

            $contents = $v['value'];

            if ($contents == "\n") {
                $DstX = $DstX_old;
                $DstY += 1.75 * $fontSize;
                continue;
            }

            if (!empty($v['color'])) {
                $draw->setFillColor($v['color']);
            } else {
                $draw->setFillColor($color);
            }

            $this->fontWeight($draw, $weight, $fontSize, $angle, $DstX, $DstY, $contents);

            $DstX += $v['w'];
        }
    }

    /**
     * 画圆角
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/5/19
     * Time: 15:34
     * @param \Imagick $pic
     * @param          $w      double 宽
     * @param          $h      double 高
     * @param          $radius int 圆角
     * @return mixed
     * @throws PosterException
     * @throws \ImagickDrawException
     * @throws \ImagickException
     */
    protected function setPixelRadius($pic, $w, $h, $radius)
    {
        // 圆角处理
        $maxRadius = $w > $h ? $h / 2 : $w / 2;
        list($leftTopRadius, $rightTopRadius, $leftBottomRadius, $rightBottomRadius) = poster_radius_type($radius,
            $maxRadius);
        // 四个角一样
        $mask = $this->createImagick();
        $mask->newImage($w, $h, $this->createColor([255, 255, 255, 127]));
        $draw = $this->createImagickDraw();
        $draw->setFillColor($this->createColor());

        if ($leftTopRadius == $rightTopRadius && $leftTopRadius == $leftBottomRadius && $leftTopRadius == $rightBottomRadius) {
            // 搞一个长方形
            $draw->roundRectangle(0, 0, $w, $h, $leftTopRadius, $leftTopRadius);
        } else {
            // 中间两个长方形填满
            $draw->rectangle(max($leftTopRadius, $leftBottomRadius) * 2, 0,
                $w - max($rightTopRadius, $rightBottomRadius) * 2, $h);
            $draw->rectangle(0, max($leftTopRadius, $rightTopRadius) * 2, $w,
                $h - max($leftBottomRadius, $rightBottomRadius) * 2);

            // 左上角为 圆角
            $draw->rectangle(0, $leftTopRadius, max($leftTopRadius, $leftBottomRadius) * 2,
                max($leftTopRadius, $rightTopRadius) * 2);
            $draw->rectangle($leftTopRadius, 0, max($leftTopRadius, $leftBottomRadius) * 2,
                max($leftTopRadius, $rightTopRadius) * 2);
            $draw->ellipse($leftTopRadius, $leftTopRadius, $leftTopRadius, $leftTopRadius, 0, 360);

            // 右上角为 圆角
            $draw->rectangle($w - max($rightTopRadius, $rightBottomRadius) * 2, 0, $w - $rightTopRadius,
                max($leftTopRadius, $rightTopRadius) * 2);
            $draw->rectangle($w - max($rightTopRadius, $rightBottomRadius) * 2, $rightTopRadius, $w,
                max($leftTopRadius, $rightTopRadius) * 2);
            $draw->ellipse($w - $rightTopRadius, $rightTopRadius, $rightTopRadius, $rightTopRadius, 0, 360);

            // 右下角为 圆角
            $draw->rectangle($w - max($rightBottomRadius, $rightTopRadius) * 2,
                $h - max($rightBottomRadius, $leftBottomRadius) * 2, $w, $h - $rightBottomRadius);
            $draw->rectangle($w - max($rightBottomRadius, $rightTopRadius) * 2,
                $h - max($rightBottomRadius, $leftBottomRadius) * 2, $w - $rightBottomRadius, $h);
            $draw->ellipse($w - $rightBottomRadius, $h - $rightBottomRadius, $rightBottomRadius, $rightBottomRadius, 0,
                360);

            // 左下角为 圆角
            $draw->rectangle(0, $h - max($rightBottomRadius, $leftBottomRadius) * 2,
                max($leftTopRadius, $leftBottomRadius) * 2, $h - $leftBottomRadius);
            $draw->rectangle($leftBottomRadius, $h - max($rightBottomRadius, $leftBottomRadius) * 2,
                max($leftTopRadius, $leftBottomRadius) * 2, $h);
            $draw->ellipse($leftBottomRadius, $h - $leftBottomRadius, $leftBottomRadius, $leftBottomRadius, 0, 360);
        }

        $mask->drawImage($draw);
        $pic->compositeImage($mask, ($this->image)::COMPOSITE_DSTIN, 0, 0);

        return $pic;
    }

    /**
     * 设置透明度
     * User: lang
     * Date: 2023/9/22
     * Time: 11:00
     * @param $pic
     * @param $alphas
     * @return void
     */
    protected function setImageAlpha($pic, $transparency)
    {
        if (method_exists($pic, 'setImageOpacity')) {
            $pic->setImageOpacity(floor((128 - $transparency) / 127 * 100) / 100); // 透明度
        } elseif (method_exists($pic, 'setImageAlpha')) {
            $pic->setImageAlpha(floor((128 - $transparency) / 127 * 100) / 100); // 透明度
        }
    }

    protected function cropHandle(\Imagick $image, $x = 0, $y = 0, $width = 0, $height = 0)
    {
        return $image->cropImage($width, $height, $x, $y);
    }

    public function destroyImage($Imagick = null)
    {
        if (is_null($Imagick)) {
            $Imagick = $this->image;
        }
        empty($Imagick) || $Imagick->destroy();
    }

    protected function createCanvas($w, $h, $rgba = [255, 255, 255, 127])
    {
        if (!is_null($rgba)) {
            $rgba = empty($rgba) ? [255, 255, 255, 127] : $rgba;
            $rgba = parse_color($rgba);
        }
        $background = $this->createColor($rgba);
        $image = $this->createImagick();
        $image->newImage($w, $h, $background, $this->getType());//设置画布的信息以及画布的格式
        return $image;
    }

    public function calculateTextBox($text, $fontSize, $font, $angle)
    {
        $draw = $this->createTextImagickDraw();
        $draw->setFont($font);
        $draw->setFontSize($fontSize);
        $rect = $this->getCanvas()->getImage()->queryFontMetrics($draw, $text);
        return [
            "left"   => abs($rect['boundingBox']['x1']) - 1,
            "top"    => abs($rect['boundingBox']['y1']) - 1,
            "width"  => $rect['textWidth'],
            "height" => $rect['textHeight'] + abs($rect['descender']),
            "box"    => $rect
        ];
    }

    public function textWidth($text, $fontSize, $font, $angle = 0)
    {
        $calculateTextBox = $this->calculateTextBox($text, $fontSize, $font, $angle);
        return $calculateTextBox['width'];
    }

    public function textHeight($text, $fontSize, $font, $angle = 0)
    {
        $calculateTextBox = $this->calculateTextBox($text, $fontSize, $font ?: $this->font, $angle);
        return $calculateTextBox['height'];
    }
}