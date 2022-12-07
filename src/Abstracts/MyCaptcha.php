<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:10
 */

namespace Kkokk\Poster\Abstracts;


abstract class MyCaptcha
{
    abstract public function config($param = []);
    abstract public function check($key , $value , $leeway = 0);
    abstract public function get();
    abstract public function draw();

    protected $im; // im
    protected $expire = 180; // 过期时间
    protected $leeway = 5;   // 误差值

    // 转base64
    protected function baseData($im){
        ob_start();
        imagepng($im);
        $data = ob_get_contents();
        ob_end_clean();
        $baseData = "data:image/png;base64,".base64_encode($data);
        imagedestroy($im);
        return $baseData;
    }
}