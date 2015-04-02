<?php

return [
    'WyriHaximus' => [
        'MinifyHtml' => [
            'debugOverride' => false,
            'factory' => 'WyriHaximus\HtmlCompress\Factory::constructFastest',
        ],
    ],
];
