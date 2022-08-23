<?php

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

$versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
if ($versionInformation->getMajorVersion() === 11) {
    $GLOBALS['TCA']['tx_z7blog_domain_model_author']['columns']['sys_language_uid']['config'] = [
        'type' => 'language'
    ];
    $GLOBALS['TCA']['tx_z7blog_domain_model_topic']['columns']['sys_language_uid']['config'] = [
        'type' => 'language'
    ];
}
