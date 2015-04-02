<?php

use Cake\Core\Configure;

if (!Configure::check('WyriHaximus.MinifyHtml.factory')) {
    Configure::write('WyriHaximus.MinifyHtml.factory', 'WyriHaximus\HtmlCompress\Factory::constructFastest');
}
