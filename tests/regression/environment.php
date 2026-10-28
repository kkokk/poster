<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
function checkEnvironment($condition, $message) { if (!$condition) throw new RuntimeException($message); }
$diagnostic = new Kkokk\Poster\Diagnostics\Environment();
if (isset($argv[1]) && $argv[1] === 'without-extensions') {
    $report = $diagnostic->check();
    checkEnvironment(!$report['ready'] && $report['checks']['extension.gd']['status'] === 'error', 'Missing extension not detected');
    echo "PASS: missing required extensions reported without fatal errors\n";
    exit(0);
}
$report = $diagnostic->check();
checkEnvironment(isset($report['checks']['php'], $report['checks']['font.0']), 'Default checks absent');
$missing = sys_get_temp_dir() . '/poster_diagnostic_missing_' . uniqid();
$report = $diagnostic->check(['fonts'=>[$missing], 'temporaryDirectory'=>$missing, 'binaries'=>['wkhtmltopdf'=>$missing]]);
checkEnvironment(!$report['ready'] && $report['checks']['font.0']['status'] === 'error'
    && $report['checks']['temporaryDirectory']['status'] === 'error'
    && $report['checks']['html.binary.wkhtmltopdf']['status'] === 'warning', 'Missing paths misclassified');
checkEnvironment(!file_exists($missing), 'Diagnostic created files');
// 普通文本假程序也只进行文件检查，不能触发执行。
$file = tempnam(sys_get_temp_dir(), 'poster_diag_');
try {
    file_put_contents($file, 'not an executable');
    $report = $diagnostic->check(['fonts'=>[$file], 'binaries'=>['fixture'=>$file]]);
    checkEnvironment($report['checks']['font.0']['status'] === 'ok', 'Readable font path rejected');
    checkEnvironment(strpos(json_encode($report), 'fixture') !== false, 'Report not serializable');
} finally { unlink($file); }
foreach ([['fonts'=>'bad'], ['fonts'=>['https://example.com/font']], ['temporaryDirectory'=>"bad\0path"], ['typo'=>true]] as $options) {
    try { $diagnostic->check($options); throw new RuntimeException('Invalid options accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
}
$status=0;
passthru(escapeshellarg(PHP_BINARY).' -n '.escapeshellarg(__FILE__).' without-extensions', $status);
checkEnvironment($status===0, 'Minimal PHP process diagnostic failed');
echo "PASS: environment report, missing paths, input validation and no execution\n";
