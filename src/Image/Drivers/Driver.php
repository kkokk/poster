<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:28
 */

namespace Kkokk\Poster\Image\Drivers;
require_once(__DIR__ . '/../../PHPQrcode/phpqrcode.php');

use Kkokk\Poster\Exception\PosterException;

class Driver
{
    /** @var resource 画布 */
    protected $im;

    /** @var resource 资源文件 */
    protected $source;

    /** @var int 画布尺寸宽 */
    protected $im_w;

    /** @var int 画布尺寸高 */
    protected $im_h;

    /** @var int[] 默认 x y 分辨率 默认是 [72, 72] */
    protected $dpi = [];

    /** @var string 存储路径 */
    protected $path;

    /** @var string 设置字体 */
    protected $font = __DIR__ . '/../../style/simkai.ttf';

    /** @var string 字体系列 例如 Microsoft YaHei */
    protected $font_family = '';

    /** @var string 默认目录 */
    protected $pathname = 'poster';

    /** @var string 文件名 */
    protected $filename;

    /** @var string 图片类型 */
    protected $type = 'png';

    /** @var string[] 图片类型 */
    protected $poster_type = [
        'gif'  => 'imagegif',
        'jpeg' => 'imagejpeg',
        'jpg'  => 'imagejpeg',
        'png'  => 'imagepng',
        'wbmp' => 'imagewbmp'
    ];

    /** @var array|null 返回结果 */
    public $result = null;

    /**
     * 设置基本配置
     * @Author lang
     * @Email: 732853989@qq.com
     * Date: 2023/2/12
     * Time: 下午10:09
     * @param array $params
     * @throws PosterException
     */
    public function setConfig($params = [])
    {
        isset($params['path']) && !empty($params['path']) && $this->setFilePath($params['path']);
        isset($params['font']) && !empty($params['font']) && $this->font = $params['font'];
        isset($params['font_family']) && !empty($params['font_family']) && $this->font_family = $params['font_family'];
        if (isset($params['dpi']) && !empty($params['dpi'])) {
            $this->dpi = is_numeric($params['dpi']) ? [$params['dpi'], $params['dpi']] : $params['dpi'];
        }
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
        $path = is_array($path) ? $path : [$path];
        $pathFileName = isset($path[0]) ? $path[0] : '';
        $pathFileName = str_replace(['\\', '/'], '/', $pathFileName);

        $fileName = $pathFileName ?: time();
        if (strripos($pathFileName, '/') !== false) {
            $this->setPathName($pathFileName);
            $fileName = substr($pathFileName, strripos($pathFileName, '/') + 1);
        }
        $this->setFileName($fileName);
        $this->setPath($pathFileName);
    }

    /**
     * setFileName 设置文件名
     * @Author lang
     * @Date   2022-03-10T15:42:06+0800
     * @param  [type]                   $fileName [description]
     */
    public function setFileName($fileName)
    {
        $this->filename = $fileName;
        if (strripos($this->filename, '.') !== false) {
            $this->type = substr($this->filename, strripos($this->filename, '.') + 1);
            if (!in_array($this->type, ['jpeg', 'jpg', 'png', 'gif', 'wbmp'])) {
                throw new PosterException('The file naming format is incorrect');
            }
        }
    }

    /**
     * setPathName 设置目录名
     * @Author lang
     * @Date   2022-03-10T15:42:19+0800
     * @param  [type]                   $fileName [description]
     */
    public function setPathName($pathFileName)
    {
        $this->pathname = substr($pathFileName, 0, strripos($pathFileName, '/'));
    }

    /**
     * setPath 设置文件位置
     * @Author lang
     * @Date   2022-03-10T15:42:38+0800
     * @param  [type]                   $fileName [description]
     */
    public function setPath($pathFileName)
    {
        // 绝对路径 or 相对路径
        $absolute = $this->isAbsolute($pathFileName);
        $this->path = $this->getDocumentRoot();
        $this->path = $absolute ? '' : ($this->path ? $this->path . '/' : __DIR__ . '/../../../tests/');
    }

