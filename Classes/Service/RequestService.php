<?php
declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Utility\GlobalUtility;

class RequestService
{
    public const REQUEST_KEY = 'tx_z7blog_list';

    public static function getArguments(): array
    {
        return GlobalUtility::getRequestParameter(self::REQUEST_KEY) ?? [];
    }

    public static function getArgument(string $key): ?string
    {
        $arguments = self::getArguments();

        return $arguments[$key] ?? null;
    }
}
