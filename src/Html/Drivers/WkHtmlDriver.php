<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 18:09
 */

namespace Kkokk\Poster\Html\Drivers;

use Kkokk\Poster\Exception\PosterException;

/**
 * User: lang
 * 注意 使用的是 -webkit 语法
 * @package Kkokk\Poster\Html\Drivers
 * @class   WkHtmlDriver
 * @author  73285 2023-08-10
 */
class WkHtmlDriver extends Driver implements HtmlDriverInterface
{
    /**
     *  文件类型，常规图片png、jpg等，和pdf
     * @var string
     */
    private $type = 'png';

    private $driver = "wkhtmltoimage";

    private $size = "";

    private $crop = "";

    private $quality = '--quality 100';

    private $transparent = '';

    private $command = '';

    private $tmpHtmlPath;

    private $tmp = false;

    private $tempDirectory;

    private $timeout = 60;

    protected $conversionOutput;

    private $temporaryFiles = [];

    public function load($html)
    {
        $this->html = $html;
    }

    public function size($width = 0, $height = 0)
    {
        $width = $this->normalizeDimension($width);
        $height = $this->normalizeDimension($height);
        $this->size = '';
        if ($width) {
            $this->size .= "--width " . $width;
        }
        if ($height) {
            $this->size .= " --height " . $height;
        }
    }

    public function crop($crop_w = 0, $crop_h = 0, $crop_x = 0, $crop_y = 0)
    {
        $crop_w = $this->normalizeDimension($crop_w);
        $crop_h = $this->normalizeDimension($crop_h);
        $crop_x = $this->normalizeDimension($crop_x);
        $crop_y = $this->normalizeDimension($crop_y);
        $this->crop = '';
        if ($crop_w) {
            $this->crop .= " --crop-w " . $crop_w;
        }
        if ($crop_h) {
            $this->crop .= " --crop-h " . $crop_h;
        }
        if ($crop_x) {
            $this->crop .= " --crop-x " . $crop_x;
        }
        if ($crop_y) {
            $this->crop .= " --crop-y " . $crop_y;
        }
    }

    public function quality($quality)
    {
        $quality = $this->normalizeDimension($quality);
        $quality = max($quality, 0);
        $quality = min($quality, 100);
        $this->quality = '--quality ' . $quality;
    }

    /**
     * 命令中的尺寸只能是有限非负整数；保留数字字符串，拒绝表达式和 shell 片段。
     * @param mixed $value
     * @return int
     * @throws PosterException
     */
    private function normalizeDimension($value)
    {
        if ((!is_int($value) && !is_float($value) && !is_string($value))
            || !is_numeric($value) || !is_finite((float) $value)
            || $value < 0 || $value > PHP_INT_MAX || floor((float) $value) != $value) {
            throw new PosterException('HTML dimensions and quality must be non-negative integers');
        }
        return (int) $value;
    }
    public function setType($type)
    {
        $this->type = strtolower($type);
        $this->driver = $this->type == 'pdf' ? 'wkhtmltopdf' : 'wkhtmltoimage';
    }

    public function output($path, $type = '')
    {
        $this->output = $path;
        if ($type) {
            $this->setType($type);
        }
    }

    public function setTransparent($transparent = true)
    {
        $this->transparent = $transparent ? '--transparent' : '';
    }

    /**
     * 设置原生命令
     * User: lang
     * Date: 2023/8/10
     * Time: 16:35
     * @param $command
     * @return void
     */
    public function setCommand($command)
    {
        $this->command .= ' ' . $command;
    }

    /** 使用已存在、可写的目录；默认使用系统临时目录。 */
    /** @param int|float $seconds 正数秒数，默认60秒。 */
    public function timeout($seconds)
    {
        if ((!is_int($seconds) && !is_float($seconds)) || !is_finite((float) $seconds) || $seconds <= 0) {
            throw new PosterException('HTML timeout must be a positive number');
        }
        $this->timeout = $seconds;
    }
    public function tempDirectory($directory)
    {
        if (!is_string($directory) || !is_dir($directory) || !is_writable($directory)) {
            throw new PosterException('HTML temporary directory must exist and be writable');
        }
        $this->tempDirectory = $directory;
    }

    private function temporaryFile()
    {
        $file = tempnam($this->tempDirectory ?: sys_get_temp_dir(), 'poster_html_');
        if ($file === false) {
            throw new PosterException('Cannot create HTML temporary file');
        }
        $this->temporaryFiles[] = $file;
        return $file;
    }
    private function tmp()
    {
        $this->output = $this->temporaryFile();
        $this->tmp = true;
    }

