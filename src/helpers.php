<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 17:24
 */

use Kkokk\Poster\Exception\PosterException;

define('POSTER_BASE_PATH', dirname(__FILE__));

if (!function_exists('ll')) {
    function ll(...$values)
    {
        echo "<pre>";
        foreach ($values as $value) {
            print_r($value);
            echo PHP_EOL;
        }
        exit;
    }
}

if (!function_exists('gd_image_save')) {
    /**
     * 获取 GD 图片创建函数
     * User: lang
     * Date: 2024/12/2
     * Time: 11:34
     * @param $type
     * @return string
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    function gd_image_save($image, $type, $outputPath = null, $quality = 100)
    {
        $imageType = \Kkokk\Poster\Image\Enums\ImageType::gdImageSaveFunctions();
        if (!isset($imageType[$type])) {
            throw new PosterException('The image type is not supported');
        }
        if (in_array($type, \Kkokk\Poster\Image\Enums\ImageType::setQuantityTypes())) {
            return $imageType[$type]($image, $outputPath, $quality);
        }
        return $imageType[$type]($image, $outputPath);
    }
}

if (!function_exists('poster_base_path')) {
    /**
     * 获取根目录
     * User: lang
     * Date: 2024/12/2
     * Time: 11:34
     * @return string
     */
    function poster_base_path()
    {
        return dirname(__FILE__);
    }
}

if (!function_exists('parse_color')) {
    /**
     * 颜色解析
     * User: lang
     * Date: 2024/12/2
     * Time: 11:34
     * @param $color
     * @return array
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    function parse_color($color)
    {
        if (is_array($color)) {
            $colorLength = count($color);
            if ($colorLength != 3 && $colorLength != 4) {
                throw new PosterException('The length of the color parameter is 3 or 4');
            }

            foreach ($color as $k => $value) {
                if (!is_int($color[$k])) {
                    throw new PosterException('The value must be an integer');
                } elseif ($k < 3 && ($color[$k] > 255 || $color[$k] < 0)) {
                    throw new PosterException('The color value is between 0-255');
                } elseif ($k == 3 && ($color[$k] > 127 || $color[$k] < 0)) {
                    throw new PosterException('The alpha value is between 0-127');
                }
            }
            return $colorLength == 4 ? $color : array_merge($color, [null]);
        }

        if (strpos($color, '#') !== false) {
            $rgba = sscanf($color, "#%02x%02x%02x%02x");
            if (!is_null($rgba[3])) {
                $rgba[3] = round($rgba[3] / 255 * 127);
            }
            return $rgba;
        }

        if (strpos($color, 'rgb') !== false) {
            $rgbPattern = '/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/i';
            $rgbaPattern = '/rgba\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*([\d.]+)\s*\)/i';
            if (preg_match($rgbPattern, $color, $matches)) {
                return [$matches[1], $matches[2], $matches[3], null];
            }
            if (preg_match($rgbaPattern, $color, $matches)) {
                return [$matches[1], $matches[2], $matches[3], round($matches[4] * 127)];
            }
        }

        throw new PosterException('The color format error');
    }
}

if (!function_exists('color_to_rgba')) {
    /**
     * 颜色数组或16进制 转换为 rgba
     * User: lang
     * Date: 2024/12/2
     * Time: 11:33
     * @param $color
     * @param $isArray
     * @return array|string
     */
    function color_to_rgba($color, $isArray = false)
    {
        if (is_array($color)) {
            $rgba = $color;
            $rgba[3] = round((128 - $rgba[3]) / 127, 2);
        } elseif (strpos($color, '#') !== false) {
            $rgba = sscanf($color, "#%02x%02x%02x%02x");
            if (!is_null($rgba[3])) {
                $rgba[3] = round($rgba[3] / 255, 2);
            }
        }
        return $isArray ? $rgba : "rgba($rgba[0], $rgba[1], $rgba[2], $rgba[3])";
    }
}

