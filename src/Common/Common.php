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
    public function baseData($im, $type = 'png')
    {
        ob_start();
        $this->imType[$type]($im);
        $data = ob_get_contents();
        ob_end_clean();
        $baseData = 'data:image/' . $type . ';base64,' . base64_encode($data);
        imagedestroy($im);
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
    public function imOutput($im, $dir='', $type='png', $quality=75)
    {
        if($type == 'jpg' || $type == 'jpeg'){
            $this->imType[$type]($im, $dir, $quality);
        } else {
            $this->imType[$type]($im, $dir);
        }

        return 1;
    }
}