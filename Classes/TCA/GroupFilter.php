<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\TCA;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GroupFilter
{
    public function filterTypes(array $parameters): array
    {
        $table = $parameters['tcaFieldConfig']['foreign_table'] ?? '';
        $type = $GLOBALS['TCA'][$table]['ctrl']['type'] ?? null;

        if ($type && isset($parameters['values'], $parameters['allowed'])) {
            $values = [];
            $allowedTypes = GeneralUtility::trimExplode(',', $parameters['allowed'], true);

            foreach ($parameters['values'] as $value) {
                if (preg_match('/^([a-z_]+)_(\d+)$/', $value, $matches)
                    && ($table === $matches[1])
                    && ($row = BackendUtility::getRecord($matches[1], (string)$matches[2], $type))
                    && in_array((string)$row[$type], $allowedTypes, true)) {
                    $values[] = $value;
                }
            }

            return $values;
        }

        return [];
    }
}
