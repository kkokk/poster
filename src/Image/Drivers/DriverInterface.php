<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/27
 * Time: 11:08
 */

namespace Kkokk\Poster\Image\Drivers;

interface DriverInterface
{
    /**
     * 获取文件路径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:23
     * @param string $path
     * @return array
     */
    public function getData($path = '');

    /**
     * 输出流
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:23
     * @return resource
     */
    public function getStream();

    /**
     * 获取base64文件
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:23
     * @return string
     */
    public function getBaseData();

    /**
     * 设置图片
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:24
     * @return array|\Kkokk\Poster\Exception\PosterException
     */
    public function setData();

    /**
     * 获取 Im 实例
     * User: lang
     * Date: 2023/8/10
     * Time: 16:16
     * @return mixed
     */
    public function getIm();

    /**
     * 创建画布
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:30
     * @param int     $w
     * @param int     $h
     * @param array   $rgba
     * @param boolean $alpha
     * @return void
     */
    public function Im($w, $h, $rgba = [255, 255, 255, 1], $alpha = false);

    /**
     * 以指定资源创建画布
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:31
     * @param string $source
     * @param int    $w
     * @param int    $h
     * @return void
     */
    public function ImDst($source, $w = 0, $h = 0);

    /**
     * 创建背景
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:40
     * @param int   $w
     * @param int   $h
     * @param array $rgba
     * @param false $alpha
     * @param int   $dst_x
     * @param int   $dst_y
     * @param int   $src_x
     * @param int   $src_y
     * @param array $query
     * @return void
     */
    public function Bg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $query = []);

    /**
     * 复制图片
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:41
     * @param string|array $src
     * @param int          $dst_x
     * @param int          $dst_y
     * @param int          $src_x
     * @param int          $src_y
     * @param int          $src_w
     * @param int          $src_h
     * @param false        $alpha
     * @param string       $type
     * @return void
     */
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
    );

    /**
     * 复制文字
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:41
     * @param string $content
     * @param int    $dst_x
     * @param int    $dst_y
     * @param int    $fontSize
     * @param array  $rgba
     * @param int    $max_w
     * @param string $font
     * @param int    $weight
     * @param int    $space
     * @param int    $angle
     * @return void
     */
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
    );

    /**
     * 复制线
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:44
     * @param int    $x1
     * @param int    $y1
     * @param int    $x2
     * @param int    $y2
     * @param array  $rgba
     * @param string $type
     * @param int    $weight
     * @return void
     */
    public function CopyLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1);

    /**
     * 复制圆弧
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:45
     * @param int    $cx
     * @param int    $cy
     * @param int    $w
     * @param int    $h
     * @param int    $s
     * @param int    $e
     * @param array  $rgba
     * @param string $type
     * @param string $style
     * @param int    $weight
     * @return void
     */
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
    );

    /**
     * 复制二维码
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:47
     * @param string $text
     * @param int    $size
     * @param int    $margin
     * @param int    $dst_x
     * @param int    $dst_y
     * @param int    $src_x
     * @param int    $src_y
     * @param int    $src_w
     * @param int    $src_h
     * @return mixed
     */
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
    );

    /**
     * 执行画图
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:48
     * @param array       $query
     * @param Driver|null $driver
     * @return Driver
     */
    public function execute($query = [], Driver $driver = null);

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
    public function crop($x = 0, $y = 0, $width = 0, $height = 0);

    /**
     * 获取流
     * Author: lang
     * Date: 2024/3/12
     * Time: 15:11
     * @return mixed
     */
    public function blob();

    /**
     * 保存到临时文件并返回路径
     * User: lang
     * Date: 2024/3/12
     * Time: 15:28
     * @return mixed
     */
    public function tmp();
}