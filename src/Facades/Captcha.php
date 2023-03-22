<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:08
 */

namespace Kkokk\Poster\Facades;
use Kkokk\Poster\Interfaces\MyCaptcha;
use Kkokk\Poster\Lang\Captcha as CaptchaManager;
/**
 * @method static CaptchaManager type(string $channel = null)
 * @method static MyCaptcha config($params = [])
 * @method static MyCaptcha check($key , $value , $leeway = 0, $secret = null)
 * @method static MyCaptcha get($expire = 0)
 *
 * @see CaptchaManager
 */
class Captcha extends Facade
{
    protected static function getFacadeModel(){
        return 'captcha';
    }
}