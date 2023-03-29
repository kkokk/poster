<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:08
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Captcha\Generators\CaptchaGenerator;
use Kkokk\Poster\Captcha\CaptchaGeneratorInterface;
use Kkokk\Poster\Captcha\CaptchaManager;

/**
 * @method static CaptchaGenerator type(string $channel = null)
 * @method static CaptchaGenerator config($params = [])
 * @method static boolean check($key, $value, $leeway = 0, $secret = null)
 * @method static array get($expire = 0)
 *
 * @see CaptchaManager
 */
class Captcha extends Facade
{
    protected static function getFacadeModel()
    {
        return 'captcha';
    }
}