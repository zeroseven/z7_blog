<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Utility;

use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Zeroseven\Z7Blog\Service\TypeCastService;

class ManualOrderUtility
{
    /**
     * Order given objects by a given order reference in array or string form
     *
     * @param array|string $orderReference
     * @param array $objects
     * @param bool|null $removeEmptyObjects
     * @return array
     */
    public static function order($orderReference, array $objects, bool $removeEmptyObjects = null): array
    {

        // Create ordered list
        $orderedObjects = array_fill_keys(TypeCastService::array($orderReference), null);

        // Assign objects
        foreach ($objects as $object) {
            if ($uid = $object->getUid()) {
                $orderedObjects[$uid] = $object;
            }
        }

        // Return ordered object list
        return $removeEmptyObjects === false ? $orderedObjects : array_filter($orderedObjects, static function ($o) {
            return !empty($o);
        });

    }
}
