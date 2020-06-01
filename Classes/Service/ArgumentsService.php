<?php
declare(strict_types=1);

namespace Zeroseven\Z7Events\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ArgumentsService
{

    public const REQUEST_KEY = 'tx_z7blog_list';

    protected static function getRequestData(): array
    {
        return GeneralUtility::_GP(self::REQUEST_KEY) ?: [];
    }

    protected static function getDefaults(): array
    {
        return [
            'stage' => 0,
            'maxStages' => 0,
            'author' => 0,
            'category' => 0,
            'tag' => '',
            'topPostsFirst' => 0,
            'topPostsOnly' => 0,
            'archiveMode' => 0,
            'ordering' => ''
        ];
    }

    public static function get(bool $getRequestData, bool $ignoreEmptyKeys = false, ...$arguments): array
    {
        $filters = self::getDefaults();

        $overrides = array_merge(($getRequestData ? [self::getRequestData()] : []), $arguments);

        if ($overrides) {
            foreach ($overrides as $override) {
                if (is_array($override)) {
                    foreach ($override as $key => $value) {
                        if (array_key_exists($key, $filters)) {
                            $filters[$key] = is_int($filters[$key]) ? (int)$value : $value;
                        }
                    }
                }
            }
        }

        return $ignoreEmptyKeys ? array_filter($filters, static function ($v) {
            return !empty($v);
        }) : $filters;
    }
}
