<?php

defined('TYPO3_MODE') || die();

// Set TCA of default pages on posts and categories
$GLOBALS['TCA']['pages']['types'][\Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE]['showitem'] = $GLOBALS['TCA']['pages']['types'][1]['showitem'];
$GLOBALS['TCA']['pages']['types'][\Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE]['showitem'] = $GLOBALS['TCA']['pages']['types'][1]['showitem'];

// Manipulate TCA
call_user_func(static function(string $table, int $postDoktype, int $categoryDoktype) {

    // Add post page type
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        $table,
        'doktype',
        [
            'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.doktype.post',
            $postDoktype,
            'apps-pagetree-blogpost'
        ],
        '1',
        'after'
    );

    // Add category page type
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        $table,
        'doktype',
        [
            'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.doktype.category',
            $categoryDoktype,
            'apps-pagetree-blogcategory'
        ],
        '1',
        'after'
    );

    // Add fields to the table "pages"
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, [
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
            ]
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
            ]
        ],
        'post_author' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_author',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_z7blog_domain_model_author',
                'foreign_table_where' => 'AND \'all other authors\' = \'gone, LOL\'',
                'itemsProcFunc' => 'Zeroseven\\Z7Blog\\TCA\\ItemsProcFunc->getAuthors',
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
                'items' => [
                    ['-', 0, 'plugin-z7blog-author']
                ]
            ]
        ],
        'post_topics' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_topics',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'foreign_table' => 'tx_z7blog_domain_model_topic',
                'foreign_table_where' => 'AND \'all other topics\' = \'gone, LOL\'',
                'MM' => 'tx_z7blog_post_topic_mm',
                'itemsProcFunc' => 'Zeroseven\\Z7Blog\\TCA\\ItemsProcFunc->getTopics',
                'default' => 0,
            ]
        ],
        'post_tags' => [
            'exclude' => true,
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_tags',
            'config' => [
                'type' => 'user',
                'renderType' => 'blogTags',
                'placeholder' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_tags.placeholder'
            ]
        ],
        'post_relations_to' => [
            'exclude' => false,
            'displayCond' => 'FIELD:l10n_parent:=:0',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_relations_to',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => $table,
                'foreign_table' => $table,
                'MM_opposite_field' => 'post_relations_from',
                'MM' => 'tx_z7blog_post_mm',
                'filter' => [
                    [
                        'userFunc' => \Zeroseven\Z7Blog\TCA\GroupFilter::class . '->filterTypes',
                        'parameters' => [
                            'allowed' => $postDoktype
                        ]
                    ]
                ],
                'suggestOptions' => [
                    'default' => [
                        'searchWholePhrase' => 1,
                        'addWhere' => ' AND ' . $table . '.uid != ###THIS_UID###'
                    ],
                    $table => [
                        'searchCondition' => 'doktype = ' . $postDoktype
                    ]
                ],
                'size' => 5,
                'autoSizeMax' => 10,
                'maxitems' => 99
            ]
        ],
        'post_relations_from' => [
            'exclude' => true,
            'displayCond' => 'FIELD:l10n_parent:=:0',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.post_relations_from',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'foreign_table' => $table,
                'allowed' => $table,
                'size' => 5,
                'maxitems' => 100,
                'MM' => 'tx_z7blog_post_mm',
                'readOnly' => 1
            ]
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
        ]
    ]);

    // Register post palette
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        $table,
        'blogpost_date_settings',
        'post_date, post_archive'
    );

    // Add fields to post pages
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table,'
        --div--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.tab.blog,
            post_top,
            --palette--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.palette.blogpost_date_settings;blogpost_date_settings,
            post_author, post_topics, post_tags, post_relations_to, post_relations_from
    ', (string)$postDoktype);

    // Add fields to category pages
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table,'
        --div--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.tab.blog,
            post_redirect_category
    ', (string)$categoryDoktype);

    // Add icons for new page types:
    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TCA']['pages'],
        [
            'ctrl' => [
                'typeicon_classes' => [
                    $postDoktype => 'apps-pagetree-blogpost',
                    $postDoktype . '-hideinmenu' => 'apps-pagetree-blogpost-hideinmenu',
                    $categoryDoktype => 'apps-pagetree-blogcategory',
                    $categoryDoktype . '-hideinmenu' => 'apps-pagetree-blogcategory-hideinmenu',
                ],
            ],
        ]
    );

},'pages', \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE, \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE);
