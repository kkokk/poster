<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:31
 */

namespace Kkokk\Poster\Image\Drivers;

use Kkokk\Poster\Common\Common;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImagickDriver extends Driver implements DriverInterface
{
    use ImagickTrait;

    protected $ImagickDraw;

    function __construct()
    {

    }

    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        $this->setDPI();
        return $this->returnImage($this->type);
    }

    public function getStream()
    {
        $this->setDPI();
        return $this->returnImage($this->type, false);
    }

    public function getBaseData()
    {
        $this->setDPI();
        $common = new Common();
        return $common->baseData($this->im->getImageBlob(), $this->type);
    }

    public function getIm()
    {
        return $this->im;
    }

    public function blob()
    {
        $this->setDPI();
        return $this->getBlob($this->im);
    }

    public function tmp()
    {
        return $this->getTmp($this->type, $this->im);
    }

    public function setData()
    {
        $this->setDPI();
        return $this->setImage($this->source);
    }

    public function im($w, $h, $rgba = [255, 255, 255, 1], $alpha = false)
    {
        $this->im_w = $w;
        $this->im_h = $h;
        $this->im = $this->createIm($w, $h, $rgba, $alpha);
    }

    public function ImDst($source, $w = 0, $h = 0)
    {
        $this->source = $source;

        $pic = $this->createImagick($source);

        $bgWidth = $pic->getImageWidth();
        $bgHeight = $pic->getImageHeight();
        $this->type = strtolower($pic->getImageFormat());

        if (!empty($w) && !empty($h)) {
            $this->im_w = $w;
            $this->im_h = $h;
            $pic->resizeImage($w, $h, $pic::FILTER_LANCZOS, 1, true);
        } else {
            $this->im_w = $bgWidth;
            $this->im_h = $bgHeight;
        }

        $this->im = $pic;
    }

    public function Bg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $query = [])
    {
        // 判断颜色是否渐变
        $rgbaColor = isset($rgba['color']) ? $rgba['color'] : [[0, 0, 0]];
        $alphas = isset($rgba['alpha']) ? $rgba['alpha'] : 1;
        $to = isset($rgba['to']) ? $rgba['to'] : 'bottom';
        $radius = isset($rgba['radius']) ? $rgba['radius'] : 0;
        $contentAlpha = isset($rgba['content_alpha']) ? $rgba['content_alpha'] : false;
        $rgbaCount = count($rgbaColor);

        // im不存在则创建
        if (empty($this->im)) {
            $this->im($w, $h, [], $alpha);
        }
        // 渐变处理->直接处理im
        // 计算颜色方向
        $pic = $this->createIm($w, $h, [], $alpha);
        $this->calcColorDirection($pic, $rgbaColor, $rgbaCount, $to, $w, $h);

        // 设置透明度，内容不透明
        if ($alpha && !$contentAlpha) {
            $this->setImageAlpha($pic, $alphas);
        }

        $dst_x = $this->calcDstX($dst_x, $this->im_w, $w);
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $h);

        if (!empty($query)) {
            $that = clone $this;
            $that->im = $pic;
            $that->im_w = $w;
            $that->im_h = $h;
            $that->execute($query, $that);

            // 合并图片, 合并图片移到下方，这里不需要再合并
            // $pic->compositeImage($that->im, ($that->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
        }

        // 设置透明度，内容也透明
        if ($alpha && $contentAlpha) {
            $this->setImageAlpha($pic, $alphas);
        }

        if ($radius) {
            // 圆角处理
            $pic = $this->setPixelRadius($pic, $w, $h, $radius);
        }

        // 裁剪图片
        $this->cropImage($pic, $src_x, $src_y);

        // 合并图片
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->im as $frame) {
                $frame->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
            }
        } else {
            $this->im->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
        }

        if ($that) {
            unset($that);
        }
        $this->destroyImage($pic);
    }

    public function CopyImage(
        $src,
        $dst_x = 0,
        $dst_y = 0,
        $src_x = 0,
        $src_y = 0,
        $src_w = 0,
        $src_h = 0,
        $alpha = false,
        $type = 'normal'
    ) {
        $angle = 0;
        if (empty($this->im)) {
            throw new PosterException('im resources not be found');
        }

        if (is_array($src)) {
            $angle = isset($src['angle']) ? $src['angle'] : 0;
            $src = isset($src['src']) ? $src['src'] : '';
            if (empty($src)) {
                throw new PosterException('image resources cannot be empty (' . $src . ')');
            }
        }

        $pic = $this->createImagick($src);

        $Width = $pic->getImageWidth();
        $Height = $pic->getImageHeight();

        $bgWidth = !empty($src_w) ? $src_w : $Width;
        $bgHeight = !empty($src_h) ? $src_h : $Height;

        switch ($type) {
            case 'normal':
                # 自定义宽高的时候
                if (!empty($src_w) && !empty($src_h)) {
                    // $pic->resizeImage($bgWidth, $bgHeight, $pic::FILTER_LANCZOS, 1, true); // 等比缩放
                    $pic->scaleImage($bgWidth, $bgHeight);
                }
                break;
            case 'circle':
                if (!empty($src_w) && !empty($src_h)) {
                    // $pic->resizeImage($bgWidth, $bgHeight, $pic::FILTER_LANCZOS, 1, true); // 等比缩放
                    $pic->scaleImage($bgWidth, $bgHeight);
                }

                $pic->setImageFormat("png");
                $pic->setImageMatte(true); // 激活遮罩通道

                // 创建一个圆形遮罩图片

                $mask = $this->createImagick();

                $mask->newImage($bgWidth, $bgHeight, $this->createColorAlpha([255, 255, 255, 127]));

                $circle = $this->createImagickDraw();
                $circle->setFillColor($this->createColorAlpha([255, 255, 255, 1]));
                $circle->circle($bgWidth / 2, $bgHeight / 2, $bgWidth / 2, $bgHeight);

                $mask->drawImage($circle);

                // 合并原始图片和圆形遮罩图片
                $pic->compositeImage($mask, $pic::COMPOSITE_DSTIN, 0, 0);

                $this->destroyImage($circle);
                $this->destroyImage($mask);
                break;
            default:
                # code...
                break;
        }

        # 处理目标 x 轴
        $dst_x = $this->calcDstX($dst_x, $this->im_w, $bgWidth);

        # 处理目标 y 轴
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $bgHeight);

        // 裁剪图片
        $this->cropImage($pic, $src_x, $src_y);


        # 处理旋转
        if ($angle > 0) {
            $pic->rotateimage($this->createColorAlpha(), $angle);
        }


        // 合并图片
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->im as $frame) {
                $frame->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
            }
        } else {
            $this->im->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
        }


        $this->destroyImage($pic);
    }

    public function CopyText(
        $content,
        $dst_x = 0,
        $dst_y = 0,
        $fontSize = null,
        $rgba = null,
        $max_w = null,
        $font = null,
        $weight = null,
        $space = null,
        $angle = null
    ) {
        if ($content == '') {
            return true;
        }

        if (empty($this->im)) {
            throw new PosterException('im resources not be found');
        }

        $fontSize = $fontSize ?: $this->font_size;
        $rgba = $rgba ?: $this->font_rgba;
        $max_w = $max_w ?: $this->font_max_w;
        $weight = $weight ?: $this->font_weight;
        $space = $space ?: $this->font_space;
        $angle = $angle ?: $this->font_angle;

        if (!empty($font)) {
            $font = $this->getRealRoute($font);
        } else {
            $font = $this->font;
        }

        $calcSpace = $space > $fontSize ? ($space - $fontSize) : 0; // 获取间距计算值

        $color = $this->createColorAlpha($rgba);

        $max_ws = $this->im_w;
        if (isset($max_w) && !empty($max_w)) {
            $max_ws = $max_w;
        }

        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $contents = '';
        $letter = [];
        $line = 1;
        $calcSpaceRes = 0;

        $draw = $this->createTextImagickDraw();
        $draw->setFont($font);
        $draw->setFillColor($color);
        $draw->setFontSize($fontSize);

        $fontSize = ($fontSize * 3) / 4; // 使和gd一致

        // 主动设置是否解析html标签
        if (is_array($content)) {

            if (!isset($content['type'])) {
                throw new PosterException('type is required');
            }
            if (!isset($content['content'])) {
                throw new PosterException('content is required');
            }

            $type = $content['type'];
            $content = $content['content'];

            // 确认包含才处理
            if ($type == 'html' && preg_match('/<[^>]*>/', $content)) {

                // 正则匹配 span 属性
                $pattern = '/<span style="(.*?)">(.*?)<\/span>/i';

                // 分割字符串
                $matches = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

                $common = new Common();

                for ($i = 0; $i < count($matches); $i += 3) {
                    if (!empty($matches[$i])) {
                        $this->getNodeValue($letter, $matches[$i], $color);
                    }

                    if (isset($matches[$i + 1])) {
                        $style = $matches[$i + 1];
                        $colorValue = $this->getStyleAttr($style);
                        $colorCustom = $this->createColorAlpha($common->getNodeStyleColor($colorValue));
                        $this->getNodeValue($letter, $matches[$i + 2], $colorCustom);
                    }
                }

            } else {
                $this->getNodeValue($letter, $content, $color);
            }

            $textWidthArr = [];
            foreach ($letter as $l) {

                $textStr = $contents . $l['value'];
                $fontBox = $this->im->queryFontMetrics($draw, $textStr);
                $textWidth = abs($fontBox['textWidth'] + $fontBox['descender']) + $calcSpaceRes;

                if ($l['value'] == "\n") {
                    $contents = "";
                    $contentsArr[] = $this->getLetterArr();
                    $line++;
                    continue;
                }

                if (!isset($textWidthArr[$line])) {
                    $textWidthArr[$line] = -$space / 2;
                }
                // 判断拼接后的字符串是否超过预设的宽度
                if (($textWidth > $max_ws || $textWidthArr[$line] > $max_ws) && ($contents !== '')) {
                    $contents .= "\n";
                    $contentsArr[] = $this->getLetterArr();
                    $line++;
                }
                $contents .= $l['value'];

                $fontBox1 = $this->im->queryFontMetrics($draw, $l['value']);
                $l['w'] = abs($fontBox1['textWidth'] + $fontBox1['descender']) + $calcSpace;
                $textWidthArr[$line] += $l['w'];
                $contentsArr[] = $l;

                $line === 1 && $calcSpaceRes += $calcSpace;
            }

            $calcFont = [
                'text_width'  => max(array_values($textWidthArr)), // 取最宽行宽
                'text_height' => abs($fontBox[1] - $fontBox[7]),
            ];
            $dst_x = $this->calcTextDstX($dst_x, $calcFont);

            $dst_y = $this->calcTextDstY($dst_y, $calcFont);

            # 自定义间距
            $this->fontWeightArr($draw, $weight, $fontSize, $angle, $dst_x - 5, $dst_y, $contentsArr, $color);

            return true;

        } else {
            // 将字符串拆分成一个个单字 保存到数组 letter 中
            for ($i = 0; $i < mb_strlen($content); $i++) {
                $letter[] = mb_substr($content, $i, 1);
            }

            foreach ($letter as $l) {
                $textStr = $contents . $l;
                $fontBox = $this->im->queryFontMetrics($draw, $textStr);
                $textWidth = abs($fontBox['textWidth'] + $fontBox['descender']) + $calcSpaceRes;
                // 判断拼接后的字符串是否超过预设的宽度
                if (($textWidth > $max_ws) && ($contents !== '')) {
                    $contents .= "\n";
                    $line++;
                }
                $contents .= $l;
                $line === 1 && $calcSpaceRes += $calcSpace;
            }

            $calcFont = [
                'text_width'  => $textWidth,
                'text_height' => abs($fontBox['textHeight'] + $fontBox['descender']),
            ];
            $dst_x = $this->calcTextDstX($dst_x, $calcFont) - $fontBox['descender']; // 调整和 gd 的误差值

            $dst_y = $this->calcTextDstY($dst_y, $calcFont);

            # 自定义间距
            if ($space > 0) {
                $dst_x_old = $dst_x;
                for ($j = 0; $j < mb_strlen($contents); $j++) {
                    $spaceStr = mb_substr($contents, $j, 1);
                    if ($spaceStr == "\n") {
                        $dst_x = $dst_x_old;
                        $dst_y += 1.75 * $fontSize;
                        continue;
                    }
                    $this->fontWeight($draw, $weight, $fontSize, $angle, $dst_x - 5, $dst_y, $spaceStr);
                    $dst_x += $space;
                }

            } else {
                $this->fontWeight($draw, $weight, $fontSize, $angle, $dst_x - 5, $dst_y, $contents);
            }
        }
    }

    public function CopyLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        $color = $this->createColorAlpha($rgba);
        $draw = $this->createImagickDraw();
        $draw->setStrokeColor($color);
        $draw->setStrokeWidth($weight);
        switch ($type) {
            case 'rectangle':
                $draw->setFillColor($this->createColorAlpha());
                $draw->rectangle($x1, $y1, $x2, $y2);
                break;
            case 'filled_rectangle':
            case 'filledRectangle':
                $draw->rectangle($x1, $y1, $x2, $y2);
                break;
            default:
                $draw->line($x1, $y1, $x2, $y2);
                break;
        }

        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->im as $frame) {
                $frame->drawImage($draw);
            }
        } else {
            $this->im->drawImage($draw);
        }
    }

    public function CopyArc(
        $cx = 0,
        $cy = 0,
        $w = 0,
        $h = 0,
        $s = 0,
        $e = 0,
        $rgba = [],
        $type = '',
        $style = '',
        $weight = 1
    ) {
        $color = $this->createColorAlpha($rgba);
        $draw = $this->createImagickDraw();
        $draw->setStrokeColor($color);
        $draw->setStrokeWidth($weight);
        $wr = $w / 2;
        $hr = $h / 2;
        switch ($type) {
            case 'filled_arc':
            case 'filledArc':
                $draw->arc($cx - $wr, $cy - $hr, $cx + $wr, $cy + $hr, $s, $e);
                break;
            default:
                $draw->setFillColor($this->createColorAlpha());
                $draw->arc($cx - $wr, $cy - $hr, $cx + $wr, $cy + $hr, $s, $e);
                break;
        }
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->im as $frame) {
                $frame->drawImage($draw);
            }
        } else {
            $this->im->drawImage($draw);
        }
    }

    public function CopyQr(
        $text,
        $level = 'L',
        $size = 4,
        $margin = 1,
        $dst_x = 0,
        $dst_y = 0,
        $src_x = 0,
        $src_y = 0,
        $src_w = 0,
        $src_h = 0
    ) {
        if (empty($this->im)) {
            throw new PosterException('im resources not be found');
        }

        $qr = \QRcode::re_png($text, $level, $size, $margin);

        $bgWidth = imagesx($qr);
        $bgHeight = imagesy($qr);

        ob_start();                     // 打开一个输出缓冲区
        $this->poster_type['png']($qr); // 将 GD 图像输出到缓冲区
        $imageData = ob_get_contents(); // 从缓冲区中读取图像数据
        ob_end_clean();

        $pic = $this->createImagick();
        $pic->readImageBlob($imageData);

        if ($src_w > 0) {
            $bgWidth = $src_w;
        }

        if ($src_h > 0) {
            $bgHeight = $src_h;
        }

        # 处理目标 x 轴
        $dst_x = $this->calcDstX($dst_x, $this->im_w, $bgWidth);

        # 处理目标 y 轴
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $bgHeight);

        # 自定义宽高的时候
        if (!empty($src_w) && !empty($src_h)) {
            $pic->resizeImage($bgWidth, $bgHeight, $pic::FILTER_LANCZOS, 1, true);
        }

        // 裁剪图片
        $this->cropImage($pic, $src_x, $src_y);
        // 合并图片
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->im as $frame) {
                $frame->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
            }
        } else {
            $this->im->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
        }

        !is_resource($qr) || imagedestroy($qr);
        $this->destroyImage($pic);
    }

    /**
     * 裁剪
     * Author: lang
     * Date: 2024/3/12
     * Time: 11:22
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     */
    public function crop($x = 0, $y = 0, $width = 0, $height = 0)
    {
        $this->im->cropImage($width, $height, $x, $y);
        $this->im_w = $width;
        $this->im_h = $height;
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

    public function destroyImage($Imagick)
    {
        empty($Imagick) || $Imagick->destroy();
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->im) || $this->im->destroy();
        empty($this->ImagickDraw) || $this->ImagickDraw->destroy();
    }
}