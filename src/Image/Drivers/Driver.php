<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:28
 */

namespace Kkokk\Poster\Image\Drivers;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'PHPQrcode' . DIRECTORY_SEPARATOR . 'phpqrcode.php');

use Kkokk\Poster\Exception\PosterException;

class Driver
{
    /** @var resource 画布 */
    protected $image;

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
    protected $font = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'simkai.ttf';
    /** @var string 字体系列 例如 Microsoft YaHei */
    protected $font_family = '';
    /** @var int 字体大小 */
    protected $font_size = 16;
    /** @var int[] 字体颜色 */
    protected $font_rgba = [52, 52, 52, 1];
    /** @var int 字体间距 */
    protected $font_space = 0;
    /** @var int 字体粗细 */
    protected $font_weight = 1;
    /** @var null 字体旋转角度 */
    protected $font_angle = 0;
    /** @var null 字体最大换行宽度 */
    protected $font_max_w = 0;

    /** @var string 默认目录 */
    protected $pathname = 'poster';

    /** @var string 文件名 */
    protected $filename;

    /** @var string 图片类型 */
    protected $type = 'png';

    /** @var array|null 返回结果 */
    public $result = null;

    /**
     * 设置基本配置
     * @Author lang
     * @Email  : 732853989@qq.com
     * Date: 2023/2/12
     * Time: 下午10:09
     * @param array $params
     * @throws PosterException
     */
    public function setConfig($params = [])
    {
        isset($params['path']) && !empty($params['path']) && $this->setFilePath($params['path']);
        isset($params['font_family']) && !empty($params['font_family']) && $this->font = $params['font_family'];
        isset($params['font_size']) && !empty($params['font_size']) && $this->font_size = $params['font_size'];
        isset($params['font_rgba']) && !empty($params['font_rgba']) && $this->font_rgba = $params['font_rgba'];
        isset($params['font_space']) && !empty($params['font_space']) && $this->font_space = $params['font_space'];
        isset($params['font_weight']) && !empty($params['font_weight']) && $this->font_weight = $params['font_weight'];
        isset($params['font_angle']) && !empty($params['font_angle']) && $this->font_angle = $params['font_angle'];
        isset($params['font_max_w']) && !empty($params['font_max_w']) && $this->font_max_w = $params['font_max_w'];

        if (isset($params['dpi']) && !empty($params['dpi'])) {
            $this->dpi = is_numeric($params['dpi']) ? [$params['dpi'], $params['dpi']] : $params['dpi'];
        }
        if (isset($params['font']) && !empty($params['font'])) {
            $this->font = get_real_path($params['font']);
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
        $pathFileName = str_replace(['\\', DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $pathFileName);

        $fileName = $pathFileName ?: time();
        if (strripos($pathFileName, DIRECTORY_SEPARATOR) !== false) {
            $this->setPathName($pathFileName);
            $fileName = substr($pathFileName, strripos($pathFileName, DIRECTORY_SEPARATOR) + 1);
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
        $this->pathname = substr($pathFileName, 0, strripos($pathFileName, DIRECTORY_SEPARATOR));
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
        $absolute = is_absolute($pathFileName);
        $this->path = get_document_root();
        $this->path = $absolute ? '' : ($this->path ?: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR);
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
    public function createQr($text, $outfile, $level, $size, $margin, $saveAndPrint)
    {
        if ($outfile) {
            $this->setPath($outfile);
            $this->setPathName($outfile);
            dir_exists($this->path . $this->pathname);
            \QRcode::png($text, $this->path . $outfile, $level, $size, $margin, $saveAndPrint);
            return ['url' => $outfile];
        } else {
            \QRcode::png($text, $outfile, $level, $size, $margin, $saveAndPrint);
        }

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
                $driver->result = $driver->createQr(...$item['params']);
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
     * @return mixed
     */
    public function getImInfo()
    {
        return [
            'type'   => $this->type,
            'width'  => $this->im_w,
            'height' => $this->im_h,
        ];
    }

    public function getStyleAttr($style, $type = 'color')
    {
        $attr = '';

        $styles = explode(';', $style);

        foreach ($styles as $style) {
            $styleParts = explode(':', $style);
            $property = trim($styleParts[0]);
            if (isset($styleParts[1])) {
                $value = trim($styleParts[1]);
            } else {
                $value = '';
            }

            if ($property === $type) {
                $attr = $value; // 输出属性值
            }
        }

        return $attr;
    }

    /**
     * 获取单个字属性
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/6/2
     * Time: 15:38
     * @param string $content
     * @param string $color
     * @param int    $w
     * @return array
     */
    public function getLetterArr($content = "\n", $color = '', $w = 0)
    {
        return [
            'color' => $color,
            'w'     => $w,
            'value' => $content
        ];
    }

    /**
     * 获取内容
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/6/2
     * Time: 15:37
     * @param        $letter
     * @param        $content
     * @param string $color
     */
    public function getNodeValue(&$letter, $content, $color = '')
    {
        $contents = $this->getBrNodeValue($content);
        foreach ($contents as $k => $v) {
            if ($v != '') {
                if (isset($contents[$k - 1])) {
                    $letter[] = $this->getLetterArr();
                }

                for ($i = 0; $i < mb_strlen($v); $i++) {
                    $letter[] = $this->getLetterArr(mb_substr($v, $i, 1), $color);
                }
            } else {
                if ($k != 0) {
                    $letter[] = $this->getLetterArr();
                }
            }

        }
    }

    /**
     * 匹配换行符
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/6/2
     * Time: 15:37
     * @param $content
     * @return array|false|string[]
     */
    public function getBrNodeValue($content)
    {
        $pattern = '/<br>|<br\/>/i';
        // 分割字符串
        return preg_split($pattern, $content, -1, 2);
    }

}