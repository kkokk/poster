<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 18:10
 */

namespace Kkokk\Poster\Image;

interface ExtensionInterface
{
    /**
     * 配置基础参数
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/24
     * Time: 14:53
     * @param array $params
     * @return Builder
     */
    public function config($params = []);

    /**
     * 创建画布
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 14:56
     * @param $w
     * @param $h
     * @param array $rgba
     * @param false $alpha
     * @return Builder
     */
    public function buildIm($w, $h, $rgba = [], $alpha = false);

    /**
     * 以图像资源创建画布
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 14:57
     * @param $src
     * @param int $w
     * @param int $h
     * @return Builder
     */
    public function buildImDst($src, $w = 0, $h = 0);

    /**
     * 创建背景
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:34
     * @param $w
     * @param $h
     * @param array $rgba
     * @param false $alpha
     * @param int $dst_x
     * @param int $dst_y
     * @param int $src_x
     * @param int $src_y
     * @param \Closure|null $callback
     * @return Builder
     */
    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, \Closure $callback = null);

    /**
     * 生成二维码
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/24
     * Time: 14:48
     * @param string $text 二维码内容
     * @param false|string $outfile false 直接输出 或者填写输出路径
     * @param string $level 容错级别，默认为L
     *                              可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)
     *                              这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别
     * @param int $size 控制生成图片的大小，默认为4
     * @param int $margin 控制生成二维码的空白区域大小
     * @param int $saveAndPrint 保存二维码图片并显示出来，$outfile 必须传递图片路径
     * @return mixed
     */
    public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0);

    /**
     * 获取海报
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:35
     * @param $query
     * @param $path
     * @return array
     */
    public function getPoster($query, $path);

    /**
     * 设置海报
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:35
     * @param $query
     * @return boolean
     */
    public function setPoster($query);

    /**
     * 返回流文件
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:37
     * @param $query
     * @return resource
     */
    public function stream($query);

    /**
     * 获取 base64 字符串
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:38
     * @param $query
     * @return string
     */
    public function baseData($query);

    /**
     * 获取 im 对象
     * Author: lang
     * Date: 2023/8/10
     * Time: 16:00
     * @param $query
     * @return mixed
     */
    public function getIm($query);

    /**
     * 获取 im 对象 图片类型，宽高
     * Author: lang
     * Date: 2023/8/10
     * Time: 16:00
     * @param $query
     * @return mixed
     */
    public function getImInfo($query);

    /**
     * 获取流
     * Author: lang
     * Date: 2024/3/12
     * Time: 15:03
     * @param $query
     * @return mixed
     */
    public function blob($query);

    /**
     * 获取临时路径
     * Author: lang
     * Date: 2024/3/12
     * Time: 15:34
     * @param $query
     * @return mixed
     */
    public function tmp($query);

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
}