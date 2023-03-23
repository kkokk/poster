<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 18:10
 */

namespace Kkokk\Poster\Image;


class Builder
{
    public $extension;

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
    public $Arcs = [];

    public function __construct(ExtensionInterface $extension)
    {
        $this->extension = $extension;
    }
}