<?php

return [
    'frontend' => [
        'zeroseven/z7_blog/redirecthandler' => [
            'target' => \Zeroseven\Z7Blog\Middleware\RedirectHandler::class,
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect'
            ],
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering'
            ]
        ],
        'zeroseven/z7_blog/structured_data' => [
            'target' => \Zeroseven\Z7Blog\Middleware\StructuredData::class,
        ],
    ]
];
