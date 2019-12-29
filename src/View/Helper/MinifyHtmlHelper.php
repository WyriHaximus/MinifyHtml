<?php declare(strict_types=1);

namespace WyriHaximus\MinifyHtml\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use function WyriHaximus\MinifyHtml\compress;

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
        if ((
                !Configure::read('debug') ||
                Configure::read('WyriHaximus.MinifyHtml.debugOverride')
            ) &&
            in_array($this->_View->getResponse()->getType(), $this->mimeTypes)
        ) {
            $content = $this->_View->fetch('content');
            $content = compress($content);
            $this->_View->assign('content', $content);
        }
    }
}