    /**
     * 获取项目根目录
     * @Author lang
     * @Date   2022-03-10T15:42:38+0800
     */
    public function getDocumentRoot()
    {
        return iconv('UTF-8', 'GBK', $_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * 判断是否是绝对路径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:54
     * @param $pathFileName
     * @return bool
     */
    public function isAbsolute($pathFileName)
    {
        // 区分WIN系统绝对路径、暂时只区分linux win mac
        switch (PHP_OS) {
            case 'Darwin':
                $absolute = stripos($pathFileName, '/') === 0 ?: false;
                break;
            case 'linux':
            default:
                if (stripos(PHP_OS, 'WIN') !== false) {
                    $absolute = substr($pathFileName, 1, 1) === ':' ?: false;
                } else {
                    $absolute = stripos($pathFileName, '/') === 0 ?: false;
                }
                break;
        }

        return $absolute;
    }

    /**
     * 检查文件是否存在并创建
     * @Author lang
     * @Date   2020-08-14T15:32:04+0800
     * @param string $pathname 路径名称
     */
    public function dirExists($pathname)
    {
        if (!file_exists($this->path . $pathname)) {
            mkdir($this->path . $pathname, 0777, true);
        }
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
     * @return array|void
     */
    protected function createQr($text, $outfile, $level, $size, $margin, $saveAndPrint)
    {
        if ($outfile) {
            $this->setPath($outfile);
            $this->setPathName($outfile);
            $this->dirExists($this->pathname);
            \QRcode::png($text, $this->path . $outfile, $level, $size, $margin, $saveAndPrint);
            return ['url' => $outfile];
        } else {
            \QRcode::png($text, $outfile, $level, $size, $margin, $saveAndPrint);
        }

    }

    /**
     * 计算画布X轴位置
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 16:10
     * @return int
     */
    protected function calcDstX($dst_x, $imWidth, $bgWidth)
    {
        if ($dst_x == '0') {
            return $dst_x;
        } elseif ($dst_x === 'center') {

            $dst_x = ceil(($imWidth - $bgWidth) / 2);

        } elseif (is_numeric($dst_x) && $dst_x < 0) {

            $dst_x = ceil($imWidth + $dst_x);

        } elseif (is_array($dst_x)) {
            if ($dst_x[0] == 'center') {
                $dst_x = ceil(($imWidth - $bgWidth) / 2) + $dst_x[1];
            }
        } elseif (strpos($dst_x, '%') !== false) {

            if (substr($dst_x, 0, strpos($dst_x, '%')) < 0) {

                $dst_x = ceil($imWidth + ($imWidth * substr($dst_x, 0, strpos($dst_x, '%')) / 100));

            } else {

                $dst_x = ceil($imWidth * substr($dst_x, 0, strpos($dst_x, '%')) / 100);

            }

        }

        return $dst_x;
    }

    /**
     * 计算画布Y轴位置
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 16:10
     * @return int
     */
    protected function calcDstY($dst_y, $imHeight, $bgHeight)
    {
        if ($dst_y == '0') {
            return $dst_y;
        } elseif ($dst_y == 'center') {

            $dst_y = ceil(($imHeight - $bgHeight) / 2);

        } elseif (is_numeric($dst_y) && $dst_y < 0) {

            $dst_y = ceil($imHeight + $dst_y);

        } elseif (is_array($dst_y)) {

            if ($dst_y[0] == 'center') {

                $dst_y = ceil(($imHeight - $bgHeight) / 2) + $dst_y[1];

            }
        } elseif (strpos($dst_y, '%') !== false) {

            if (substr($dst_y, 0, strpos($dst_y, '%')) < 0) {

                $dst_y = ceil($imHeight + (($imHeight * substr($dst_y, 0, strpos($dst_y, '%'))) / 100));

            } else {

                $dst_y = ceil($imHeight * substr($dst_y, 0, strpos($dst_y, '%')) / 100);

            }
        }

        return $dst_y;
    }

    /**
     * 计算文字x轴坐标
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/2/13
     * Time: 14:15
     * @param $dst_x
     * @param $fontBox
     * @param null $x1
     * @param null $x2
     * @return false|float|int|mixed
     */
    protected function calcTextDstX($dst_x, $calcFont, $x1 = NULL, $x2 = NULL)
    {
        $fontBoxWidth = $calcFont['text_width'];
        $imWidth = ($x1 !== null && $x2 !== null) ?
            ($x2 - $x1)
            : $this->im_w;
        if ($dst_x === 'center') {
            $dst_x = ceil(($imWidth - $fontBoxWidth) / 2);
        } elseif (is_array($dst_x)) {
            $dst_x[1] = isset($dst_x[1]) ? $dst_x[1] : 0;
            $x1 = $x1 !== null ? $x1 : 0;
            switch ($dst_x[0]) {
                case 'center':
                    $dst_x = ceil(($imWidth - $fontBoxWidth) / 2) + $x1 + $dst_x[1];
                    break;
                case 'left': // 左对齐 且 左右偏移
                    $dst_x = $x1 + $dst_x[1];
                    break;
                case 'right': // 右对齐 且 左右偏移
                    $dst_x = ceil(($imWidth - $fontBoxWidth)) + $x1 + $dst_x[1];
                    break;
                case 'custom': // 设置 自定义宽度居中 ['custom', 'center|top|bottom', $x1, $x2, $offset] $x1 区间起点宽度 $x2 区间终点宽度 $offset 偏移
                    $custom = [$dst_x[1], isset($dst_x[4]) ? $dst_x[4] : 0];
                    $dst_x = $this->calcTextDstX($custom, $calcFont, $dst_x[2], $dst_x[3]);
                    break;
                default:
                    $dst_x = 0;
            }

        } elseif (strpos($dst_x, '%') !== false) {

            if (substr($dst_x, 0, strpos($dst_x, '%')) < 0) {

                $dst_x = ceil($imWidth + ($imWidth * substr($dst_x, 0, strpos($dst_x, '%')) / 100));

            } else {

                $dst_x = ceil($imWidth * substr($dst_x, 0, strpos($dst_x, '%')) / 100);

            }
        }

        return $dst_x;
    }

    /**
     * 计算文字y轴坐标
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/2/13
     * Time: 14:14
     * @param $dst_y
     * @param $fontBox
     * @param null $y1
     * @param null $y2
     * @return false|float|int|mixed
     */
    protected function calcTextDstY($dst_y, $calcFont, $y1 = NULL, $y2 = NULL)
    {
        $fontBoxHeight = $calcFont['text_height']; // 文字加换行数的高度
        $imHeight = ($y1 !== null && $y2 !== null) ?
            ($y2 - $y1)
            : $this->im_h;
        if ($dst_y === 'center') {
            $dst_y = ceil(($imHeight/2) + ($fontBoxHeight / 2 ) - $fontBoxHeight);
        } elseif (is_array($dst_y)) {
            $dst_y[1] = isset($dst_y[1]) ? $dst_y[1] : 0;
            $y1 = $y1 !== null ? $y1 : 0;
            switch ($dst_y[0]) {
                case 'center':
                    $dst_y = ceil(($imHeight/2) + ($fontBoxHeight / 2 ) - $fontBoxHeight) + $y1 + $dst_y[1];
                    break;
                case 'top': // 顶对齐 且 上下偏移
                    $dst_y = $y1 + $dst_y[1];
                    break;
                case 'bottom': // 底对齐 且 上下偏移
                    $dst_y = ceil(($imHeight - $fontBoxHeight)) + $y1 + $dst_y[1];
                    break;
                case 'custom': // 设置 自定义高度居中 ['custom', 'center|top|bottom', $y1, $y2, $offset] $y1 区间起点高度 $y2 区间终点高度 $offset 偏移
                    $custom = [$dst_y[1], isset($dst_y[4]) ? $dst_y[4] : 0];
                    $dst_y = $this->calcTextDstY($custom, $calcFont, $dst_y[2], $dst_y[3]);
                    break;
                default:
                    $dst_y = 0;
            }

        } elseif (strpos($dst_y, '%') !== false) {

            if (substr($dst_y, 0, strpos($dst_y, '%')) < 0) {

                $dst_y = ceil($imHeight + (($imHeight * substr($dst_y, 0, strpos($dst_y, '%'))) / 100));

            } else {

                $dst_y = ceil($imHeight * substr($dst_y, 0, strpos($dst_y, '%')) / 100);

            }
        }

        return $dst_y;
    }

    /**
     * 计算加粗绘画
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 16:46
     * @param int $i        当前循环次数
     * @param int $weight   循环次数
     * @param int $fontSize 字体大小
     * @param int $dst_x    x 位置
     * @param int $dst_y    y 位置
     * @return array|float[]
     */
    protected function calcWeight($i, $weight, $fontSize, $dst_x, $dst_y)
    {
        if ($weight % 2 == 0 && $i > 0) {
            $really_dst_x = $dst_x + ($i * 0.25);
            $really_dst_y = $dst_y + $fontSize;
        } elseif($weight % 2 != 0 && $i > 0) {
            $really_dst_x = $dst_x;
            $really_dst_y = $dst_y + $fontSize + ($i * 0.25);
        } else {
            $really_dst_x = $dst_x;
            $really_dst_y = $dst_y + $fontSize;
        }
        return [$really_dst_x, $really_dst_y];
    }

}