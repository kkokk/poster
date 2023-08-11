<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:10
 */

namespace Kkokk\Poster\Captcha\Generators;

use Kkokk\Poster\Common\Common;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Facades\Cache;
use Kkokk\Poster\Image\Drivers\GdDriver;

class CaptchaGenerator
{
    protected $PosterDriver; // GdDriver
    protected $Common; // Common
    protected $im; // im
    protected $expire = 180; // 过期时间
    protected $leeway = 5;   // 误差值

    function __construct()
    {
        $this->PosterDriver = new GdDriver();
        $this->Common = new Common;
    }

    /**
     * 转base64
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/15
     * Time: 10:38
     * @param $im
     * @param string $type
     * @return string
     */
    protected function baseData($im, $type = 'png')
    {
        return $this->Common->baseData($im, $type);
    }

    public function imOutput($im, $type = 'png', $quality = 75, $filename = 'im')
    {
        $yes = 0; // 控制是否生成图片，测试时方便查看
        $dir = __DIR__ . '/../../../tests/poster/' . $filename . '.' . $this->configs['im_type'];
        return $yes && $this->Common->imOutput($im, $dir, $type, $quality);
    }

    public function getCross($p1, $p2, $p)
    {
        return $this->Common->getCross($p1, $p2, $p);
    }

    // 获取缓存
    public function getCache($key)
    {
        try {
            $contents = Cache::pull($key);
        } catch (PosterException $e) {
            // 如果未定义缓存器则返回false, 需要传递自行保存的密码进行比对
            return false;
        } catch (\Exception $e) {
            // 报错，则返回false, 需要传递自行保存的密码进行比对
            return false;
        }
        return $contents;
    }

    // 设置缓存
    public function setCache($key, $value, $expire)
    {
        try {
            Cache::put($key, $value, $expire ?: $this->expire);
        } catch (PosterException $e) {
            // 未查询到缓存器，则返回密码，自行保存
            return false;
        } catch (\Exception $e) {
            // 报错，则返回密码，自行保存
            return false;
        }
        return true;
    }

    /**
     * 画布填充图片
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/15
     * Time: 10:37
     * @param string $src
     * @param false $resize
     * @param int $dst_x
     * @param int $dst_y
     * @param int $src_x
     * @param int $src_y
     * @param int $src_width
     * @param int $src_height
     * @throws PosterException
     */
    protected function drawImage($src = '', $resize = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_width = 0, $src_height = 0)
    {
        $src = $src ?: $this->getImBg();

        list($Width, $Height, $bgType) = @getimagesize($src);

        $Width = $src_width ?: $Width;
        $Height = $src_height ?: $Height;

        $bgType = image_type_to_extension($bgType, false);

        if (empty($bgType)) throw new PosterException('image resources cannot be empty (' . $src . ')');

        $getGdVersion = preg_match('~\((.*) ~', gd_info()['GD Version'], $matches);
        if ($getGdVersion && (float)$matches[1] < 2 && $bgType == 'gif') {
            $pic = imagecreatefromstring(file_get_contents($src));
        } else {
            $fun = 'imagecreatefrom' . $bgType;
            $pic = @$fun($src);
        }

        if ($resize) {
            imagecopyresized($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $this->configs['im_width'], $this->configs['im_height'], $Width, $Height);
        } else {
            imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $Width, $Height);
        }
        $this->destroyImage($pic);
    }

    /**
     * 干扰线
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/15
     * Time: 10:37
     * @param int $width
     * @param int $height
     * @throws PosterException
     */
    public function drawLine($width = 0, $height = 0)
    {
        $lineCount = $this->configs['line_count'];

        if ($lineCount > 0) {
            $im_width = $width ?: $this->configs['im_width'];
            $im_height = $height ?: $this->configs['im_height'];

            for ($i = 0; $i <= $lineCount; $i++) {
                $color = $this->PosterDriver->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                $x1 = mt_rand(0, $im_width);
                $y1 = mt_rand(0, $im_height);
                $x2 = mt_rand(0, $im_width);
                $y2 = mt_rand(0, $im_height);
                imageline($this->im, $x1, $y1, $x2, $y2, $color);
            }

            imageantialias($this->im, true);
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
            $font_family = $this->configs['font_family'];
            $font = $this->configs['font_size'] / 2;

            $contents = $this->getChar($this->configs['type']);

            $color = $this->PosterDriver->createColorAlpha($this->im, [255, 255, 255, 1]);

            $im_width = $width ?: $this->configs['im_width'];
            $im_height = $height ?: $this->configs['im_height'];

            for ($i = 0; $i < $charCount; $i++) {
                $content = mb_substr($contents, mt_rand(0, mb_strlen($contents) - 1), 1);
                $x = mt_rand($font, $im_width - $font);
                $y = mt_rand($font, $im_height - $font);
                $angle = mt_rand(0, 45);
                imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
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

    /**
     * 释放resource
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/15
     * Time: 10:38
     * @param $Resource
     */
    protected function destroyImage($Resource)
    {

        !is_resource($Resource) || imagedestroy($Resource);
    }

    /**
     * 析构方法，用于销毁 im 图像资源
     */
    public function __destruct()
    {
        empty($this->im) || !is_resource($this->im) || imagedestroy($this->im);
    }
}