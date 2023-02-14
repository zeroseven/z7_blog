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
    'version' => '1.3.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.9.99'
        ],
        'suggests' => [
        ]
    ]
];
