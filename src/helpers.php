<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 17:24
 */

use Kkokk\Poster\Exception\PosterException;

define('POSTER_BASE_PATH', dirname(__FILE__));

if (!function_exists('gd_image_create')) {
    function gd_image_create($type)
    {
        $imageType = [
            'gif'  => 'imagegif',
            'jpeg' => 'imagejpeg',
            'jpg'  => 'imagejpeg',
            'png'  => 'imagepng',
            'wbmp' => 'imagewbmp'
        ];

        if (!isset($imageType[$type])) {
            throw new PosterException('The image type is not supported');
        }

        return $imageType[$type];
    }
}

if (!function_exists('poster_base_path')) {
    function poster_base_path()
    {
        return dirname(__FILE__);
    }
}


if (!function_exists('parse_color')) {
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
    function base64_data($image, $type = 'png')
    {
        $baseData = '';
        if (is_resource($image) || is_object($image)) {
            ob_start();
            gd_image_create($type)($image);
            $data = ob_get_contents();
            ob_end_clean();
            $baseData = 'data:image/' . $type . ';base64,' . base64_encode($data);
            imagedestroy($image);
        } elseif (is_string($image)) {
            $baseData = 'data:image/' . $type . ';base64,' . base64_encode($image);
        }
        return $baseData;
    }
}
if (!function_exists('image_out_put')) {
    /**
     * 输出图片
     * User: lang
     * Date: 2024/11/28
     * Time: 10:19
     * @param $im
     * @param $dir
     * @param $type
     * @param $quality
     * @return true
     */
    function image_out_put($im, $dir = '', $type = 'png', $quality = 75)
    {
        if ($type == 'jpg' || $type == 'jpeg') {
            gd_image_create($type)($im, $dir, $quality);
        } else {
            gd_image_create($type)($im, $dir);
        }

        return true;
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
    function is_file_path($path)
    {
        $pattern = '~^(?:[a-zA-Z]:[/\\\]+(?:[^\\\/:*?"<>|\r\n]+[/\\\]*)*[^\\\/:*?"<>|\r\n]*|/(?:[^\\\/:*?"<>|\r\n]+[/\\\]*)*[^\\\/:*?"<>|\r\n]*)~';
        return strpos($path, 'phar://') === 0 || preg_match($pattern, $path) === 1;
    }
}