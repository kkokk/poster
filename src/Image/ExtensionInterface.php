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

    public function buildIm($w, $h, $rgba = [], $alpha = false);

    public function buildImDst($src, $w = 0, $h = 0);

    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, \Closure $callback = null);

    /**
     * 生成二维码
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/24
     * Time: 14:48
     * @param string $text          二维码内容
     * @param false|string $outfile false 直接输出 或者填写输出路径
     * @param string $level         容错级别，默认为L
     *                              可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)
     *                              这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别
     * @param int $size             控制生成图片的大小，默认为4
     * @param int $margin           控制生成二维码的空白区域大小
     * @param int $saveAndPrint     保存二维码图片并显示出来，$outfile 必须传递图片路径
     * @return mixed
     */
    public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0);

    public function getPoster($query, $path);

    public function setPoster($query);

    public function stream($query);

    public function baseData($query);
}