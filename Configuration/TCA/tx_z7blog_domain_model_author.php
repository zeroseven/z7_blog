<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author',
        'label' => 'firstname',
        'label_alt' => 'lastname',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'firstname,lastname,email,image,description',
        'iconfile' => 'EXT:z7_blog/Resources/Public/Icons/tx_z7blog_domain_model_author.svg'
    ],
    'palettes' => [
        'name' => [
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.palette.name',
            'showitem' => 'firstname, lastname'
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;name, expertise, email, image, --div--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.tab.info, description, page, --div--;LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.tab.social, twitter, linkedin, xing'
        ]
    ],
    'columns' => [
        'firstname' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.firstname',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required'
            ]
        ],
        'lastname' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.lastname',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ]
        ],
        'expertise' => [
            'exclude' => true,
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.expertise',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ]
        ],
        'email' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.email',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ]
        ],
        'image' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'image',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                    ],
                    'overrideChildTca' => [
                        'types' => [
                            '0' => ['showitem' => '--palette--;;filePalette'],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => ['showitem' => '--palette--;;filePalette'],
                        ]
                    ],
                    'maxitems' => 1
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
        'description' => [
            'exclude' => true,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'enableRichtext' => 1
            ]
        ],
        'page' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.page',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'eval' => 'trim'
            ]
        ],
        'twitter' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.twitter',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ]
        ],
        'linkedin' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.linkedin',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ]
        ],
        'xing' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.xing',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ]
        ]
    ]
];


