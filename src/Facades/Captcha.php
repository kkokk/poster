<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:08
 */

namespace Kkokk\Poster\Facades;

/**
 * @method static \Kkokk\Poster\Interfaces\MyCaptcha type(string $channel = null)
 * @method static object config($params = [])
 * @method static boolean check($key , $value , $leeway = 0, $secret = null)
 * @method static array get($expire = 0)
 *
 * @see \Kkokk\Poster\Lang\Captcha
 */
class Captcha extends Facade
{
    protected static function getFacadeModel(){
        return 'captcha';
    }
}