if (!function_exists('calc_dst_x')) {
    /**
     * 计算画布X轴位置
     * User: lang
     * Date: 2024/11/27
     * Time: 15:47
     * @param $dstX
     * @param $imageWidth
     * @param $bgWidth
     * @return float
     */
    function calc_dst_x($dstX, $imageWidth, $bgWidth)
    {
        if ($dstX == '0') {
            return $dstX;
        } elseif ($dstX === 'center') {
            $dstX = ceil(($imageWidth - $bgWidth) / 2);
        } elseif (is_numeric($dstX) && $dstX < 0) {
            $dstX = ceil($imageWidth + $dstX);
        } elseif (is_array($dstX)) {
            if ($dstX[0] == 'center') {
                $dstX = ceil(($imageWidth - $bgWidth) / 2) + $dstX[1];
            }
        } elseif (strpos($dstX, '%') !== false) {
            if (substr($dstX, 0, strpos($dstX, '%')) < 0) {
                $dstX = ceil($imageWidth + ($imageWidth * substr($dstX, 0, strpos($dstX, '%')) / 100));
            } else {
                $dstX = ceil($imageWidth * substr($dstX, 0, strpos($dstX, '%')) / 100);
            }
        }
        return $dstX;
    }
}

if (!function_exists('calc_dst_y')) {
    /**
     * 计算画布Y轴位置
     * User: lang
     * Date: 2024/11/27
     * Time: 15:48
     * @param $dstY
     * @param $imHeight
     * @param $bgHeight
     * @return false|float|mixed
     */
    function calc_dst_y($dstY, $imHeight, $bgHeight)
    {
        if ($dstY == '0') {
            return $dstY;
        } elseif ($dstY == 'center') {
            $dstY = ceil(($imHeight - $bgHeight) / 2);
        } elseif (is_numeric($dstY) && $dstY < 0) {
            $dstY = ceil($imHeight + $dstY);
        } elseif (is_array($dstY)) {
            if ($dstY[0] == 'center') {
                $dstY = ceil(($imHeight - $bgHeight) / 2) + $dstY[1];
            }
        } elseif (strpos($dstY, '%') !== false) {
            if (substr($dstY, 0, strpos($dstY, '%')) < 0) {
                $dstY = ceil($imHeight + (($imHeight * substr($dstY, 0, strpos($dstY, '%'))) / 100));
            } else {
                $dstY = ceil($imHeight * substr($dstY, 0, strpos($dstY, '%')) / 100);
            }
        }
        return $dstY;
    }
}

if (!function_exists('calc_text_dst_x')) {
    /**
     * 计算文字x轴坐标
     * User: lang
     * Date: 2024/12/2
     * Time: 9:48
     * @param $DstX
     * @param $calcFont
     * @param $x1
     * @param $x2
     * @return false|float|int|mixed
     */
    function calc_text_dst_x($DstX, $fontBox, $imageWidth, $x1 = null, $x2 = null)
    {
        $fontBoxWidth = $fontBox['max_width'];
        $currentImageWidth = ($x1 !== null && $x2 !== null) ?
            ($x2 - $x1)
            : $imageWidth;
        if ($DstX === 'center') {
            // 如果文字宽度大于 画布宽度 则为0
            $DstX = ceil(max(0, ($currentImageWidth - $fontBoxWidth)) / 2);
        } elseif (is_array($DstX)) {
            $DstX[1] = isset($DstX[1]) ? $DstX[1] : 0;
            $x1 = $x1 !== null ? $x1 : 0;
            switch ($DstX[0]) {
                case 'center':
                    $DstX = ceil(max(0, ($currentImageWidth - $fontBoxWidth)) / 2) + $x1 + $DstX[1];
                    break;
                case 'left': // 左对齐 且 左右偏移
                    $DstX = $x1 + $DstX[1];
                    break;
                case 'right': // 右对齐 且 左右偏移
                    $DstX = ceil(($currentImageWidth - $fontBoxWidth)) + $x1 + $DstX[1];
                    break;
                case 'custom': // 设置 自定义宽度居中 ['custom', 'center|top|bottom', $x1, $x2, $offset] $x1 区间起点宽度 $x2 区间终点宽度 $offset 偏移
                    $custom = [$DstX[1], isset($DstX[4]) ? $DstX[4] : 0];
                    $DstX = calc_text_dst_x($custom, $fontBox, $imageWidth, $DstX[2], $DstX[3]);
                    break;
                default:
                    $DstX = 0;
            }
        } elseif (strpos($DstX, '%') !== false) {
            if (substr($DstX, 0, strpos($DstX, '%')) < 0) {
                $DstX = ceil($currentImageWidth + ($currentImageWidth * substr($DstX, 0, strpos($DstX, '%')) / 100));
            } else {
                $DstX = ceil($currentImageWidth * substr($DstX, 0, strpos($DstX, '%')) / 100);
            }
        }
        return $DstX;
    }
}

