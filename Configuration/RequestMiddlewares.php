<?php

return [
    'frontend' => [
        'zerseoeven/z7_bloh/redirecthandler' => [
            'target' => \Zeroseven\Z7Blog\Middleware\RedirectHandler::class,
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect'
            ],
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering'
            ]
        ]
    ]
];
