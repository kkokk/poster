<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/12
 * Time: 11:47
 */

namespace Kkokk\Poster\Captcha;


use Kkokk\Poster\Abstracts\MyCaptcha;

class Input extends MyCaptcha
{

    protected $configs = [
        'src' => '',
        'im_width'      => 256,
        'im_height'     => 64,
        'type'          => 'number', // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        'font_family'   => __DIR__ . '/../style/simkai.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件
        'font_size'     => 32, // 字体大小
        'font_count'    => 4,  // 字体长度
        'line_count'    => 5,  // 干扰线数量
        'char_count'    => 10,  // 干扰字符数量
    ];

    public function config($param = [])
    {
        if (empty($param)) return $this;
        if (PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['im_width'] = isset($param['im_width']) ? $param['im_width'] : $this->configs['im_width'];
            $this->configs['im_height'] = isset($param['im_height']) ? $param['im_height'] : $this->configs['im_height'];
            $this->configs['type'] = isset($param['type']) ? $param['type'] : $this->configs['type'];
            $this->configs['font_family'] = isset($param['font_family']) ? $param['font_family'] : $this->configs['font_family'];
            $this->configs['font_size'] = isset($param['font_size']) ? $param['font_size'] : $this->configs['font_size'];
            $this->configs['font_count'] = isset($param['font_count']) ? $param['font_count'] : $this->configs['font_count'];
            $this->configs['line_count'] = isset($param['line_count']) ? $param['line_count'] : $this->configs['line_count'];
            $this->configs['char_count'] = isset($param['char_count']) ? $param['char_count'] : $this->configs['char_count'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $param['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $param['im_height'] ?? $this->configs['im_height'];
            $this->configs['type'] = $param['type'] ?? $this->configs['type'];
            $this->configs['font_family'] = $param['font_family'] ?? $this->configs['font_family'];
            $this->configs['font_size'] = $param['font_size'] ?? $this->configs['font_size'];
            $this->configs['font_count'] = $param['font_count'] ?? $this->configs['font_count'];
            $this->configs['line_count'] = $param['line_count'] ?? $this->configs['line_count'];
            $this->configs['char_count'] = $param['char_count'] ?? $this->configs['char_count'];
        }

        return $this;
    }

    public function check($key, $value, $leeway = 0)
    {
        if(class_exists(Cache::class)){
            $x = Cache::pull($key);
        } else {
            return false;
        }

        if(empty($x)) return false;

        return $x == $value;
    }

    public function get($expire = 0)
    {
        $data = $this->draw();

        imagepng($this->im, __DIR__ . '/../../tests/poster/input' . $this->configs['type'] . '.png');

        $baseData = $this->baseData($this->im, 'png');

        $key = uniqid('input:'.$this->configs['type'].mt_rand(0, 9999), true);

        if(class_exists(Cache::class)){
            Cache::put($key , $data['value'], $expire ?: $this->expire);
        }

        return [
            'img' => $baseData,
        ];
    }

    public function draw()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [mt_rand(125, 255), 255, mt_rand(125, 255), 1], false);

        if($this->configs['src']) { // 如果指定背景则用背景
            $this->drawImage($this->configs['src'], false, 0, 0, 0, 0, $im_width, $im_height);
        }

        $this->drawLine(); // 干扰线

        $this->drawChar(); // 干扰字

        $res = $this->getContents();

        $this->drawText($res['contents']); // 字

        return $res;
    }

    public function getContents(){
        // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        $fontCount = $this->configs['font_count'];
        switch ($this->configs['type']) {
            case 'math':
                $formula = '+-x';
                $a = mt_rand(0, 20);
                $b = mt_rand(0, 20);

                $formula = substr($formula, mt_rand(0, 2), 1);

                if($formula=='+') $mul =  $a + $b;
                if($formula=='-') $mul =  $a - $b;
                if($formula=='x') $mul =  $a * $b;

                $res = [
                    'contents' => [
                        $a,
                        $formula,
                        $b,
                        '='
                    ],
                    'value'    => $mul,
                ];

                break;
            case 'text':
                $str = '的一是在不了有和人这中大为上个国我以要他时来用们生到作地于出就分对成会可主发年动同工也能下过子说产种面而方后多定行学法所民得经十三之进着等部度家电力里如水化高自二理起小物现实加量都两体制机当使点从业本去把性好应开它合还因由其些然前外天政四日那社义事平形相全表间样与关各重新线内数正心反你明看原又么利比或但质气第向道命此变条只没结解问意建月公无系军很情者最立代想已通并提直题党程展五果料象员革位入常文总次品式活设及管特件长求老头基资边流路级少图山统接知较将组见计别她手角期根论运农指几九区强放决西被干做必战先回则任取据处队南给色光门即保治北造百规热领七海口东导器压志世金增争济阶油思术极交受联什认六共权收证改清己美再采转更单风切打白教速花带安场身车例真务具万每目至达走积示议声报斗完类八离华名确才科张信马节话米整空元况今集温传土许步群广石记需段研界拉林律叫且究观越织装影算低持音众书布复容儿须际商非验连断深难近矿千周委素技备半办青省列习响约支般史感劳便团往酸历市克何除消构府称太准精值号率族维划选标写存候毛亲快效斯院查江型眼王按格养易置派层片始却专状育厂京识适属圆包火住调满县局照参红细引听该冯价严龙飞';
                for ($i=0; $i < $fontCount; $i++){
                    $contents .= mb_substr($str, mt_rand(0, 499), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value'    => $contents,
                ];
                break;
            case 'alpha_num':
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                for ($i=0; $i < $fontCount; $i++){
                    $contents .= substr($str, mt_rand(0, 61), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value'    => $contents,
                ];
                break;
            default:
                $str = '1234567890';
                for ($i=0; $i < $fontCount; $i++){
                    $contents .= substr($str, mt_rand(0, 9), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value'    => $contents,
                ];
                break;
        }

        return $res;
    }

    public function drawText($contents)
    {
        $font_family = $this->configs['font_family'];
        $font = $this->configs['font_size'];
        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        if(is_array($contents)){
            $equally = $im_width / count($contents);
            foreach ($contents as $k => $v){
                $color = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                $content = $v;
                $x = mt_rand($k * $equally + 10, ($k + 1) * $equally - $font);
                $y = mt_rand($font+10, $im_height);
                $angle = $this->configs['type']==='math' ? 0 : mt_rand(0, 45);
                imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
            }

        } else {
            $equally = $im_width / mb_strlen($contents);

            for ($i = 0; $i < mb_strlen($contents); $i++) {
                $color = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                $content = mb_substr($contents, $i, 1);
                $x = mt_rand($i * $equally + 10, ($i + 1) * $equally - $font);
                $y = mt_rand($font+10, $im_height);
                $angle = mt_rand(0, 45);
                imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
            }
        }


    }
}