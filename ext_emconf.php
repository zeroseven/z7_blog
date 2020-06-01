<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Blog',
    'description' => 'Another blog-system for TYPO3',
    'category' => 'plugin',
    'author' => 'zeroseven design studios GmbH',
    'author_email' => 'r.thanner@zeroseven.de',
    'state' => 'alpha',
    'clearCacheOnLoad' => 1,
    'version' => '0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
        ],
        'conflicts' => [
            'blogpages' => ''
        ],
        'suggests' => [
        ],
    ],
];
