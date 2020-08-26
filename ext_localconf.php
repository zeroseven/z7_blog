<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function (int $postDoktype, int $categoryDoktype) {

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

// Register hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'][] = \Zeroseven\Z7Blog\Hooks\IconFactory\OverrideIconOverlay::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Zeroseven\Z7Blog\Hooks\DataHandler\ResortPagetree::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][] = \Zeroseven\Z7Blog\Hooks\DrawHeader\PostHeader::class . '->render';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][] = \Zeroseven\Z7Blog\Hooks\DrawHeader\CategoryHeader::class . '->render';
