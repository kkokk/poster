<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 13:32
 */

namespace Kkokk\Poster\Html;

interface HtmlInterface
{
    public function load($html);
    public function render($query);
}