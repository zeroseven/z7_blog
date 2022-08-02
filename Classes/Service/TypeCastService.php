<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

class TypeCastService
{
    /** @throws Exception */
    protected static function throwException($value, string $expectation = null): void
    {
        throw new Exception(sprintf('Type of "%s" can not be converted to %s.', gettype($value), $expectation ?: debug_backtrace()[1]['function']), 1659427314);
    }

    /** @throws Exception */
    public static function int($value): int
    {
        if ($value === null || is_int($value) || empty($value) || MathUtility::canBeInterpretedAsInteger($value)) {
            return (int)$value;
        }

        if (is_object($value) && $value instanceof AbstractDomainObject) {
            return $value->getUid();
        }

        self::throwException($value);
    }

    /** @throws Exception */
    public static function string($value): string
    {
        if ($value === null || is_string($value) || is_int($value)) {
            return (string)$value;
        }

        self::throwException($value);
    }

    /** @throws Exception */
    public static function array($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || empty($value)) {
            return [];
        }

        if (is_string($value)) {
            return GeneralUtility::trimExplode(',', $value);
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        self::throwException($value);
    }

    /** @throws Exception */
    public static function bool($value): bool
    {
        if ($value === null || !is_array($value) && !is_object($value)) {
            return (bool)$value;
        }

        self::throwException($value);
    }
}
