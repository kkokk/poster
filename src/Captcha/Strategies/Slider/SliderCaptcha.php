<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 17:32
 */

namespace Kkokk\Poster\Captcha\Strategies\Slider;

use Kkokk\Poster\Captcha\Strategies\InputCaptchaInterface;
use Kkokk\Poster\Captcha\Traits\SliderTrait;

class SliderCaptcha extends SliderCaptchaStrategy implements InputCaptchaInterface
{
    use SliderTrait;

    public function check($key, $value, $leeway = 0, $secret = null)
    {
        $x = $this->cache->pull($key) ?: $secret;
        if (empty($x)) {
            return false;
        }
        $leeway = $leeway ?: $this->leeway;
        return $x >= ($value - $leeway) && $x <= ($value + $leeway);
    }

    public function get($expire = 0)
    {
        $data = $this->draw();
        list($key, $baseData) = $this->create('slider');

        $res = [
            'img' => $baseData,
            'key' => $key,
            'y'   => $data['y'],
        ];

        $setCache = $this->put($key, $data['x'], $expire);
        if (!$setCache) {
            $res['secret'] = $data['x'];
        }
        return $res;
    }
}