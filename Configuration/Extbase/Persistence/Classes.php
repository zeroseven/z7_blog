<?php
declare(strict_types=1);

return [
    \Zeroseven\Z7Blog\Domain\Model\AbstractPageModel::class => [
        'tableName' => 'pages',
        'properties' => [
            'fileReferences' => [
                'fieldName' => 'media'
            ],
        ],
    ],
    \Zeroseven\Z7Blog\Domain\Model\Category::class => [
        'tableName' => 'pages',
        'recordType' => \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE,
        'properties' => [
            'redirectCategory' => [
                'fieldName' => 'post_redirect_category'
            ],
        ],
    ],
    \Zeroseven\Z7Blog\Domain\Model\Post::class => [
        'tableName' => 'pages',
        'recordType' => \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE,
        'properties' => [
            'top' => [
                'fieldName' => 'post_top'
            ],
            'archiveDate' => [
                'fieldName' => 'post_archive'
            ],
            'date' => [
                'fieldName' => 'post_date'
            ],
            'tags' => [
                'fieldName' => 'post_tags'
            ],
            'author' => [
                'fieldName' => 'post_author'
            ],
            'related' => [
                'fieldName' => 'post_related'
            ],
        ],
    ],
];