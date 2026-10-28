<?php
require __DIR__ . '/html_execution.php';
$p = new HtmlExecutionProbe();
$p->timeout(0.15);
$start = microtime(true);
try {
    $p->realExecute(escapeshellarg(PHP_BINARY) . ' -r ' . escapeshellarg('sleep(3);'));
    throw new RuntimeException('Timeout not enforced');
} catch (Kkokk\Poster\Exception\PosterException $e) {
    htmlParameterCheck(strpos($e->getMessage(), 'timed out') !== false);
    htmlParameterCheck(microtime(true) - $start < 2);
}
$p->timeout(5);
htmlParameterCheck($p->realExecute(escapeshellarg(PHP_BINARY) . ' -r ' . escapeshellarg('echo chr(111),chr(107);')) === 'ok');
foreach ([0, -1, INF, NAN, '3'] as $invalid) {
    try { $p->timeout($invalid); throw new RuntimeException('Invalid timeout accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
}
echo "PASS: real process timeout and successful completion\n";