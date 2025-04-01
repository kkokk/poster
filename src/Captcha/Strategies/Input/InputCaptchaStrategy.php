<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 14:50
 */

namespace Kkokk\Poster\Captcha\Strategies\Input;

use Kkokk\Poster\Captcha\Strategies\CaptchaStrategy;
use Kkokk\Poster\Exception\PosterException;

class InputCaptchaStrategy extends CaptchaStrategy
{
    protected $configs = [
        'src'         => '',
        'im_width'    => 256,
        'im_height'   => 64,
        'im_type'     => 'png', // png 默认 jpg quality 质量
        'quality'     => 80,    // jpg quality 质量
        'type'        => 'number', // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        'font_family' => POSTER_BASE_PATH . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'simkai.ttf',
        'font_size'   => 32, // 字体大小
        'font_count'  => 4,  // 字体长度
        'line_count'  => 5,  // 干扰线数量
        'char_count'  => 10,  // 干扰字符数量
    ];

    /**
     * 干扰线
     * User: lang
     * Date: 2025/4/1
     * Time: 14:54
     * @param $width
     * @param $height
     * @return void
     */
    public function drawLine($width = 0, $height = 0)
    {
        $lineCount = $this->configs['line_count'];
        if ($lineCount > 0) {
            $imageWidth = $width ?: $this->configs['im_width'];
            $imageHeight = $height ?: $this->configs['im_height'];
            for ($i = 0; $i <= $lineCount; $i++) {
                $x1 = mt_rand(0, $imageWidth);
                $y1 = mt_rand(0, $imageHeight);
                $x2 = mt_rand(0, $imageWidth);
                $y2 = mt_rand(0, $imageHeight);
                $this->driver->CopyLine($x1, $y1, $x2, $y2, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
            }
        }
    }

    /**
     * 干扰文字
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/15
     * Time: 10:37
     * @param int $width
     * @param int $height
     * @throws PosterException
     */
    public function drawChar($width = 0, $height = 0)
    {
        $charCount = $this->configs['char_count'];

        if ($charCount > 0) {
            $font = $this->configs['font_family'];
            $fontSize = $this->configs['font_size'] / 2;

            $contents = $this->getChar($this->configs['type']);

            $imageWidth = $width ?: $this->configs['im_width'];
            $imageHeight = $height ?: $this->configs['im_height'];

            for ($i = 0; $i < $charCount; $i++) {
                $content = mb_substr($contents, mt_rand(0, mb_strlen($contents) - 1), 1);
                $x = mt_rand($fontSize, $imageWidth - $fontSize);
                $y = mt_rand($fontSize, $imageHeight - $fontSize);
                $angle = mt_rand(0, 45);
                $this->driver->CopyText($content, $x, $y, $fontSize,
                    [255, 255, 255, 1], null, $font, null, null, $angle);
            }
        }
    }

    public function getChar($type)
    {
        switch ($type) {
            case 'text':
                $str = '的一是在不了有和人这中大为上个国我以要他时来用们生到作地于出就分对成会可主发年动同工也能下过子说产种面而方后多定行学法所民得经十三之进着等部度家电力里如水化高自二理起小物现实加量都两体制机当使点从业本去把性好应开它合还因由其些然前外天政四日那社义事平形相全表间样与关各重新线内数正心反你明看原又么利比或但质气第向道命此变条只没结解问意建月公无系军很情者最立代想已通并提直题党程展五果料象员革位入常文总次品式活设及管特件长求老头基资边流路级少图山统接知较将组见计别她手角期根论运农指几九区强放决西被干做必战先回则任取据处队南给色光门即保治北造百规热领七海口东导器压志世金增争济阶油思术极交受联什认六共权收证改清己美再采转更单风切打白教速花带安场身车例真务具万每目至达走积示议声报斗完类八离华名确才科张信马节话米整空元况今集温传土许步群广石记需段研界拉林律叫且究观越织装影算低持音众书布复容儿须际商非验连断深难近矿千周委素技备半办青省列习响约支般史感劳便团往酸历市克何除消构府称太准精值号率族维划选标写存候毛亲快效斯院查江型眼王按格养易置派层片始却专状育厂京识适属圆包火住调满县局照参红细引听该冯价严龙飞';
                break;
            case 'alpha_num':
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                break;
            case 'math':
            case 'number':
                $str = '1234567890';
                break;
            default:
                $str = 'abcdefghijklmnopqrstuvwxyz';
                break;
        }

        return $str;
    }
}