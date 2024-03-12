<?php
/**
 * User: lang
 * Date: 2023/8/22
 * Time: 10:57
 */

use Kkokk\Poster\Facades\Poster;
require '../../vendor/autoload.php';

$path = __DIR__ . '/../poster/1.png';
$id = "1001";
$qr = Poster::Qr('http://www.baidu.com?in=' . $id, $path);
print_r($qr);