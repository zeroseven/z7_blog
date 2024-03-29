<?php

defined('TYPO3') || die('✘');

call_user_func(static function (string $extensionKey, int $postDoktype, int $categoryDoktype) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionKey,
        'Filter',
        [\Zeroseven\Z7Blog\Controller\PostController::class => 'filter'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionKey,
        'List',
        [\Zeroseven\Z7Blog\Controller\PostController::class => 'list', 'listUncached'],
        [\Zeroseven\Z7Blog\Controller\PostController::class => 'listUncached'],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionKey,
        'Static',
        [\Zeroseven\Z7Blog\Controller\PostController::class => 'static'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionKey,
        'Authors',
        [\Zeroseven\Z7Blog\Controller\AuthorController::class => 'list'],
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

}, \Zeroseven\Z7Blog\Service\SettingsService::EXTENSION_KEY, \Zeroseven\Z7Blog\Domain\Model\Post::DOKTYPE, \Zeroseven\Z7Blog\Domain\Model\Category::DOKTYPE);

// Register hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'][] = \Zeroseven\Z7Blog\Hooks\IconFactory\OverrideIconOverlay::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Zeroseven\Z7Blog\Hooks\DataHandler\ResortPageTree::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Zeroseven\Z7Blog\Hooks\DataHandler\RefreshPageTree::class;

// Add styles to the backend
$GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets']['z7_blog'] = 'EXT:z7_blog/Resources/Public/Css/Backend/';
