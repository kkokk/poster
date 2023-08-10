<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/12
 * Time: 11:47
 */

namespace Kkokk\Poster\Captcha\Generators;

use Kkokk\Poster\Captcha\CaptchaGeneratorInterface;
use Kkokk\Poster\Captcha\Traits\InputTrait;

class InputGenerator extends CaptchaGenerator implements CaptchaGeneratorInterface
{
    use InputTrait;

    protected $configs = [
        'src' => '',
        'im_width' => 256,
        'im_height' => 64,
        'im_type' => 'png', // png 默认 jpg quality 质量
        'quality' => 80,    // jpg quality 质量
        'type' => 'number', // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        'font_family' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'simkai.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件
        'font_size' => 32, // 字体大小
        'font_count' => 4,  // 字体长度
        'line_count' => 5,  // 干扰线数量
        'char_count' => 10,  // 干扰字符数量
    ];

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
        $this->configs['im_width'] = isset($params['im_width']) ? $params['im_width'] : $this->configs['im_width'];
        $this->configs['im_height'] = isset($params['im_height']) ? $params['im_height'] : $this->configs['im_height'];
        $this->configs['im_type'] = isset($params['im_type']) ? $params['im_type'] : $this->configs['im_type'];
        $this->configs['quality'] = isset($params['quality']) ? $params['quality'] : $this->configs['quality'];
        $this->configs['type'] = isset($params['type']) ? $params['type'] : $this->configs['type'];
        $this->configs['font_family'] = isset($params['font_family']) ? $params['font_family'] : $this->configs['font_family'];
        $this->configs['font_size'] = isset($params['font_size']) ? $params['font_size'] : $this->configs['font_size'];
        $this->configs['font_count'] = isset($params['font_count']) ? $params['font_count'] : $this->configs['font_count'];
        $this->configs['line_count'] = isset($params['line_count']) ? $params['line_count'] : $this->configs['line_count'];
        $this->configs['char_count'] = isset($params['char_count']) ? $params['char_count'] : $this->configs['char_count'];
        return $this;
    }

    public function check($key, $value, $leeway = 0, $secret = null)
    {
        $x = $this->getCache($key) ?: $secret;

        if (empty($x)) return false;

        return $x == $value;
    }

    public function get($expire = 0)
    {
        $data = $this->draw();

        $this->imOutput(
            $this->im,
            $this->configs['im_type'],
            $this->configs['quality'],
            'input'
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('input:' . $this->configs['type'] . mt_rand(0, 9999), true);

        $res = [
            'key' => $key,
            'img' => $baseData,
        ];

        $setCache = $this->setCache($key, $data['value'], $expire);
        if (!$setCache) $res['secret'] = $data['value'];

        return $res;
    }
}
