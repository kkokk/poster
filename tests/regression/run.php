<?php

// 每个脚本独立进程，隔离函数名、Session、错误处理器和测试替身。
$groups = [
    'core' => ['core_defects', 'gd_polygon_thickness', 'cache_namespace', 'cache_write_failure', 'remote_image', 'html_timeout', 'html_temporary', 'environment'],
    'image' => ['task_isolation', 'image_copy', 'image_fit', 'mask_cutout_alpha', 'cutout_native', 'rounded_alpha', 'gradient', 'text_debug'],
    'http' => ['remote_image_http'],
    'html' => ['html_render'],
    'redis' => ['redis_pull', 'redis_pull_concurrent'],
];
$selected = isset($argv[1]) ? $argv[1] : 'core';
if ($selected === '--list') {
    foreach ($groups as $name => $scripts) echo $name . ': ' . implode(', ', $scripts) . "\n";
    exit(0);
}
if (!isset($groups[$selected])) {
    fwrite(STDERR, "Unknown group. Use --list.\n");
    exit(2);
}
$failed = [];
foreach ($groups[$selected] as $script) {
    echo PHP_EOL . '>>> ' . $script . "\n";
    $status = 0;
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/' . $script . '.php'), $status);
    if ($status !== 0) $failed[] = $script;
}
echo "\nGroup: " . $selected . '; failed: ' . count($failed) . "\n";
if ($failed) echo implode(', ', $failed) . "\n";
exit($failed ? 1 : 0);
