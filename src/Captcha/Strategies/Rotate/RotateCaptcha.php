<?php
/**
 * User: lang
 * Date: 2025/4/2
 * Time: 13:49
 */

namespace Kkokk\Poster\Captcha\Strategies\Rotate;

use Kkokk\Poster\Captcha\Strategies\RotateCaptchaInterface;
use Kkokk\Poster\Captcha\Traits\RotateTrait;

class RotateCaptcha extends RotateCaptchaStrategy implements RotateCaptchaInterface
{
    use RotateTrait;

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
        list($key, $baseData) = $this->create('rotate');
        $res = [
            'img' => $baseData,
            'key' => $key,
        ];

        $setCache = $this->put($key, $data['angle'], $expire);
        if (!$setCache) {
            $res['secret'] = $data['angle'];
        }

        return $res;
    }
}