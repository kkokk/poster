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
        $x = $this->pull($key);
        // 缓存未命中才使用服务端备用答案，0 和 "0" 都是有效答案。
        if ($x === null || $x === false || $x === '') {
            $x = $secret;
        }
        if ($x === null || $x === false || $x === '') {
            return false;
        }
        // 防止零答案与布尔值、空字符串等发生宽松比较而误通过。
        if ((!is_string($value) && !is_int($value) && !is_float($value)) || $value === '') {
            return false;
        }
        if (($x === 0 || $x === '0') && !is_numeric($value)) {
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
