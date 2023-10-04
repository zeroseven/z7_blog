<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Utility;

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

use Psr\Http\Message\ServerRequestInterface;

/**
 * GlobalUtility
 */
class GlobalUtility
{    
    /**
     * Method getRequest
     *
     * @return ServerRequestInterface
     */
    public static function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
    
    /**
     * Method getGP
     *
     * @param string $key 
     *
     * @return mixed
     */
    public static function getRequestParameter(string $key): mixed
    {
        $request = self::getRequest();
        return $request->getParsedBody()[$key] ?? $request->getQueryParams()[$key] ?? null;
    }
}
