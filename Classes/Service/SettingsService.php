<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class SettingsService
{

    public const EXTENSION_KEY = 'z7_blog';

    protected static function getKey(array $array, string $key = null)
    {

        if($key === null) {
            return $array;
        }

        $settings = $array;
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

    public static function getPluginConfiguration(string $key = null)
    {
        // Try to get settings from cache
        if (!is_array($pluginConfiguration = $GLOBALS['USER'][self::EXTENSION_KEY]['plugin_configuration'] ?? null)) {

            $typoScriptSetup = GeneralUtility::makeInstance(ObjectManager::class)->get(ConfigurationManager::class)->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
            if ($settings = ($typoScriptSetup['plugin.']['tx_z7blog.'] ?? null)) {
                $pluginConfiguration = $GLOBALS['USER'][self::EXTENSION_KEY]['plugin_configuration'] = (array)GeneralUtility::makeInstance(TypoScriptService::class)->convertTypoScriptArrayToPlainArray($settings);
            }
        }

        return self::getKey($pluginConfiguration, $key);
    }

    public static function getSettings(string $key = null)
    {
        $settings = self::getPluginConfiguration('settings') ?? [];

        return self::getKey($settings, $key);
    }

}
