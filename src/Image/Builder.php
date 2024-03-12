<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 18:10
 */

namespace Kkokk\Poster\Image;


use Kkokk\Poster\Image\Queries\Query;

class Builder
{
    public $extension;

    /** @var Query 合成参数 */
    public $query;

    // 画布
    public $im = [];

    /** @var array 基础配置 */
    public $configs = [];

    /** @var array 图片组 */
    public $images = [];

    /** @var array 文字组 */
    public $texts = [];

    /** @var array 二维码组 */
    public $qrs = [];

    /** @var array 背景组 */
    public $bgs = [];

    /** @var array 线组 */
    public $lines = [];

    /** @var array 圆组 */
    public $arcs = [];

    /** @var string */
    public $path = null;

    public function __construct(ExtensionInterface $extension, Query $query, $path = null)
    {
        $this->extension = $extension;
        $this->query = $query;
        $this->path = $path;
        $this->query->setPath($path);
    }

    public function config($params = [])
    {
        $this->configs = $params;
        $this->query->setQuery('config', $this->configs);
        return $this;
    }

    public function buildIm($w, $h, $rgba = [255, 255, 255, 1], $alpha = false)
    {
        $this->im = [$w, $h, $rgba, $alpha];
        $this->query->setQuery('im', $this->im);
        return $this;
    }

