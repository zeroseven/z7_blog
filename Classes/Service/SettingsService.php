<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

class SettingsService
{
    public const EXTENSION_KEY = 'z7_blog';

    protected static function getPropertyPath($subject, string $propertyPath = null)
    {
        if ($propertyPath === null) {
            return $subject;
        }

        return ObjectAccess::getPropertyPath((array)$subject, $propertyPath);
    }

    /** @throws InvalidConfigurationTypeException | Exception */
    public static function getPluginConfiguration(string $propertyPath = null)
    {
        // Try to get settings from cache
        if (!($pluginConfiguration = $GLOBALS['USER'][self::EXTENSION_KEY]['configuration'] ?? null)) {
            $typoScriptSetup = GeneralUtility::makeInstance(ObjectManager::class)->get(ConfigurationManager::class)->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
            if ($settings = ($typoScriptSetup['plugin.']['tx_z7blog.'] ?? null)) {
                $pluginConfiguration = $GLOBALS['USER'][self::EXTENSION_KEY]['configuration'] = (array)GeneralUtility::makeInstance(TypoScriptService::class)->convertTypoScriptArrayToPlainArray($settings);
            }
        }

        return self::getPropertyPath($pluginConfiguration, $propertyPath);
    }

    public static function getSettings(string $propertyPath = null)
    {
        try {
            $settings = self::getPluginConfiguration('settings');

            return self::getPropertyPath($settings, $propertyPath);
        } catch (InvalidConfigurationTypeException | Exception $e) {
        }

        return null;
    }
}
