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
 * @package       MinifyHtml.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');
App::uses('JSMin', 'MinifyHtml.Vendor/JSMin');
App::uses('CssMin', 'MinifyHtml.Vendor/CssMin');

/**
 * Minify HTML helper.
 *
 * Automatic minification of HTML.
 *
 * @package       MinifyHtml.View.Helper
 */
class MinifyHtmlHelper extends AppHelper {

    /**
     * Style attribute cache MinifyHtmlHelper.
     *
     * @var array
     * @access protected
     */
    protected $_styleAttributeCache = array();

    /**
     * Array with strings to find before the html minification.
     *
     * @var array
     * @access protected
     */
    protected $_preFind = array();

    /**
     * Array with strings to replace the find strings before the html minification.
     *
     * @var array
     * @access protected
     */
    protected $_preReplace = array();

    /**
     * Array with strings to find after the html minification.
     *
     * @var array
     * @access protected
     */
    protected $_postFind = array();

    /**
     * Array with strings to replace the find strings after the html minification.
     *
     * @var array
     * @access protected
     */
    protected $_postReplace = array();

    /**
     * After layout callback used to minify the HTML just before it's send to the browser
     *
     * @param string $layoutFile The layout file that was rendered.
     * @return void
     */
    public function afterLayout($layoutFile) {
        if ((!Configure::read('debug')) && (($this->_View->response->type() == 'text/html') || ($this->_View->response->type() == 'text/xhtml'))) {
            $content = $this->_View->Blocks->get('content');
            $content = $this->_squeeze($content);
            $this->_View->Blocks->set('content', $content);
            $this->_reset();
        }
    }

    /**
     * Resets all internal properties to their initial state.
     *
     * @param string $html The HTML to be minified.
     * @return string
     */
    protected function _reset() {
        $this->_preFind = array();
        $this->_preReplace = array();
        $this->_postFind = array();
        $this->_postReplace = array();
        $this->_styleAttributeCache = array();
    }

    /**
     * Minifies JavaScript, CSS and HTML seperately.
     *
     * @param string $html The HTML to be minified.
     * @return string
     */
    protected function _squeeze($html) {
        $this->__findTags($html);
        $this->__findStyleAttributes($html);

        $html = str_replace($this->_preFind, $this->_preReplace, $html);
        $html = $this->__squeezeHtml($html);
        $html = str_replace($this->_postFind, $this->_postReplace, $html);

        return trim($html);
    }

    /**
     * Locates style, script, pre and textarea tags and prepares them for minification or exclusion from minification
     *
     * @param string $html The HTML to be searched for tags.
     * @return void
     */
    private function __findTags($html) {
        preg_match_all("!(<(style|script|pre|textarea)[^>]*>(?:\\s*<\\!--)?)(.*?)((?://-->\\s*)?</(style|script|pre|textarea)>)!is", $html, $scriptParts);
        $scriptPartsCount = count($scriptParts[0]);

        for ($i = 0; $i < $scriptPartsCount; $i++) {
            $code = $scriptParts[3][$i];
            if ((!empty($code)) && (($scriptParts[2][$i] == 'script') || ($scriptParts[2][$i] == 'style') || ($scriptParts[2][$i] == 'pre') || ($scriptParts[2][$i] == 'textarea'))) {
                if (($scriptParts[2][$i] == 'script') && (strpos($scriptParts[1][$i], 'type="text/html"') === false) && strpos($scriptParts[1][$i], 'type="text/template"') === false) {
                    $code = trim(JSMin::minify($code));
                } elseif ($scriptParts[2][$i] == 'style') {
                    $code = trim(CssMin::minify($code));
                }

                $this->_preFind[] = $scriptParts[0][$i];
                $this->_preReplace[] = '<htmlBlockNr' . $i . 'ForInlineReplacement />';
                $this->_postFind[] = '<htmlBlockNr' . $i . 'ForInlineReplacement />';
                $this->_postReplace[] = trim($scriptParts[1][$i]) . $code . trim($scriptParts[4][$i]);
            }
        }
    }

    /**
     * Locates style attributes and prepares them for minification
     *
     * @param string $html The HTML to be searched for style attributes.
     * @return void
     */
    private function __findStyleAttributes($html) {
        if (preg_match_all('/style=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is', $html, $match)) {
            $matchCount = count($match[2]);
            for ($i = 0; $i < $matchCount; $i++) {
                $this->_preFind[] = $match[2][$i];
                $this->_preReplace[] = 'cssBlockNr' . $i . 'ForInlineReplacement';
                $this->_postFind[] = 'cssBlockNr' . $i . 'ForInlineReplacement';

                $inTagCssMd5 = md5($match[2][$i]);
                if (isset($this->_styleAttributeCache[$inTagCssMd5])) {
                    $inTagCss = $this->_styleAttributeCache[$inTagCssMd5];
                } else {
                    $inTagCss = str_replace(array('fakeHtmlTag{', '}'), '', CssMin::minify('fakeHtmlTag{' . $match[2][$i] . '}'));
                    $this->_styleAttributeCache[$inTagCssMd5] = $inTagCss;
                }
                $this->_postReplace[] = $inTagCss;
            }
        }
    }

    /**
     * Minifies HTML (removes \r, \n, \t and unnecessary spaces).
     *
     * @param string $html The HTML to be minified.
     * @return string
     */
    private function __squeezeHtml($html) {
        // Replace newlines, returns and tabs with nothing
        $html = str_replace(array("\r", "\n", "\t"), '', $html);
        // Replace multiple spaces with a single space
        $html = preg_replace('/(\s+)/m', ' ', $html);

        // Remove spaces that are followed by either > or <
        $html = preg_replace('/ ([<>])/', '$1', $html);
        // Remove spaces that are preceded by either > or < 
        $html = str_replace('> ', '>', $html);

        return trim($html);
    }

}