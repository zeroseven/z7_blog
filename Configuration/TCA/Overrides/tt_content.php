<?php

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(static function (array $cTypes) {

    foreach ($cTypes as $cType) {

        // Create resource identifier
        $resourceIdentifier = 'content-' . str_replace('_', '-', $cType);

        // Add some default fields to the content elements by copy configuration of "header"
        $GLOBALS['TCA']['tt_content']['types'][$cType]['showitem'] = $GLOBALS['TCA']['tt_content']['types']['header']['showitem'];

        // Register Flexform
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:z7_blog/Configuration/FlexForms/' . $resourceIdentifier . '.xml', $cType);

        // Add the flexform to the content element
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'pi_flexform', $cType, 'after:header');

        // Register plugins
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Zeroseven.Z7Blog',
            ucfirst(str_replace('z7blog_', '', $cType)),
            'LLL:EXT:z7_blog/Resources/Private/Language/locallang_db.xlf:tt_content.cType.' . $cType,
            $resourceIdentifier
        );
    }

}, ['z7blog_list', 'z7blog_static', 'z7blog_filter', 'z7blog_authors']);
