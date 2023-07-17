<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/7
 * Time: 10:55
 */

namespace Kkokk\Poster\Captcha\Generators;

use Kkokk\Poster\Captcha\CaptchaGeneratorInterface;
use Kkokk\Poster\Captcha\Traits\SliderTrait;

class SliderGenerator extends CaptchaGenerator implements CaptchaGeneratorInterface
{
    use SliderTrait;

    protected $configs = [
        'src' => '',
        'im_width' => 340,
        'im_height' => 251,
        'im_type' => 'png', // png 默认 jpg quality 质量
        'quality' => 80,    // jpg quality 质量
        'type' => '4', // 默认四边形 3 三角形 5 五边形 6 六边形
        'bg_width' => 340,
        'bg_height' => 191,
        'slider_width' => 50,
        'slider_height' => 50,
        'slider_border' => 2,
        'slider_bg' => 1,
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
        $this->configs['im_width'] = isset($params['im_width']) ? $params['im_width'] : $this->configs['im_width'];
        $this->configs['im_height'] = isset($params['im_height']) ? $params['im_height'] : $this->configs['im_height'];
        $this->configs['im_type'] = isset($params['im_type']) ? $params['im_type'] : $this->configs['im_type'];
        $this->configs['quality'] = isset($params['quality']) ? $params['quality'] : $this->configs['quality'];
        $this->configs['bg_width'] = isset($params['bg_width']) ? $params['bg_width'] : $this->configs['bg_width'];
        $this->configs['bg_height'] = isset($params['bg_height']) ? $params['bg_height'] : $this->configs['bg_height'];
        $this->configs['slider_width'] = isset($params['slider_width']) ? $params['slider_width'] : $this->configs['slider_width'];
        $this->configs['slider_height'] = isset($params['slider_height']) ? $params['slider_height'] : $this->configs['slider_height'];
        $this->configs['slider_border'] = isset($params['slider_border']) ? $params['slider_border'] : $this->configs['slider_border'];
        $this->configs['slider_bg'] = isset($params['slider_bg']) ? $params['slider_bg'] : $this->configs['slider_bg'];
        $this->configs['type'] = isset($params['type']) ? $params['type'] : $this->configs['type'];

        return $this;
    }

    public function get($expire = 0)
    {

        $data = $this->draw();

        $this->imOutput(
            $this->im,
            $this->configs['im_type'],
            $this->configs['quality'],
            'slider'
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('slider' . mt_rand(0, 9999), true);

        $res = [
            'img' => $baseData,
            'key' => $key,
            'y' => $data['y'],
        ];

        $setCache = $this->setCache($key, $data['x'], $expire);
        if (!$setCache) $res['secret'] = $data['x'];

        return $res;
    }

    /**
     * 判断是否正确
     * 目前使用的是 laravel 的 cache
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/7
     * Time: 11:44
     * @param $key
     * @param $value
     * @param int $leeway
     * @return bool
     */
    public function check($key, $value, $leeway = 0, $secret = null)
    {
        $x = $this->getCache($key) ?: $secret;

        if (empty($x)) return false;

        $leeway = $leeway ?: $this->leeway;

        return $x >= ($value - $leeway) && $x <= ($value + $leeway);
    }

}
