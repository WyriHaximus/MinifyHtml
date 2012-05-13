<?php

/**
 * MinifyHtmlTest file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright 2012, Cees-Jan Kiewiet.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2012, Cees-Jan Kiewiet.
 * @package       MinifyHtml.Test.Case.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('View', 'View');
App::uses('Controller', 'Controller');
App::uses('Cache', 'Cache');
App::uses('MinifyHtmlHelper', 'MinifyHtml.View/Helper');
App::uses('CakeRequest', 'Network');

/**
 * MinifyHtmlTest class
 *
 * @package	   MinifyHtml
 * @subpackage       MinifyHtml.Test.Case.View.Helper
 * @property FakeMinifyHtmlHelper $MinifyHtmlHelper
 */
class MinifyHtmlTest extends CakeTestCase {

    /**
     * setUp method
     *
     * Test if we are testing the correct helper.
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        Configure::write('debug', 0);
        $this->MinifyHtmlHelper = new FakeMinifyHtmlHelper(new View(new Controller(new CakeRequest('/test', false))));
    }

    /**
     * testInstance method
     *
     * Test if we are testing the correct helper.
     *
     * @return void
     */
    public function tearDown() {
        parent::tearDown();
        $this->MinifyHtmlHelper = null;
    }

    /**
     * testInstance method
     *
     * Test if we are testing the correct helper.
     *
     * @return void
     */
    public function testInstance() {
        $this->assertTrue(is_a($this->MinifyHtmlHelper, 'FakeMinifyHtmlHelper'));
        $this->assertTrue(is_a($this->MinifyHtmlHelper, 'MinifyHtmlHelper'));
    }

    /**
     * testSimple method
     *
     * Test a simple html minification with nothing special.
     *
     * @return void
     */
    public function testSimple() {
        $this->__test('simple');
    }

    /**
     * testPre method
     *
     * Test minification with a pre block in it.
     *
     * @return void
     */
    public function testPre() {
        $this->__test('pre');
    }

    /**
     * testPre method
     *
     * Test minification with a textarea block in it.
     *
     * @return void
     */
    public function testTextarea() {
        $this->__test('textarea');
    }

    /**
     * testJs method
     *
     * Test script tag contents minification.
     *
     * @return void
     */
    public function testJs() {
        $this->__test('js');
    }

    /**
     * testCss method
     *
     * Test style tag contents minification.
     *
     * @return void
     */
    public function testCss() {
        $this->__test('css');
    }

    /**
     * testInlineCss method
     *
     * Test css inside a style attribute minification.
     *
     * @return void
     */
    public function testInlineCss() {
        $this->__test('inline_css');
    }

    /**
     * testInlineCssCache method
     *
     * Test the minified style attribute contents cache.
     *
     * @return void
     */
    public function testInlineCssCache() {
        $this->__test('inline_css_cache', array(2, 2, 2, 2, 1));
        $this->assertEqual(array('f479d19ba2dc90ae9be3d45a51490693' => 'color:red'), $this->MinifyHtmlHelper->getStyleAttributeCache());
    }

    /**
     * testInlineHtml method
     *
     * Test html minification within a script tag.
     *
     * @return void
     */
    public function testInlineHtml() {
        $this->__test('inline_html');
    }

    /**
     * testComplicated method
     *
     * Test all of the above.
     *
     * @return void
     */
    public function testComplicated() {
        $this->__test('complicated', array(12, 12, 12, 12, 2));
    }

    /**
     * testReset method
     *
     * Test all of the above with debug on so no minification.
     *
     * @return void
     */
    public function testReset() {
        $this->__test('complicated', array(12, 12, 12, 12, 2));
        $this->__test('complicated');
        $this->assertEqual(array(0, 0, 0, 0, 0), $this->MinifyHtmlHelper->getReset());
    }

    /**
     * testDebugComplicated method
     *
     * Test all of the above with debug on so no minification.
     *
     * @return void
     */
    public function testDebugComplicated() {
        Configure::write('debug', 2);
        $this->__test('complicated_debug');
        $this->assertEqual(array(0, 0, 0, 0, 0), $this->MinifyHtmlHelper->getReset());
    }

    /**
     * __test method
     *
     * Test method that performs most of the testing.
     *
     * @return void
     */
    private function __test($testFileName, $reset = null) {
        $fileIn = CakePlugin::path('MinifyHtml') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Controller' . DS . $testFileName . '_in.html';
        $fileOut = CakePlugin::path('MinifyHtml') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Controller' . DS . $testFileName . '_out.html';

        $this->MinifyHtmlHelper->_View->Blocks->set('content', file_get_contents($fileIn));

        if (is_null($reset)) {
            $this->MinifyHtmlHelper->afterLayout(null);
        } else {
            $this->MinifyHtmlHelper->squeezeWithoutReset();
            $this->assertEqual($reset, $this->MinifyHtmlHelper->getReset());
        }

        $this->assertEqual(file_get_contents($fileOut), $this->MinifyHtmlHelper->_View->Blocks->get('content'));
    }

}

/**
 * FakeMinifyHtmlHelper class
 * 
 * Providing data from inside the Helper.
 * Run squeeze without resetting the internal state
 */
class FakeMinifyHtmlHelper extends MinifyHtmlHelper {

    /**
     * Squeeze the countent without resetting the internal state
     * 
     * @return void 
     */
    public function squeezeWithoutReset() {
        $content = $this->_View->Blocks->get('content');
        $content = $this->_squeeze($content);
        $this->_View->Blocks->set('content', $content);
    }

    /**
     * Get the count for internal properties
     * @return array 
     */
    public function getReset() {
        return array(
            count($this->_preFind),
            count($this->_preReplace),
            count($this->_postFind),
            count($this->_postReplace),
            count($this->_styleAttributeCache),
        );
    }

    /**
     * Get the style attribute cache
     * @return array
     */
    public function getStyleAttributeCache() {
        return $this->_styleAttributeCache;
    }

}