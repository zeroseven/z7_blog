<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Utility;

use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ManualOrderUtility
{
    /**
     * Order given objects by a given order reference in array or string form
     *
     * @param $orderReference
     * @param array $objects
     * @return array
     * @throws RuntimeException
     */
    public static function order($orderReference, array $objects): array
    {
        $orderedObjects = [];

        // Prepare empty order array with the uids as key
        if (is_array($orderReference)) {
            foreach ($orderReference as $key) {
                $orderedObjects[$key] = null;
            }
        }
        else if (is_string($orderReference)) {
            foreach (GeneralUtility::intExplode(",", $orderReference) as $key) {
                $orderedObjects[$key] = null;
            }
        }
        else {
            throw new RuntimeException('Order reference must be of type string or array.');
        }

        foreach ($objects as $object) {
            if ($uid = $object->getUid()) {
                $orderedObjects[$uid] = $object;
            }
        }

        return array_filter($orderedObjects, static function($o) { return !empty($o); });

    }
}
