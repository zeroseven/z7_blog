<?php

declare(strict_types=1);

return [
    \Zeroseven\Z7Blog\Domain\Model\AbstractPageModel::class => [
        'tableName' => 'pages',
        'properties' => [
            'fileReferences' => [
                'fieldName' => 'media'
            ],
            'lastChange' => [
                'fieldName' => 'SYS_LASTCHANGED'
            ]
        ]
    ],
    \Zeroseven\Z7Blog\Domain\Model\Category::class => [
        'tableName' => 'pages',
        'recordType' => \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE,
        'properties' => [
            'redirect' => [
                'fieldName' => 'post_redirect_category'
            ]
        ]
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
            'author' => [
                'fieldName' => 'post_author'
            ],
            'topics' => [
                'fieldName' => 'post_topics'
            ],
            'tags' => [
                'fieldName' => 'post_tags'
            ],
            'relationsTo' => [
                'fieldName' => 'post_relations_to'
            ],
            'relationsFrom' => [
                'fieldName' => 'post_relations_from'
            ]
        ]
    ],
    \Zeroseven\Z7Blog\Domain\Model\Author::class => [
        'properties' => [
            'firstName' => [
                'fieldName' => 'firstname'
            ],
            'lastName' => [
                'fieldName' => 'lastname'
            ]
        ]
    ],
    \Zeroseven\Z7Blog\Domain\Model\TraitCollector\PostTraitCollector::class => [
        'tableName' => 'pages'
    ],
    \Zeroseven\Z7Blog\Domain\Model\TraitCollector\CategoryTraitCollector::class => [
        'tableName' => 'pages'
    ],
    \Zeroseven\Z7Blog\Domain\Model\TraitCollector\AuthorTraitCollector::class => [
        'tableName' => 'tx_z7blog_domain_model_author'
    ],
    \Zeroseven\Z7Blog\Domain\Model\TraitCollector\TopicTraitCollector::class => [
        'tableName' => 'tx_z7blog_domain_model_topic'
    ]
];
