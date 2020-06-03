<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function () {

    // Register plugins
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Zeroseven.Z7Blog',
        'List',
        'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tt_content.cType.z7blog',
        'content-z7blog'
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Zeroseven.Z7Blog',
        'Static',
        'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tt_content.cType.z7blog_static',
        'content-z7blog-static'
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Zeroseven.Z7Blog',
        'Filter',
        'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tt_content.cType.z7blog_filter',
        'content-z7blog-filter'
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Zeroseven.Z7Blog',
        'Authors',
        'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tt_content.cType.z7blog_authors',
        'content-z7blog-authors'
    );

});
