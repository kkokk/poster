<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 17:00
 */

namespace Kkokk\Poster\Html;

use Kkokk\Poster\Html\Queries\WkQuery;

class WkHtml extends Html
{
    public function getQueryInstance()
    {
        return new WkQuery;
    }
}