    public function buildImDst($src, $w = 0, $h = 0)
    {
        $this->im = [$src, $w, $h];
        $this->query->setQuery('imDst', $this->im);
        return $this;
    }

    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, \Closure $callback = null)
    {
        $query = [];
        if ($callback) {
            $that = clone $this;
            $that->query->clearQuery(); // 清理 query
            $callback($that);
            $query = $that->query->getQuery(); // 获取闭包内生成的query
            unset($that);
        }

        $bg = [$w, $h, $rgba, $alpha, $dst_x, $dst_y, $src_x, $src_y, $query];
        $this->query->setQuery('bg', $bg);
        return $this;
    }

    public function buildImage($src, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $alpha = false, $type = 'normal')
    {
        $this->setImages($src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $alpha, $type);
        return $this;
    }

    public function buildImageMany($params = [])
    {
        foreach ($params as $value) {
            $value['dst_x'] = isset($value['dst_x']) ? $value['dst_x'] : 0;
            $value['dst_y'] = isset($value['dst_y']) ? $value['dst_y'] : 0;
            $value['src_x'] = isset($value['src_x']) ? $value['src_x'] : 0;
            $value['src_y'] = isset($value['src_y']) ? $value['src_y'] : 0;
            $value['src_w'] = isset($value['src_w']) ? $value['src_w'] : 0;
            $value['src_h'] = isset($value['src_h']) ? $value['src_h'] : 0;
            $value['alpha'] = isset($value['alpha']) ?: false;
            $value['type'] = isset($value['type']) ? $value['type'] : 'normal';
            $this->setImages($value['src'], $value['dst_x'], $value['dst_y'], $value['src_x'], $value['src_y'], $value['src_w'], $value['src_h'], $value['alpha'], $value['type']);
        }
        return $this;
    }

    public function buildText($content, $dst_x = 0, $dst_y = 0, $fontSize = null, $rgba = null, $max_w = null, $font = null, $weight = null, $space = null, $angle = null)
    {
        $this->setTexts($content, $dst_x, $dst_y, $fontSize, $rgba, $max_w, $font, $weight, $space, $angle);
        return $this;
    }

    public function buildTextMany($params = [])
    {
        foreach ($params as $value) {
            $value['dst_x'] = isset($value['dst_x']) ? $value['dst_x'] : 0;
            $value['dst_y'] = isset($value['dst_y']) ? $value['dst_y'] : 0;
            $value['fontSize'] = isset($value['font']) ? $value['font'] : 0;
            $value['rgba'] = isset($value['rgba']) ? $value['rgba'] : [];
            $value['max_w'] = isset($value['max_w']) ? $value['max_w'] : 0;
            $value['font'] = isset($value['font_family']) ? $value['font_family'] : '';
            $value['weight'] = isset($value['weight']) ? $value['weight'] : 1;
            $value['space'] = isset($value['space']) ? $value['space'] : 0;
            $value['angle'] = isset($value['angle']) ? $value['angle'] : 0;
            $this->setTexts($value['content'], $value['dst_x'], $value['dst_y'], $value['fontSize'], $value['rgba'], $value['max_w'], $value['font'], $value['weight'], $value['space'], $value['angle']);
        }
        return $this;
    }

    public function buildQr($text, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $size = 4, $margin = 1, $level = 'L')
    {
        $this->setQrs($text, $level, $size, $margin, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        return $this;
    }

    public function buildQrMany($params = [])
    {
        foreach ($params as $value) {
            $value['dst_x'] = isset($value['dst_x']) ? $value['dst_x'] : 0;
            $value['dst_y'] = isset($value['dst_y']) ? $value['dst_y'] : 0;
            $value['src_x'] = isset($value['src_x']) ? $value['src_x'] : 0;
            $value['src_y'] = isset($value['src_y']) ? $value['src_y'] : 0;
            $value['src_w'] = isset($value['src_w']) ? $value['src_w'] : 0;
            $value['src_h'] = isset($value['src_h']) ? $value['src_h'] : 0;
            $value['size'] = isset($value['size']) ? $value['size'] : 4;
            $value['margin'] = isset($value['margin']) ? $value['margin'] : 1;
            $value['level'] = isset($value['level']) ? $value['level'] : 'L';
            $this->setQrs($value['text'], $value['level'], $value['size'], $value['margin'], $value['dst_x'], $value['dst_y'], $value['src_x'], $value['src_y'], $value['src_w'], $value['src_h']);
        }
        return $this;
    }

    public function buildLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        $line = [$x1, $y1, $x2, $y2, $rgba, $type, $weight];
        $this->lines[] = $line;
        $this->query->setQuery('line', $line);
        return $this;
    }

    public function buildArc($cx = 0, $cy = 0, $w = 0, $h = 0, $s = 0, $e = 0, $rgba = [], $type = '', $style = '', $weight = 1)
    {
        $arc = [$cx, $cy, $w, $h, $s, $e, $rgba, $type, $style, $weight];
        $this->arcs = $arc;
        $this->query->setQuery('arc', $arc);
        return $this;
    }

    public function path($path)
    {
        $this->path = $path;
        $this->query->setPath($path);
        return $this;
    }

    public function getPoster($path = '')
    {
        $query = $this->query->getQuery();
        return $this->extension->getPoster($query, $path);
    }

    public function setPoster()
    {
        $query = $this->query->getQuery();
        return $this->extension->setPoster($query);
    }

    public function stream()
    {
        $query = $this->query->getQuery();
        return $this->extension->stream($query);
    }

    public function baseData()
    {
        $query = $this->query->getQuery();
        return $this->extension->baseData($query);
    }

    public function getIm()
    {
        $query = $this->query->getQuery();
        return $this->extension->getIm($query);
    }

    public function getImInfo()
    {
        $query = $this->query->getQuery();
        return $this->extension->getImInfo($query);
    }

    public function blob()
    {
        $query = $this->query->getQuery();
        return $this->extension->blob($query);
    }

    public function tmp()
    {
        $query = $this->query->getQuery();
        return $this->extension->tmp($query);
    }

    public function crop($x = 0, $y = 0, $width = 0, $height = 0)
    {
        $crop = [$x, $y, $width, $height];
        $this->query->setQuery('crop', $crop);
        return $this;
    }

    protected function setImages(...$params)
    {
        $this->images[] = $params;
        $this->query->setQuery('image', $params);
    }

    protected function setTexts(...$params)
    {
        $this->texts[] = $params;
        $this->query->setQuery('text', $params);
    }

    protected function setQrs(...$params)
    {
        $this->qrs[] = $params;
        $this->query->setQuery('qrs', $params);
    }

    public function __clone()
    {
        $this->query = clone $this->query;
    }
}