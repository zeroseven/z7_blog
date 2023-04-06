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
        'searchFields' => 'firstname, lastname, email, image, description',
        'typeicon_classes' => [
            'default' => 'plugin-z7blog-author'
        ]
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
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1, 'flags-multiple']
                ],
                'default' => 0
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0]
                ],
                'foreign_table' => 'tx_z7blog_domain_model_author',
                'foreign_table_where' => 'AND tx_z7blog_domain_model_author.pid=###CURRENT_PID### AND tx_z7blog_domain_model_author.sys_language_uid IN (-1,0)'
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled', 1]
                ]
            ]
        ],
        'firstname' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.firstname',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'default' => ''
            ]
        ],
        'lastname' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.lastname',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'expertise' => [
            'exclude' => true,
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.expertise',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'email' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.email',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'blindLinkOptions' => 'file, spec, folder, telephone, page, url'
                        ]
                    ]
                ],
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'image' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.image',
            'config' => [
                'type' => 'file',
                'maxitems' => 6,
                'allowed' => 'common-image-types'
            ],
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
                'enableRichtext' => 1,
                'default' => ''
            ]
        ],
        'page' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.page',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'blindLinkOptions' => 'file, spec, folder, mail, telephone'
                        ]
                    ]
                ],
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'twitter' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.twitter',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'blindLinkOptions' => 'file, page, spec, folder, mail, telephone'
                        ]
                    ]
                ],
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'linkedin' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.linkedin',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'blindLinkOptions' => 'file, page, spec, folder, mail, telephone'
                        ]
                    ]
                ],
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'xing' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tx_z7blog_domain_model_author.xing',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'blindLinkOptions' => 'file, page, spec, folder, mail, telephone'
                        ]
                    ]
                ],
                'eval' => 'trim',
                'default' => ''
            ]
        ]
    ]
];
