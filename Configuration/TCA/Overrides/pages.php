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
                'foreign_table' => $table,
                'filter' => [
                    [
                        'userFunc' => \Zeroseven\Z7Blog\TCA\GroupFilter::class . '->filterDoktypes',
                        'parameters' => [
                            'allowed' => $postDoktype
                        ],
                    ],
                ],
                'suggestOptions' => [
                    'default' => [
                        'searchWholePhrase' => 1
                    ],
                    $table => [
                        'searchCondition' => 'doktype = ' . $postDoktype
                    ]
                ],
                'allowed' => $table,
                'MM' => 'tx_z7blog_post_mm',
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

    // Register post palette
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        $table,
        'blogpost_date_settings',
        'post_date, post_archive'
    );

    // Register post palette
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        $table,
        'blogpost_relations',
        'post_author, --linebreak--, post_related'
    );

    // Add fields to post pages
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table,'
        --div--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.tab.blog, 
            post_top, 
            --palette--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.palette.blogpost_date_settings;blogpost_date_settings, 
            --palette--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:pages.palette.blogpost_relations;blogpost_relations, 
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
