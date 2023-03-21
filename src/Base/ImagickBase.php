<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/21
 * Time: 18:02
 */

namespace Kkokk\Poster\Base;

require_once(__DIR__ . '/../PHPQrcode/phpqrcode.php');

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Common\Common;

class ImagickBase extends PosterBase
{
    protected $im;
    protected $im_w;
    protected $im_h;
    protected $pathname = 'poster';
    protected $filename;
    protected $type = '';
    protected $path;
    protected $source;
    protected $font_family = __DIR__ . '/../style/simkai.ttf';
    protected $poster_type = [
        'gif' => 'imagegif',
        'jpeg' => 'imagejpeg',
        'jpg' => 'imagejpeg',
        'png' => 'imagepng',
        'wbmp' => 'imagewbmp'
    ];

    public function __construct($params = [])
    {
        $params = is_array($params) ? $params : [$params];

        if (PHP_VERSION <= 7) {
            $pathFileName = isset($params[0]) ? $params[0] : '';
        } else {
            $pathFileName = $params[0] ?? '';
        }
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
        isset($params['font_family']) && !empty($params['font_family']) && $this->font_family = $params['font_family'];
    }

}