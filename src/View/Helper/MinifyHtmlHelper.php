<?php

/**
 * This file is part of MinifyHtml.
 *
 ** (c) 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\MinifyHtml\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

/**
 * Class MinifyHtmlHelper
 * @package WyriHaximus\MinifyHtml\View\Helper
 */
class MinifyHtmlHelper extends Helper
{
    /**
     * @var array
     */
    protected $mimeTypes = [
        'text/html',
        'text/xhtml',
    ];

    public function afterLayout()
    {
        if (
            (
                !Configure::read('debug') ||
                Configure::read('WyriHaximus.MinifyHtml.debugOverride')
            ) &&
            in_array($this->_View->response->type(), $this->mimeTypes)
        ) {
            $content = $this->_View->Blocks->get('content');
            $content = \WyriHaximus\MinifyHtml\compress($content);
            $this->_View->Blocks->set('content', $content);
        }
    }
}
