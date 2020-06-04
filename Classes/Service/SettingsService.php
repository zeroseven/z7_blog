<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class SettingsService
{

    public const EXTENSION_KEY = 'z7_blog';

    protected static function pluginConfiguration(): array
    {
        $typoScriptSetup = GeneralUtility::makeInstance(ObjectManager::class)->get(ConfigurationManager::class)->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $pluginConfiguration = [];

        if ($settings = ($typoScriptSetup['plugin.']['tx_z7blog.']['settings.'] ?? null)) {
            $pluginConfiguration = GeneralUtility::makeInstance(TypoScriptService::class)->convertTypoScriptArrayToPlainArray($settings);
        }

        return $pluginConfiguration;
    }

    public static function get(): array
    {

        // Return cached settings
        if ($cache = $GLOBALS['USER'][self::EXTENSION_KEY]['settings'] ?? null) {
            return $cache;
        }

        // Cache settings and return merged array
        return $GLOBALS['USER'][self::EXTENSION_KEY]['settings'] = self::pluginConfiguration();
    }

    public static function getKey(string $key)
    {
        $settings = self::get();
        $parts = GeneralUtility::trimExplode('.', $key, true);

        foreach ($parts as $part) {
            if ($value = $settings[$part] ?? null) {
                $settings = $value;
            } else {
                return null;
            }
        }

        return $settings ?? null;
    }

}
