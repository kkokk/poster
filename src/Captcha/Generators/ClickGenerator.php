<?php
/**
 * @Author lang
 * @Email: 732853989@qq.com
 * Date: 2022/12/11
 * Time: 下午9:40
 */

namespace Kkokk\Poster\Captcha\Generators;

use Kkokk\Poster\Captcha\CaptchaGeneratorInterface;
use Kkokk\Poster\Captcha\Traits\ClickTrait;
use Kkokk\Poster\Exception\PosterException;

class ClickGenerator extends CaptchaGenerator implements CaptchaGeneratorInterface
{
    use ClickTrait;

    protected $configs = [
        'src' => '',
        'im_width' => 256,
        'im_height' => 306,
        'im_type' => 'png', // png 默认 jpg quality 质量
        'quality' => 80,    // jpg quality 质量
        'bg_width' => 256,
        'bg_height' => 256,
        'type' => 'text', // text 汉字 number 数字 alpha_num 字母和数字
        'font_family' => __DIR__ . DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'zhankukuheiti.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件（绝对路径）
        'contents' => '',   // 自定义文字
        'font_size' => 42,  // 字体大小
        'font_count' => 0,  // 字体大小
        'line_count' => 0,  // 干扰线数量
        'char_count' => 0,  // 干扰字符数量
    ];  // 验证码图片配置

    /**
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:06
     * @param array $params
     * @return $this|CaptchaGenerator
     */
    public function config($params = [])
    {
        if (empty($params)) return $this;
        $this->configs['src'] = isset($params['src']) ? $params['src'] : $this->configs['src'];
        $this->configs['im_type'] = isset($params['im_type']) ? $params['im_type'] : $this->configs['im_type'];
        $this->configs['quality'] = isset($params['quality']) ? $params['quality'] : $this->configs['quality'];
        $this->configs['contents'] = isset($params['contents']) ? $params['contents'] : $this->configs['contents'];
        $this->configs['font_family'] = isset($params['font_family']) ? $params['font_family'] : $this->configs['font_family'];
        $this->configs['font_size'] = isset($params['font_size']) ? $params['font_size'] : $this->configs['font_size'];
        $this->configs['font_count'] = isset($params['font_count']) ? $params['font_count'] : $this->configs['font_count'];
        $this->configs['line_count'] = isset($params['line_count']) ? $params['line_count'] : $this->configs['line_count'];
        $this->configs['char_count'] = isset($params['char_count']) ? $params['line_count'] : $this->configs['char_count'];
        if ($this->configs['contents']) $this->configs['font_count'] = mb_strlen($this->configs['contents']);
        return $this;
    }

    public function check($key, $value, $leeway = 0, $secret = null)
    {
        if (!is_array($value)) throw new PosterException('array format required');

        $contents = $this->getCache($key) ?: $secret;

        if (!$contents) return false;

        if (!is_array($contents)) {
            $points = json_decode($contents, true);
        } else {
            $points = $contents;
        }

        if (count($points) != count($value)) return false;

        foreach ($points as $k => $v) {
            $point = $v['point'];

            // 任意坐标点
            $p = [$value[$k]['x'], $value[$k]['y']];
            $p1 = [$point[0], $point[1]]; // 左下
            $p2 = [$point[2], $point[3]]; // 右下
            $p3 = [$point[4], $point[5]]; // 右上
            $p4 = [$point[6], $point[7]]; // 左上

            // 叉积计算 点在四条平行线内部则是在矩形内 p1->p2 p1->p3 参考点 p1  叉积大于0点p3在p2逆时针方向 等于0 三点一线 小于0 点p3在p2顺时针防线
            $isCross = $this->getCross($p1, $p2, $p) * $this->getCross($p3, $p4, $p) >= 0 && $this->getCross($p2, $p3, $p) * $this->getCross($p4, $p1, $p) >= 0;
            if ($isCross) {
                continue;
            } else {
                return false;
            }
        }

        return true;
    }

    public function get($expire = 0)
    {

        $data = $this->draw();

        $this->imOutput(
            $this->im,
            $this->configs['im_type'],
            $this->configs['quality'],
            'click'
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('click' . mt_rand(0, 9999), true);

        $res = [
            'key' => $key,
            'img' => $baseData,
            'content_width' => $data['content_width'],
            'content_height' => $data['content_height'],
            'x' => $data['x'],
            'y' => $data['y'],
        ];

        $setCache = $this->setCache($key, json_encode($data['contents']), $expire);
        if (!$setCache) $res['secret'] = json_encode($data['contents']);

        return $res;
    }

}
