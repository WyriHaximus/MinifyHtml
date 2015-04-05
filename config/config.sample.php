<?php

return [
    'WyriHaximus' => [
        'MinifyHtml' => [
            'debugOverride' => false, // Minify even when debug is enabled
            'factory' => 'WyriHaximus\HtmlCompress\Factory::constructFastest', // Parser factory
        ],
    ],
];
