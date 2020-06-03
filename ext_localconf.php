<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function (int $postDoktype, int $categoryDoktype) {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Zeroseven.Z7Blog',
        'Filter',
        ['Post' => 'filter, filterUncached'],
        ['Post' => 'filterUncached'],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Zeroseven.Z7Blog',
        'List',
        ['Post' => 'list'],
        [],
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
        'Detail',
        ['Post' => 'detail'],
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
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:z7_blog/Configuration/PageTs/Mod.tsconfig'");

    // Allow custom doktypes in page tree
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig("options.pageTree.doktypesToShowInNewPageDragArea := addToList($postDoktype,$categoryDoktype)");

    // Add module configuration
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(trim("
        plugin.tx_z7blog.doktype {
            category = $categoryDoktype
            post = $postDoktype
        }
    "));

},\Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE, \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE);
