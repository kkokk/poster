<?php

namespace Kkokk\Poster\Diagnostics;

use Kkokk\Poster\Exception\PosterException;

/** 只检查当前PHP进程的能力，不执行转换程序或连接外部服务。 */
class Environment
{
    /**
     * @param array $options fonts路径数组、temporaryDirectory、binaries名称到路径数组
     * @return array ready只表示必需静态检查通过，非完整渲染/转换成功
     * @throws PosterException
     */
    public function check($options = [])
    {
        if (!is_array($options) || array_diff(array_keys($options), ['fonts','temporaryDirectory','binaries'])) {
            throw new PosterException('Unknown environment diagnostic options');
        }
        $fonts = isset($options['fonts']) ? $options['fonts'] : [dirname(__DIR__) . '/style/simkai.ttf'];
        $directory = isset($options['temporaryDirectory']) ? $options['temporaryDirectory'] : sys_get_temp_dir();
        $binaries = isset($options['binaries']) ? $options['binaries'] : [];
        if (!is_array($fonts) || !is_array($binaries) || !is_string($directory) || $directory === '') {
            throw new PosterException('Invalid environment diagnostic paths');
        }
        foreach (array_merge(array_values($fonts), array_values($binaries)) as $path) {
            if (!is_string($path) || $path === '' || strpos($path, "\0") !== false || strpos($path, '://') !== false) {
                throw new PosterException('Environment diagnostics requires local file paths');
            }
        }
        if (strpos($directory, "\0") !== false || strpos($directory, '://') !== false) {
            throw new PosterException('Environment diagnostics requires a local temporary directory');
        }
        $checks = [];
        $this->add($checks, 'php', version_compare(PHP_VERSION, '5.6.0', '>='), true,
            PHP_VERSION, '使用满足composer声明的PHP版本；此检查不代表全部版本已通过测试。');
        foreach (['gd','mbstring','iconv'] as $extension) {
            $this->add($checks, 'extension.' . $extension, extension_loaded($extension), true,
                $extension, '为当前CLI/FPM进程安装并启用对应扩展，重启服务后复查。');
        }
        foreach (['imagettfbbox','imagettftext'] as $function) {
            $this->add($checks, 'function.' . $function, is_callable($function), true,
                $function, '安装包含FreeType的GD，并检查禁用函数配置。');
        }
        foreach (['imagick','redis'] as $extension) {
            $this->add($checks, 'optional.' . $extension, extension_loaded($extension), false,
                $extension, '仅使用该驱动/缓存后端时安装对应扩展；扩展存在不代表服务可用。');
        }
        $this->add($checks, 'optional.predis', class_exists('Predis\\Client'), false,
            'Predis\\Client', '仅使用Predis缓存时安装客户端；此处不连接Redis服务。');
        $clusters = [];
        $grapheme = preg_match_all('/\X/u', "e\xCC\x81", $clusters);
        $this->add($checks, 'encoding.grapheme', $grapheme === 1, false,
            defined('PCRE_VERSION') ? PCRE_VERSION : 'unknown', '字素模式需要Unicode PCRE；此样例不验证所有Unicode序列。');
        $utf8 = is_callable('mb_check_encoding') && mb_check_encoding("\xE4\xB8\xAD", 'UTF-8');
        $this->add($checks, 'encoding.utf8', $utf8, true, 'UTF-8', '启用mbstring并使用有效UTF-8输入。');
        foreach (array_values($fonts) as $index => $font) {
            $this->add($checks, 'font.' . $index, is_file($font) && is_readable($font), true,
                $font, '提供当前进程可读的字体文件；可读不代表字体有效或覆盖所需字形。');
        }
        $this->add($checks, 'temporaryDirectory', is_dir($directory) && is_writable($directory), true,
            $directory, '指定已存在且当前进程可写的临时目录；未实际写文件，配额和运行时权限仍需验证。');
        $process = is_callable('proc_open') && is_callable('proc_get_status') && is_callable('proc_terminate') && is_callable('proc_close');
        $this->add($checks, 'html.process', $process, false, 'proc_open/status/terminate/close',
            '仅HTML转换需要进程函数，请检查当前SAPI的禁用函数设置。');
        foreach ($binaries as $name => $path) {
            $available = is_file($path) && is_readable($path) && (DIRECTORY_SEPARATOR === '\\' || is_executable($path));
            $this->add($checks, 'html.binary.' . $name, $available, false, $path,
                '配置本机转换程序路径及PATH；此处未执行程序，不验证版本、依赖或实际转换。');
        }
        $ready = true;
        foreach ($checks as $check) if ($check['status'] === 'error') $ready = false;
        return ['ready' => $ready, 'phpVersion' => PHP_VERSION, 'sapi' => PHP_SAPI, 'checks' => $checks];
    }

    protected function add(&$checks, $name, $available, $required, $value, $advice)
    {
        $checks[$name] = ['status' => $available ? 'ok' : ($required ? 'error' : 'warning'),
            'required' => $required, 'value' => $value, 'advice' => $available ? '' : $advice];
    }
}
