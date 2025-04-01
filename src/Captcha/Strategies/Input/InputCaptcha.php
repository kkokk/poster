<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 16:06
 */

namespace Kkokk\Poster\Captcha\Strategies\Input;

use Kkokk\Poster\Captcha\Strategies\InputCaptchaInterface;
use Kkokk\Poster\Captcha\Traits\InputTrait;

class InputCaptcha extends InputCaptchaStrategy implements InputCaptchaInterface
{
    use InputTrait;

    public function check($key, $value, $leeway = 0, $secret = null)
    {
        $x = $this->cache->pull($key) ?: $secret;
        if (empty($x)) {
            return false;
        }
        return $x == $value;
    }

    public function get($expire = 0)
    {
        $data = $this->draw();
        list($key, $baseData) = $this->create('input');
        $res = [
            'key' => $key,
            'img' => $baseData,
        ];
        $setCache = $this->put($key, $data['value'], $expire ?: $this->expire);
        if (!$setCache) {
            $res['secret'] = $data['value'];
        }
        return $res;
    }
}