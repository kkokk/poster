<?php
/**
 * User: lang
 * Date: 2023/8/25
 * Time: 10:15
 */

require '../../vendor/autoload.php';

use Kkokk\Poster\Facades\Captcha;

$res = Captcha::type('input')->config(['debug' => true])->get();

print_r($res);