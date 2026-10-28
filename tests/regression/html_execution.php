<?php
require __DIR__ . '/html_parameters.php';
class HtmlExecutionProbe extends Kkokk\Poster\Html\Drivers\WkHtmlDriver {
    public $commands = [];
    public $target;
    public $writeOutput = true;
    protected function executeCommand($command) {
        $this->commands[] = $command;
        if (strpos($command, '--version') !== false) return 'test converter';
        if ($this->writeOutput) file_put_contents($this->conversionOutput, 'test output');
        return '';
    }
    public function realExecute($command) { return parent::executeCommand($command); }
}
$p = new HtmlExecutionProbe();
$p->target = sys_get_temp_dir() . '/poster html ' . uniqid() . '.png';
try {
    $p->load('input with spaces.html');
    $p->output($p->target);
    $p->render([]);
    $command = $p->commands[1];
    htmlParameterCheck(strpos($command, escapeshellarg('input with spaces.html')) !== false);
    htmlParameterCheck(is_file($p->target));
    file_put_contents($p->target, 'old result');
    $p->writeOutput = false;
    try { $p->render([]); throw new RuntimeException('Missing output accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
    htmlParameterCheck(file_get_contents($p->target) === 'old result');
    try {
        $p->realExecute(escapeshellarg(PHP_BINARY) . ' -r ' . escapeshellarg('exit(7);'));
        throw new RuntimeException('Nonzero exit accepted');
    } catch (Kkokk\Poster\Exception\PosterException $e) {
        htmlParameterCheck(strpos($e->getMessage(), 'exit 7') !== false);
    }
} finally {
    if (is_file($p->target)) unlink($p->target);
}
echo "PASS: HTML path quoting, missing output and process failure\n";