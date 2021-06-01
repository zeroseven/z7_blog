<?php

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function (string $extensionKey, int $postDoktype, int $categoryDoktype) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Zeroseven.Z7Blog',
        'Filter',
        ['Post' => 'filter'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Zeroseven.Z7Blog',
        'List',
        ['Post' => 'list', 'listUncached'],
        ['Post' => 'listUncached'],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Zeroseven.Z7Blog',
        'Static',
        ['Post' => 'static'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Zeroseven.Z7Blog',
        'Authors',
        ['Author' => 'list'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    // Add the wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:$extensionKey/Configuration/PageTs/Mod.tsconfig'");

    // Allow custom doktypes in page tree
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig("options.pageTree.doktypesToShowInNewPageDragArea := addToList($postDoktype,$categoryDoktype)");

    // Add module configuration
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(trim("
        plugin.tx_z7blog.doktype {
            category = $categoryDoktype
            post = $postDoktype
        }
    "));

    // Register temporary cache objects in "USER" array
    $GLOBALS['USER'][$extensionKey] = [
        'post' => null,
        'repository' => null,
        'configuration' => null,
    ];

}, Zeroseven\Z7Blog\Service\SettingsService::EXTENSION_KEY, \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE, \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE);

// Register hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'][] = \Zeroseven\Z7Blog\Hooks\IconFactory\OverrideIconOverlay::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Zeroseven\Z7Blog\Hooks\DataHandler\ResortPageTree::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Zeroseven\Z7Blog\Hooks\DataHandler\RefreshPageTree::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][] = \Zeroseven\Z7Blog\Hooks\WebLayoutHeader\PostHeader::class . '->render';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][] = \Zeroseven\Z7Blog\Hooks\WebLayoutHeader\CategoryHeader::class . '->render';
