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
use Cake\Http\Response;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use WyriHaximus\MinifyHtml\View\Helper\MinifyHtmlHelper;

/**
 * Class MinifyHtmlHelpertest
 * @package WyriHaximus\CakePHP\Test\MinifyHtml\View\Helper
 */
class MinifyHtmlHelpertest extends TestCase {

    public function testAfterLayout() {
        Configure::write('debug', true);
        Configure::write('WyriHaximus.MinifyHtml.debugOverride', true);
        Configure::write('WyriHaximus.MinifyHtml.factory', 'WyriHaximus\HtmlCompress\Factory::construct');

        $response = $this->prophesize(Response::class);
        $response->getType()->shouldBeCalled()->willReturn('text/html');

        $view = $this->prophesize(View::class);
        $view->fetch('content')->shouldBeCalled()->willReturn('foo.bar');
        $view->assign('content', 'foo.bar')->shouldBeCalled();
        $view->getResponse()->shouldBeCalled()->willReturn($response->reveal());

        $helper = new MinifyHtmlHelper($view->reveal());

        $helper->afterLayout('foo.bar');
    }
}