if (!function_exists('calc_text_dst_y')) {
    /**
     * 计算文字y轴坐标
     * User: lang
     * Date: 2024/12/2
     * Time: 9:49
     * @param $DstY
     * @param $calcFont
     * @param $y1
     * @param $y2
     * @return false|float|int|mixed
     */
    function calc_text_dst_y($DstY, $fontBox, $imageHeight, $y1 = null, $y2 = null)
    {
        $fontBoxHeight = $fontBox['max_height']; // 文字加换行数的高度
        $currentImageHeight = ($y1 !== null && $y2 !== null) ?
            ($y2 - $y1)
            : $imageHeight;
        if ($DstY === 'center') {
            $DstY = ceil(max(0, ($currentImageHeight / 2) + ($fontBoxHeight / 2) - $fontBoxHeight));
        } elseif (is_array($DstY)) {
            $DstY[1] = isset($DstY[1]) ? $DstY[1] : 0;
            $y1 = $y1 !== null ? $y1 : 0;
            switch ($DstY[0]) {
                case 'center':
                    $DstY = ceil(max(0,
                            ($currentImageHeight / 2) + ($fontBoxHeight / 2) - $fontBoxHeight)) + $y1 + $DstY[1];
                    break;
                case 'top': // 顶对齐 且 上下偏移
                    $DstY = $y1 + $DstY[1];
                    break;
                case 'bottom': // 底对齐 且 上下偏移
                    $DstY = ceil(($currentImageHeight - $fontBoxHeight)) + $y1 + $DstY[1];
                    break;
                case 'custom': // 设置 自定义高度居中 ['custom', 'center|top|bottom', $y1, $y2, $offset] $y1 区间起点高度 $y2 区间终点高度 $offset 偏移
                    $custom = [$DstY[1], isset($DstY[4]) ? $DstY[4] : 0];
                    $DstY = calc_text_dst_y($custom, $fontBox, $imageHeight, $DstY[2], $DstY[3]);
                    break;
                default:
                    $DstY = 0;
            }
        } elseif (strpos($DstY, '%') !== false) {
            if (substr($DstY, 0, strpos($DstY, '%')) < 0) {
                $DstY = ceil($currentImageHeight + (($currentImageHeight * substr($DstY, 0,
                                strpos($DstY, '%'))) / 100));
            } else {
                $DstY = ceil($currentImageHeight * substr($DstY, 0, strpos($DstY, '%')) / 100);
            }
        }

        return $DstY;
    }
}

if (!function_exists('calc_font_weight')) {
    /**
     * 计算文字加粗的绘制坐标
     * User: lang
     * Date: 2024/12/2
     * Time: 11:32
     * @param $num
     * @param $weight
     * @param $fontSize
     * @param $DstX
     * @param $DstY
     * @return array|float[]
     */
    function calc_font_weight($num, $weight, $fontSize, $DstX, $DstY)
    {
        if ($weight % 2 == 0 && $num > 0) {
            $reallyDstX = $DstX + ($num * 0.1);
            $reallyDstY = $DstY;
        } elseif ($weight % 2 != 0 && $num > 0) {
            $reallyDstX = $DstX;
            $reallyDstY = $DstY + ($num * 0.1);
        } else {
            $reallyDstX = $DstX;
            $reallyDstY = $DstY;
        }
        return [$reallyDstX, $reallyDstY];
    }
}

