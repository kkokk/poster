<?php
/**
 * User: lang
 * Date: 2024/3/12
 * Time: 13:29
 */
require '../vendor/autoload.php';

use Kkokk\Poster\Facades\Poster;
use Kkokk\Poster\Facades\Html;

$html = '<p><img src="https://img.alicdn.com/imgextra/i3/2216558867983/O1CN01x1zaRA28qGize0z8Z-2216558867983.png" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i3/2216558867983/O1CN018Wkbza28qGgEw9zFc-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i1/2216558867983/O1CN012kv2PV28qGgOLWevq-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i4/2216558867983/O1CN01wkGs3928qGgQ33mpL-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i2/2216558867983/O1CN01aKQZLP28qGgLIwWCo-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i4/2216558867983/O1CN01y16VA428qGgRQVuEa-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i4/2216558867983/O1CN01FPc1GR28qGgTR2N2e-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i2/2216558867983/O1CN01wXODo528qGgNH6UOJ-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i4/2216558867983/O1CN01fKlq4w28qGgKjeXRu-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i3/2216558867983/O1CN01VNiW7U28qGgKjfXre-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i3/2216558867983/O1CN0137I51828qGgNGJssR-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i3/2216558867983/O1CN01LKBile28qGgSe9v8m-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i3/2216558867983/O1CN01tEqT4328qGgjTggJE-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/><img src="https://img.alicdn.com/imgextra/i3/2216558867983/O1CN01wOhqA728qGgnrcyi9-2216558867983.jpg" style="max-width:100%;" contenteditable="false"/></p>';

$chunkHeight = 1500;

$poster = Poster::extension('imagick')->buildImDst(Html::load($html)->size(480)->render());
$imInfo = $poster->getImInfo();
$tmpIm = $poster->tmp();
$width = $imInfo['width'];
$height = $imInfo['height'];
$count = ceil($height / $chunkHeight);
for ($i = 0; $i < $count; $i ++) {
    $y =  $chunkHeight * $i;
    if($i + 1 == $count) {
        $chunkHeight = $height - $y;
    }
    $crop = Poster::buildImDst($tmpIm)->crop(0, $y,$width, $chunkHeight)->getPoster(__DIR__ . '/crop/' . $i . '.png');
}
