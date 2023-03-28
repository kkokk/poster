<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/9
 * Time: 15:53
 */

namespace Kkokk\Poster\Common;

class Common
{
    protected $imType = [
        'gif' => 'imagegif',
        'jpeg' => 'imagejpeg',
        'jpg' => 'imagejpeg',
        'png' => 'imagepng',
        'wbmp' => 'imagewbmp'
    ];

    // 转base64
    public function baseData($image, $type = 'png')
    {
        $baseData = '';
        if (is_resource($image)) {
            ob_start();
            $this->imType[$type]($image);
            $data = ob_get_contents();
            ob_end_clean();
            $baseData = 'data:image/' . $type . ';base64,' . base64_encode($data);
            imagedestroy($image);
        } elseif (is_string($image)) {
            $baseData = 'data:image/' . $type . ';base64,' . base64_encode($image);
        }
        return $baseData;
    }

    /**
     * 输出图片
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/15
     * Time: 10:44
     * @param $im
     * @param string $dir
     * @param string $type
     * @param int $quality
     */
    public function imOutput($im, $dir = '', $type = 'png', $quality = 75)
    {
        if ($type == 'jpg' || $type == 'jpeg') {
            $this->imType[$type]($im, $dir, $quality);
        } else {
            $this->imType[$type]($im, $dir);
        }

        return 1;
    }

    /**
     * 计算 三个点的叉乘 |p1 p2| X |p1 p|
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/15
     * Time: 10:44
     * @param int $p1
     * @param int $p2
     * @param int $p
     * @return int
     */
    public function getCross($p1, $p2, $p)
    {
        // (p2.x - p1.x) * (p.y - p1.y) -(p.x - p1.x) * (p2.y - p1.y);
        return ($p1[0] - $p[0]) * ($p2[1] - $p[1]) - ($p2[0] - $p[0]) * ($p1[1] - $p[1]);
    }
}