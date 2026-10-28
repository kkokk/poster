<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
use Kkokk\Poster\Html\Drivers\WkHtmlDriver;
$directory = sys_get_temp_dir() . '/poster render ' . uniqid();
mkdir($directory);
try {
    foreach (['png', 'pdf'] as $type) {
        $target = $directory . '/output sample.' . $type;
        file_put_contents($target, 'old output');
        $driver = new WkHtmlDriver();
        $driver->load('<!doctype html><html><body style="margin:0;background:white"><h1 style="color:blue">Poster integration</h1><div style="width:100px;height:80px;background:red"></div></body></html>');
        $driver->tempDirectory($directory);
        $driver->timeout(20);
        $driver->output($target, $type);
        $driver->render([]);
        $blob = file_get_contents($target);
        if ($type === 'png') {
            $info = getimagesize($target);
            if (!$info || $info[2] !== IMAGETYPE_PNG) throw new RuntimeException('Invalid PNG');
            echo 'PASS: PNG ' . $info[0] . 'x' . $info[1] . "\n";
        } elseif (substr($blob, 0, 5) !== '%PDF-') {
            throw new RuntimeException('Invalid PDF');
        } else echo "PASS: PDF signature\n";
        $driver->render([]);
        unset($driver);
        unlink($target);
    }
    $result = Kkokk\Poster\Facades\Html::fresh()->load('<html><body>Builder integration</body></html>')
        ->tempDirectory($directory)->timeout(20)->type('png')->render();
    if (substr($result->getImageBlob(), 0, 8) !== "\x89PNG\r\n\x1a\n") {
        throw new RuntimeException('Builder result is not PNG');
    }
    $temporaryOutput = $result->getFilePath();
    unset($result);
    if (is_file($temporaryOutput)) throw new RuntimeException('Builder output leaked');
    echo "PASS: public Builder configuration and default output cleanup\n";
    if (glob($directory . '/*')) throw new RuntimeException('Temporary files leaked');
    echo "PASS: existing target replacement, repeated render and cleanup\n";
} finally {
    foreach (glob($directory . '/*') as $file) unlink($file);
    rmdir($directory);
}