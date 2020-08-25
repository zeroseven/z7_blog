<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Domain\Demand\AbstractDemand;

class RequestService
{

    public const REQUEST_KEY = 'tx_z7blog_list';

    public static function getArguments(string $requestKey = null): array
    {
        return GeneralUtility::_GP($requestKey ?: self::REQUEST_KEY) ?: [];
    }

    public static function getArgumentDifference(array $base, AbstractDemand $demand): array
    {
        $result = [];

        foreach ($demand->getParameterArray(false) as $parameter => $value) {
            if (
                $parameter === 'list_id'
                || ($type = gettype($originalValue = $demand->getParameter($parameter)))
                && (
                    $type !== 'array' && $base[$parameter] !== $value
                    || $type === 'array' && (count(array_diff(TypeCastService::array($base[$parameter]), $originalValue)) || count(array_diff($originalValue, TypeCastService::array($base[$parameter]))))
                )
            ) {
                if (!empty($value)) {
                    $result[$parameter] = $value;
                } elseif (!empty($base[$parameter])) {
                    $result[$parameter] = '';
                }
            }
        }


        return $result;
    }
}