    /**
     * 失败后立即释放本轮创建的临时文件；成功输出保留到对象销毁供读取。
     * @param array $query
     * @return $this
     */
    public function render($query)
    {
        $offset = count($this->temporaryFiles);

        $completed = false;
        try {
            $result = $this->renderQuery($query);
            $completed = true;
            return $result;
        } finally {
            // 输入 HTML 只供本轮命令使用，不应让临时路径覆盖原始内容。
            if ($this->tmpHtmlPath && in_array($this->tmpHtmlPath, array_slice($this->temporaryFiles, $offset), true)) {
                @unlink($this->tmpHtmlPath);
                $this->tmpHtmlPath = null;
            }
            if (!$completed) {
                foreach (array_slice($this->temporaryFiles, $offset) as $file) {
                    @unlink($file);
                }
            }
            // 成功输出需留给 getImageBlob/getFilePath；由析构负责清理。

        }
    }
    private function renderQuery($query)
    {
        foreach ($query as $v) {
            switch ($v['type']) {
                case 'timeout':
                    $this->timeout(...$v['params']);
                    break;
                case 'tempDirectory':
                    $this->tempDirectory(...$v['params']);
                    break;
                case "load":
                    $this->load(...$v['params']);
                    break;
                case "crop":
                    $this->crop(...$v['params']);
                    break;
                case "quality":
                    $this->quality(...$v['params']);
                    break;
                case "type":
                    $this->setType(...$v['params']);
                    break;
                case "tmp":
                    $this->tmp();
                    break;
                case "output":
                    $this->output(...$v['params']);
                    break;
                case "size":
                    $this->size(...$v['params']);
                    break;
                case "transparent":
                    $this->setTransparent(...$v['params']);
                    break;
                case "command":
                    $this->setCommand(...$v['params']);
                    break;
            }
        }

        // 可用性由退出码判断，程序不保证始终输出版本文本。
        $this->executeCommand($this->driver . ' --version');


        if (preg_match('/<[^>]*>/', $this->html)) {
            $this->tmpHtmlPath = $this->temporaryFile();
            $htmlPath = $this->tmpHtmlPath . '.html';
            if (!rename($this->tmpHtmlPath, $htmlPath)) {
                throw new PosterException('Cannot prepare temporary HTML input');
            }
            $this->tmpHtmlPath = $htmlPath;
            $this->temporaryFiles[] = $htmlPath;
            if (file_put_contents($this->tmpHtmlPath, $this->html) === false) {
                throw new PosterException('Cannot write temporary HTML');
            }

        }

        if ($this->type == 'pdf') {

            if (empty($this->output)) {
                throw new PosterException('Save path cannot be empty');
            }
            $this->quality = '';

        } else {
            if (empty($this->output)) {
                $this->tmp();
            }
        }

        // 与目标同目录，避免跨文件系统移动；失败不能覆盖调用方的旧文件。
        $directory = dirname($this->output);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new PosterException('HTML output directory must exist and be writable');
        }
        $this->conversionOutput = tempnam($directory, 'poster_result_');
        if ($this->conversionOutput === false) {
            throw new PosterException('Cannot create HTML staging output');
        }
        $this->temporaryFiles[] = $this->conversionOutput;
        // tempnam 不带扩展名，图片格式必须显式指定。
        $format = $this->type === 'pdf' ? '' : ' --format ' . escapeshellarg($this->type);
        $command = sprintf("%s %s %s %s %s %s %s %s", $this->driver, $this->command, $this->transparent, $this->size,
            $this->crop, $this->quality . $format, escapeshellarg($this->tmpHtmlPath ?: $this->html), escapeshellarg($this->conversionOutput));

        $this->executeCommand($command);
        clearstatcache(true, $this->conversionOutput);
        if (!is_file($this->conversionOutput) || filesize($this->conversionOutput) === 0) {
            throw new PosterException('HTML conversion produced no output file');
        }
        if (!rename($this->conversionOutput, $this->output)) {
            throw new PosterException('Cannot publish HTML conversion output');
        }

        return $this;
    }

    /**
     * 集中检查退出码；原生命令扩展仍只允许可信配置。
     * @param string $command
     * @return string
     * @throws PosterException
     */
    protected function executeCommand($command)
    {
        // 文件描述符避免 Windows 匿名管道阻塞，轮询只负责进程状态和期限。
        $log = $this->temporaryFile();
        $pipes = [];
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']], $pipes, null, null, ['bypass_shell' => true]);
        if (!is_resource($process)) {
            @unlink($log);
            throw new PosterException('Cannot start HTML command');
        }
        fclose($pipes[0]);
        $started = microtime(true);
        try {
            do {
                $state = proc_get_status($process);
                if (!$state['running']) break;
                if (microtime(true) - $started >= $this->timeout) {
                    proc_terminate($process);
                    throw new PosterException('HTML command timed out after ' . $this->timeout . ' seconds');
                }
                usleep(10000);
            } while (true);
            $output = file_get_contents($log);
            if ($state['exitcode'] !== 0) {
                throw new PosterException('HTML command failed (exit ' . $state['exitcode'] . '): ' . $output);
            }
            return $output;
        } finally {
            proc_close($process);
            @unlink($log);
        }
    }
    public function getImageBlob()
    {
        return file_get_contents($this->output);
    }

    public function getFilePath()
    {
        return $this->output;
    }

    function __destruct()
    {
        // 仅清理自身创建的文件，不能删除调用方提供的输出路径。
        foreach ($this->temporaryFiles as $file) {
            @unlink($file);
        }

    }
}

