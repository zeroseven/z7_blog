<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Utility\GlobalUtility;

class RequestService
{
    public const REQUEST_KEY = 'tx_z7blog_list';

    public static function getArguments(): array
    {
        return GlobalUtility::getRequestParameter(self::REQUEST_KEY);
    }

    public static function getArgument(string $key): ?string
    {
        $arguments = self::getArguments();

        return $arguments[$key] ?? null;
    }
}
