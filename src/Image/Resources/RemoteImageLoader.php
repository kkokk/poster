<?php

namespace Kkokk\Poster\Image\Resources;

use Kkokk\Poster\Exception\PosterException;

/** 两驱动共用下载结果，避免 GD 为尺寸与解码重复请求远程素材。 */
class RemoteImageLoader
{
    private $timeout;
    private $maxBytes;
    private $maxPixels;
    private $allowedHosts;

    /** 白名单只约束主机名称，不构成 DNS/IP 级 SSRF 防护。 */
    public function __construct($timeout = 15, $maxBytes = 20971520, $maxPixels = 40000000, $allowedHosts = null)
    {
        foreach ([$timeout, $maxBytes, $maxPixels] as $value) {
            if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value) || $value <= 0) {
                throw new PosterException('Remote image limits must be positive numbers');
            }
        }
        if (floor($maxBytes) != $maxBytes || $maxBytes >= PHP_INT_MAX || floor($maxPixels) != $maxPixels) {
            throw new PosterException('Remote image byte and pixel limits must be integers');
        }
        if ($allowedHosts !== null && !is_array($allowedHosts)) {
            throw new PosterException('Remote image hosts must be an array or null');
        }
        $this->timeout = $timeout;
        $this->maxBytes = (int) $maxBytes;
        $this->maxPixels = $maxPixels;
        $this->allowedHosts = $allowedHosts === null ? null : array_map('strtolower', $allowedHosts);
    }
    public function load($url)
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new PosterException('Remote image requires HTTP or HTTPS');
        }
        if ($this->allowedHosts !== null && !in_array(strtolower((string) parse_url($url, PHP_URL_HOST)), $this->allowedHosts, true)) {
            throw new PosterException('Remote image host is not allowed');
        }
        $context = stream_context_create(['http' => [
            'timeout' => $this->timeout,
            'header' => 'Connection: close',
            'max_redirects' => 5,
            'follow_location' => $this->allowedHosts === null ? 1 : 0,
        ]]);
        $stream = @fopen($url, 'rb', false, $context);
        if ($stream === false) {
            throw new PosterException('Cannot open remote image');
        }
        try {
            // 多读一个字节辨别超限，不依赖可能缺失或不可信的 Content-Length。
            $blob = '';
            $deadline = microtime(true) + $this->timeout;
            while (!feof($stream) && strlen($blob) <= $this->maxBytes) {
                $remaining = $deadline - microtime(true);
                if ($remaining <= 0) {
                    throw new PosterException('Remote image download timed out');
                }
                stream_set_timeout($stream, (int) $remaining, (int) (($remaining - floor($remaining)) * 1000000));
                $chunk = fread($stream, min(8192, $this->maxBytes + 1 - strlen($blob)));
                $state = stream_get_meta_data($stream);
                if ($chunk === false || !empty($state['timed_out'])) {
                    throw new PosterException('Remote image read failed or timed out');
                }
                if ($chunk === '' && !feof($stream)) {
                    // TLS/HTTP流短暂空读不等于失败，仍由总期限约束等待。
                    usleep(10000);
                    continue;
                }
                $blob .= $chunk;
            }
            $meta = stream_get_meta_data($stream);
            if ($blob === false || !empty($meta['timed_out'])) {
                throw new PosterException('Remote image read failed or timed out');
            }
            return $this->validate($blob);
        } finally {
            fclose($stream);
        }
    }

    /** 在驱动解码前限制常见位图体积和像素；不等于完整解压内存上限。 */
    public function validate($blob)
    {
        if (strlen($blob) > $this->maxBytes) {
            throw new PosterException('Remote image exceeds byte limit');
        }
        $info = @getimagesizefromstring($blob);
        if (!$info || $info[0] <= 0 || $info[1] <= 0 || $info[0] > $this->maxPixels / $info[1]) {
            throw new PosterException('Remote image is invalid or exceeds pixel limit');
        }
        return $blob;
    }
}