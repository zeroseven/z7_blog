<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

class TypeCastService
{
    protected static function throwException($value, string $expectation = null): void
    {
        throw new \RuntimeException(sprintf('Type of "%s" can not be converted to %s.', gettype($value), $expectation ?: debug_backtrace()[1]['function']), 1659427314);
    }

    public static function int($value): int
    {
        if (is_int($value) || empty($value) || MathUtility::canBeInterpretedAsInteger($value)) {
            return (int)$value;
        }

        if ($value instanceof AbstractDomainObject) {
            return $value->getUid();
        }

        return 0;
    }

    public static function string($value): string
    {
        if ($value === null || is_string($value) || is_int($value)) {
            return (string)$value;
        }
        return '';
    }

    public static function array($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            return GeneralUtility::trimExplode(',', $value);
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        return [];
    }

    public static function bool($value): bool
    {
        if (!is_array($value) && !is_object($value)) {
            return (bool)$value;
        }

        return false;
    }
}
