<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/9
 * Time: 15:44
 */

namespace Kkokk\Poster\Captcha\Generators;

use Kkokk\Poster\Captcha\CaptchaGeneratorInterface;
use Kkokk\Poster\Captcha\Traits\RotateTrait;

class RotateGenerator extends CaptchaGenerator implements CaptchaGeneratorInterface
{
    use RotateTrait;

    protected $configs = [
        'src' => '',
        'im_width' => 350,
        'im_height' => 350,
        'im_type' => 'png', // png 默认 jpg quality 质量
        'quality' => 80,    // jpg quality 质量
    ];  // 验证码图片配置

    /**
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/29
     * Time: 15:05
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
        return $this;
    }

    public function check($key, $value, $leeway = 3, $secret = null)
    {
        $x = $this->getCache($key) ?: $secret;

        if (empty($x)) return false;

        $leeway = $leeway ?: $this->leeway;

        return $x >= ($value - $leeway) && $x <= ($value + $leeway);
    }

    public function get($expire = 0)
    {
        $data = $this->draw();

        $this->imOutput(
            $this->im,
            $this->configs['im_type'],
            $this->configs['quality'],
            'rotate'
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('rotate' . mt_rand(0, 9999), true);

        $res = [
            'img' => $baseData,
            'key' => $key,
        ];

        $setCache = $this->setCache($key, $data['angle'], $expire);
        if (!$setCache) $res['secret'] = $data['angle'];

        return $res;
    }
}