<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 17:12
 */

use Kkokk\Poster\Facades\Html;

require '../../vendor/autoload.php';


$html = <<<eol
<html>
    <head>
        <title>测试</title>
    </head>
    <body>
        <h1>
            测试
        </h1>
    </body>    
</html>
eol;


Html::channel('wk')->load($html)->render();