<?php
require __DIR__ . '/html_execution.php';
$d = new HtmlExecutionProbe();
$d->tempDirectory(sys_get_temp_dir());
$d->load('<html>test</html>');
$d->output(sys_get_temp_dir() . '/unused_' . uniqid() . '.png');
$d->writeOutput = false;
try { $d->render([]); } catch (Kkokk\Poster\Exception\PosterException $e) {}
$property = new ReflectionProperty('Kkokk\Poster\Html\Drivers\WkHtmlDriver', 'temporaryFiles');
$property->setAccessible(true);
$files = $property->getValue($d);
htmlParameterCheck(count($files) >= 1 && !is_file($files[0]));
unset($d);
htmlParameterCheck(!is_file($files[0]));
try {
    (new Kkokk\Poster\Html\Drivers\WkHtmlDriver())->tempDirectory('/missing_' . uniqid());
    throw new RuntimeException('Missing directory accepted');
} catch (Kkokk\Poster\Exception\PosterException $e) {}
echo "PASS: HTML temporary ownership and destructor cleanup\n";