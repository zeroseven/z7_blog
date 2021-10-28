<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Blog',
    'description' => 'Another blog-system for TYPO3',
    'category' => 'plugin',
    'author' => 'Raphael Thanner',
    'author_email' => 'r.thanner@zeroseven.de',
    'author_company' => 'zeroseven design studios GmbH',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '1.2.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99'
        ],
        'suggests' => [
        ]
    ]
];
