<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\TCA;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GroupFilter
{

    public function filterDoktypes(array $parameters): array
    {
        if (isset($parameters['values'], $parameters['allowed'])) {

            $values = [];
            $allowed = GeneralUtility::intExplode(',', $parameters['allowed'], true);

            foreach ($parameters['values'] as $value) {
                if (preg_match('/^([a-z_]+)_(\d+)$/', $value, $matches)
                    && ($row = BackendUtility::getRecord($matches[1], (int)$matches[2], 'doktype'))
                    && in_array((int)$row['doktype'], $allowed, true)) {
                    $values[] = $value;
                }
            }

            return $values;
        }

        return [];
    }
}
