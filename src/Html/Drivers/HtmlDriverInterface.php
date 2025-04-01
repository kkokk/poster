<?php
/**
 * User: lang
 * Date: 2023/8/10
 * Time: 11:21
 */

namespace Kkokk\Poster\Html\Drivers;

interface HtmlDriverInterface
{
    public function render($query);

    public function getImageBlob();

    public function getFilePath();
}