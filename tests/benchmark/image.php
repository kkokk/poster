<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });

function benchmarkFixture($size, $mask = false)
{
    $image = imagecreatetruecolor($size, $size);
    imagealphablending($image, false); imagesavealpha($image, true);
    for ($y=0; $y<$size; $y++) for ($x=0; $x<$size; $x++) {
        $gray = (int) round(255*$x/($size-1));
        $color = $mask ? imagecolorallocatealpha($image,$gray,$gray,$gray,0)
            : imagecolorallocatealpha($image,($x*3)%256,($y*5)%256,($x+$y)%256,($x+$y)%128);
        imagesetpixel($image,$x,$y,$color);
    }
    ob_start(); imagepng($image); $blob=ob_get_clean(); imagedestroy($image);
    return $blob;
}

function benchmarkWorker($driver, $operation, $size)
{
    if (!in_array($driver,['Gd','Imagick'],true) || !in_array($operation,['circle','mask','cutout','cutout_all'],true)) throw new RuntimeException('Unknown case');
    $class='Kkokk\\Poster\\Image\\'.$driver.'\\Image';
    $image=new $class(benchmarkFixture($size));
    $mask=benchmarkFixture($size,true);
    $predicate=function ($point) { return ($point[0]+$point[1])%3 !== 0; };
    $start=microtime(true);
    if ($operation==='circle') $output=$image->circle();
    elseif ($operation==='mask') $output=$image->applyMask($mask);
    elseif ($operation==='cutout_all') $output=$image->cutout(0,0,$size,$size);
    else $output=$image->cutout(0,0,$size,$size,$predicate);
    $elapsed=(microtime(true)-$start)*1000;
    // 峰值在编码与像素摘要之前读取，但包含装载及固定素材构造；不代表原生库RSS。
    $peak=memory_get_peak_usage(true);
    $decoded=imagecreatefromstring($output->blob()); imagepalettetotruecolor($decoded);
    if (imagesx($decoded)!==$size || imagesy($decoded)!==$size) throw new RuntimeException('Unexpected dimensions');
    $hash=hash_init('sha256'); $transparent=0; $partial=0;
    for ($y=0; $y<$size; $y++) for ($x=0; $x<$size; $x++) {
        $c=imagecolorsforindex($decoded,imagecolorat($decoded,$x,$y));
        hash_update($hash,pack('C4',$c['red'],$c['green'],$c['blue'],$c['alpha']));
        if ($c['alpha']===127) $transparent++;
        elseif ($c['alpha']>0) $partial++;
    }
    imagedestroy($decoded);
    return ['milliseconds'=>$elapsed,'phpPeakBytes'=>$peak,'pixelSha256'=>hash_final($hash),
        'width'=>$size,'height'=>$size,'transparentPixels'=>$transparent,'partialAlphaPixels'=>$partial];
}

try {
    if (isset($argv[1]) && $argv[1]==='--worker') {
        if (count($argv)!==5 || !ctype_digit($argv[4]) || (int)$argv[4]<8 || (int)$argv[4]>1024) throw new RuntimeException('Invalid worker size');
        echo json_encode(benchmarkWorker($argv[2],$argv[3],(int)$argv[4])); exit(0);
    }
    $size=isset($argv[1])?$argv[1]:'256'; $iterations=isset($argv[2])?$argv[2]:'3';
    if (count($argv)>3 || !ctype_digit($size) || !ctype_digit($iterations) || $size<8 || $size>1024 || $iterations<1 || $iterations>10) {
        throw new RuntimeException('Usage: php tests/benchmark/image.php [size:8..1024=256] [iterations:1..10=3]');
    }
    $report=['phpVersion'=>PHP_VERSION,'os'=>PHP_OS,'size'=>(int)$size,'iterations'=>(int)$iterations,
        'imagickVersion'=>class_exists('Imagick')?Imagick::getVersion():null,'cases'=>[]];
    foreach (['Gd','Imagick'] as $driver) foreach (['circle','mask','cutout','cutout_all'] as $operation) {
        $samples=[]; $times=[]; $peaks=[]; $signature=null;
        for ($i=0;$i<(int)$iterations;$i++) {
            $command=escapeshellarg(PHP_BINARY).' '.escapeshellarg(__FILE__).' --worker '.escapeshellarg($driver).' '.escapeshellarg($operation).' '.(int)$size;
            $lines=[]; $status=0; exec($command,$lines,$status);
            $sample=json_decode(implode("\n",$lines),true);
            if ($status!==0 || !is_array($sample)) throw new RuntimeException('Worker failed: '.$driver.' '.$operation);
            if ($signature!==null && $signature!==$sample['pixelSha256']) throw new RuntimeException('Non-deterministic output: '.$driver.' '.$operation);
            $signature=$sample['pixelSha256']; $samples[]=$sample; $times[]=$sample['milliseconds']; $peaks[]=$sample['phpPeakBytes'];
        }
        sort($times); $count=count($times); $middle=(int)floor($count/2);
        $median=$count%2?$times[$middle]:($times[$middle-1]+$times[$middle])/2;
        $report['cases'][$driver.'.'.$operation]=['medianMilliseconds'=>$median,'minMilliseconds'=>min($times),
            'maxMilliseconds'=>max($times),'maxPhpPeakBytes'=>max($peaks),'samples'=>$samples];
    }
    echo json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
} catch (Exception $e) {
    fwrite(STDERR,$e->getMessage().PHP_EOL); exit(1);
}
