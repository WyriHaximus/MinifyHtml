<?php

/**
 * This file is part of MinifyHtml.
 *
 ** (c) 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\CakePHP\Test\MinifyHtml\View\Helper;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Phake;
use WyriHaximus\CakePHP\MinifyHtml\View\Helper\MinifyHtmlHelper;

/**
 * Class MinifyHtmlHelper
 * @package WyriHaximus\CakePHP\MinifyHtml\View\Helper
 */
class MinifyHtmlHelpertest extends TestCase {

    public function testAfterLayout() {
        Configure::write('debug', false);

        $view = Phake::mock('Cake\View\View');
        $view->Blocks = Phake::mock('Cake\View\ViewBlock');
        $view->request = Phake::mock('Cake\Network\Request');
        $view->response = Phake::mock('Cake\Network\Response');
        $helper = new MinifyHtmlHelper($view);

        Phake::when($view->response)->type()->thenReturn('text/html');

        $helper->afterLayout('foo.bar');

        Phake::inOrder(
            Phake::verify($view->Blocks)->get('content'),
            Phake::verify($view->Blocks)->set('content', $this->isType('string'))
        );
    }
}