if (!function_exists('is_absolute')) {
    /**
     * 判断是否是绝对路径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:54
     * @param $pathFileName
     * @return bool
     */
    function is_absolute($pathFileName)
    {
        // 区分WIN系统绝对路径、暂时只区分linux win mac
        switch (PHP_OS) {
            case 'Darwin':
                $absolute = stripos($pathFileName, DIRECTORY_SEPARATOR) === 0 ?: false;
                break;
            case 'linux':
            default:
                if (stripos(PHP_OS, 'WIN') !== false) {
                    $absolute = substr($pathFileName, 1, 1) === ':' ?: false;
                } else {
                    $absolute = stripos($pathFileName, DIRECTORY_SEPARATOR) === 0 ?: false;
                }
                break;
        }

        return $absolute;
    }
}

if (!function_exists('get_document_root')) {
    /**
     * 获取项目根目录
     * User: lang
     * Date: 2024/11/28
     * Time: 9:51
     * @return string
     */
    function get_document_root()
    {
        $documentRoot = iconv('UTF-8', 'GBK', $_SERVER['DOCUMENT_ROOT']);

        return $documentRoot ? $documentRoot . DIRECTORY_SEPARATOR : '';
    }
}

if (!function_exists('get_real_path')) {
    /**
     * 获取真实路径
     * User: lang
     * Date: 2024/11/28
     * Time: 9:47
     * @param $path
     * @return false|string
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    function get_real_path($path)
    {
        $isAbsolute = is_absolute($path);

        if (isCli() && !$isAbsolute) {
            throw new PosterException('For cli environment, please pass the absolute path');
        }

        // 检测是否运行在 Phar 环境中
        $isInPhar = Phar::running(false) !== '';

        if (!$isAbsolute) {
            if ($isInPhar) {
                $basePath = 'phar://' . Phar::running(false); // 获取运行中的 Phar 基础路径
                return $basePath . $path;
            } else {
                return get_document_root() . $path;
            }
        }

        // 绝对路径情况下
        if ($isInPhar && strpos($path, 'phar://') === 0) {
            // 如果是 Phar 的绝对路径，直接返回
            return $path;
        }

        // 非 Phar 环境或普通文件系统路径，使用 realpath
        return realpath($path);
    }
}

if (!function_exists('is_cli')) {
    /**
     * 是否是 cli 环境
     * User: lang
     * Date: 2024/11/28
     * Time: 9:46
     * @return bool
     */
    function isCli()
    {
        return php_sapi_name() === 'cli';
    }
}

if (!function_exists('dir_exists')) {
    /**
     * 检查文件是否存在并创建
     * User: lang
     * Date: 2024/11/28
     * Time: 9:46
     * @param $pathname
     * @return void
     */
    function dir_exists($pathname)
    {
        if (!is_dir($pathname)) {
            mkdir($pathname, 0777, true);
        }
    }
}

if (!function_exists('base64_data')) {
    /**
     * base64 数据
     * User: lang
     * Date: 2024/12/2
     * Time: 11:31
     * @param $image
     * @param $type
     * @return string
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    function base64_data($image, $type = 'png')
    {
        $baseData = '';
        if (is_resource($image) || is_object($image)) {
            ob_start();
            gd_image_save($image, $type);
            $data = ob_get_contents();
            ob_end_clean();
            $baseData = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } elseif (is_string($image)) {
            $baseData = 'data:image/' . $type . ';base64,' . base64_encode($image);
        }
        return $baseData;
    }
}

if (!function_exists('cross_product')) {
    /**
     * 计算 三个点的叉乘 |p1 p2| X |p1 p|
     * User: lang
     * Date: 2024/11/28
     * Time: 10:23
     * @param $p1
     * @param $p2
     * @param $p
     * @return float|int
     */
    function cross_product($p1, $p2, $p)
    {
        // 公式 (p2.x - p1.x) * (p.y - p1.y) -(p.x - p1.x) * (p2.y - p1.y);
        return ($p1[0] - $p[0]) * ($p2[1] - $p[1]) - ($p2[0] - $p[0]) * ($p1[1] - $p[1]);
    }
}

