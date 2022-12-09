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
    // 转base64
    public function baseData($im, $type){
        $poster_type = [
            'gif' => 'imagegif',
            'jpeg' => 'imagejpeg',
            'jpg' => 'imagejpeg',
            'png' => 'imagepng',
            'wbmp' => 'imagewbmp'
        ];
        ob_start();
        $poster_type[$type]($im);
        $data = ob_get_contents();
        ob_end_clean();
        $baseData = 'data:image/'.$type.';base64,'.base64_encode($data);
        imagedestroy($im);
        return $baseData;
    }
}