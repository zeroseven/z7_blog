<?php

defined('TYPO3') || die('✘');

call_user_func(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        Zeroseven\Z7Blog\Service\SettingsService::EXTENSION_KEY,
        'Configuration/TypoScript',
        'Blog'
    );
});
