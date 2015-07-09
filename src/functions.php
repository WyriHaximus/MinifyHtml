<?php

/**
 * This file is part of MinifyHtml.
 *
 ** (c) 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\MinifyHtml;

use Cake\Core\Configure;

/**
 * @param string $content
 * @return string
 */
function compress($content)
{
    $factory = call_user_func(Configure::read('WyriHaximus.MinifyHtml.factory'));
    return $factory->compress($content);
}
