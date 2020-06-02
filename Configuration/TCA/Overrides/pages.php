<?php

// Add page types
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'pages',
    'doktype',
    [
        'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post',
        \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE,
        'apps-pagetree-blogpost'
    ],
    '1',
    'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'pages',
    'doktype',
    [
        'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.postcategory',
        \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE,
        'apps-pagetree-blogcategory'
    ],
    '1',
    'after'
);

//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
//    '*',
//    'FILE:EXT:z7_events/Configuration/FlexForms/flexform_event.xml',
//    'z7events_list'
//);

// Add blog post fields to the table "pages"
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', [

    // The definition of the `pid` is required for the data mapping in the extbase repository
    'pid' => [
        'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.pid',
        'config' => [
            'type' => 'passthrough',
            'foreign_table' => 'pages',
        ]
    ],
    'post_top' => [
        'exclude' => true,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_top',
        'config' => [
            'type' => 'check',
            'items' => [
                '1' => [
                    '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                ]
            ],
            'default' => 0
        ]
    ],
    'post_archive' => [
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_archive',
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'size' => 10,
            'eval' => 'date,int',
            'default' => 0
        ],
    ],
    'post_date' => [
        'exclude' => false,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_date',
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'size' => 10,
            'eval' => 'date,required',
            'default' => time()
        ],
    ],
    'post_author' => [
        'exclude' => true,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_author',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tx_z7blog_domain_model_author',
            'foreign_table_where' => ' AND tx_z7blog_domain_model_author.sys_language_uid <= 0 ORDER BY tx_z7blog_domain_model_author.firstname ASC',
            'minitems' => 0,
            'maxitems' => 1,
            'default' => 0,
            'items' => [
                ['-', 0]
            ],
        ],
    ],
    'post_related' => [
        'exclude' => false,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_related',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'foreign_table' => 'pages',
            'suggestOptions' => [
                'default' => [
                    'searchWholePhrase' => 1
                ],
                'pages' => [
                    'searchCondition' => 'doktype = ' . \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE
                ]
            ],
            'allowed' => 'pages',
            'MM' => 'tx_blogpages_post__mm',
            'size' => 5,
            'autoSizeMax' => 10,
            'maxitems' => 99,
        ],
    ],
    'post_redirect_category' => [
        'exclude' => true,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_redirect_category',
        'config' => [
            'type' => 'check',
            'items' => [
                '1' => [
                    '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                ]
            ],
            'default' => 0
        ]
    ],
]);

// Register post palette with all the date stuff
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'post_settings',
    'post_top, post_date, post_archive, --linebreak--, post_author, --linebreak--, post_tags, --linebreak--, post_related'
);

// Set TCA for new page types
$GLOBALS['TCA']['pages']['types'][\Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE] = [
    'showitem' => $GLOBALS['TCA']['pages']['types'][1]['showitem'] . ',
        --div--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.tab.blog,
        --palette--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.palette.post_settings;post_settings,
        pid',
];

$GLOBALS['TCA']['pages']['types'][\Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE]['showitem'] = $GLOBALS['TCA']['pages']['types'][1]['showitem'];
$GLOBALS['TCA']['pages']['types'][\Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE]['showitem'] .= ',--div--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.tab.blog, event_redirect_category';

// Add icons for new page types:
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
    $GLOBALS['TCA']['pages'],
    [
        'ctrl' => [
            'typeicon_classes' => [
                \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE => 'apps-pagetree-blogpost',
                \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE . '-hideinmenu' => 'apps-pagetree-blogpost-hideinmenu',
                \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE => 'apps-pagetree-blogcategory',
                \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE . '-hideinmenu' => 'apps-pagetree-blogcategory-hideinmenu',
            ],
        ],
    ]
);
