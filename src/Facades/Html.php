<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 17:06
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Html\Builder;

/**
 * User: lang
 * @method static Builder channel($channel = "");
 * @method static Builder load($html);
 * @package Kkokk\Poster\Facades
 * @class Html
 * @author 73285 2023-08-09
 *
 * @see Builder
 */
class Html extends Facade
{
    protected static function getFacadeModel()
    {
        return 'html';
    }
}