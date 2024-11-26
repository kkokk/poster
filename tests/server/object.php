<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Gd\Canvas;

require '../../vendor/autoload.php';

$color = "#fffffff0";
$rgb = sscanf($color, "#%02x%02x%02x%02x");

var_dump($rgb);
