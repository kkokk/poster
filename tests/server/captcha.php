<?php
/**
 * User: lang
 * Date: 2023/8/25
 * Time: 10:15
 */

require '../../vendor/autoload.php';

use Kkokk\Poster\Facades\Captcha;

$res = Captcha::extension('imagick')->type('input')->config(['debug' => true])->get();

echo $res['secret'];
echo "<img src='{$res['img']}' />";