if (!function_exists('is_file_path')) {
    /**
     * 判断是否是文件路径
     * User: lang
     * Date: 2024/12/2
     * Time: 11:30
     * @param $path
     * @return bool
     */
    function is_file_path($path)
    {
        $pattern = '~^(?:[a-zA-Z]:[/\\\]+(?:[^\\\/:*?"<>|\r\n]+[/\\\]*)*[^\\\/:*?"<>|\r\n]*|/(?:[^\\\/:*?"<>|\r\n]+[/\\\]*)*[^\\\/:*?"<>|\r\n]*)~';
        return strpos($path, 'phar://') === 0 || preg_match($pattern, $path) === 1;
    }
}

if (!function_exists('poster_radius_type')) {
    /**
     * 圆角类型
     * User: lang
     * Date: 2024/12/2
     * Time: 11:26
     * @param $radius
     * @param $maxRadius
     * @return float[]
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    function poster_radius_type($radius, $maxRadius)
    {
        if (is_string($radius)) {
            // 把字符串格式转数组
            $radius = preg_replace('~\s+~', ' ', trim($radius, ' '));
            $radius = explode(' ', $radius);
        } elseif (is_numeric($radius)) {
            // 整形转数组
            $radius = [$radius, $radius, $radius, $radius];
        } else {
            if (!is_array($radius)) {
                throw new PosterException('圆角参数类型错误');
            }
        }
        // [20] 四个角
        // [20,30] 第一个值 左上 右下 第二个值 右上 左下
        // [20,30,20] 第一个值 左上 第二个值 右上 左下 第三个值 右下
        // [20,30,20,10]  左上 右上 右下  左下
        $radiusCount = count($radius);
        if ($radiusCount == 1) {
            $leftTopRadius = poster_max_radius($maxRadius, $radius[0]);
            $rightTopRadius = poster_max_radius($maxRadius, $radius[0]);
            $leftBottomRadius = poster_max_radius($maxRadius, $radius[0]);
            $rightBottomRadius = poster_max_radius($maxRadius, $radius[0]);
        } elseif ($radiusCount == 2) {
            $leftTopRadius = poster_max_radius($maxRadius, $radius[0]);
            $rightBottomRadius = poster_max_radius($maxRadius, $radius[0]);
            $rightTopRadius = poster_max_radius($maxRadius, $radius[1]);
            $leftBottomRadius = poster_max_radius($maxRadius, $radius[1]);
        } elseif ($radiusCount == 3) {
            $leftTopRadius = poster_max_radius($maxRadius, $radius[0]);
            $rightTopRadius = poster_max_radius($maxRadius, $radius[1]);
            $leftBottomRadius = poster_max_radius($maxRadius, $radius[1]);
            $rightBottomRadius = poster_max_radius($maxRadius, $radius[2]);
        } else {
            $leftTopRadius = poster_max_radius($maxRadius, $radius[0]);
            $rightTopRadius = poster_max_radius($maxRadius, $radius[1]);
            $leftBottomRadius = poster_max_radius($maxRadius, $radius[2]);
            $rightBottomRadius = poster_max_radius($maxRadius, $radius[3]);
        }

        return [$leftTopRadius, $rightTopRadius, $leftBottomRadius, $rightBottomRadius];
    }
}

if (!function_exists('poster_max_radius')) {
    /**
     * 圆角最大值
     * User: lang
     * Date: 2024/12/2
     * Time: 11:26
     * @param $maxRadius
     * @param $radius
     * @return float
     */
    function poster_max_radius($maxRadius, $radius)
    {
        return $radius < $maxRadius ? floor($radius) : floor($maxRadius);
    }
}