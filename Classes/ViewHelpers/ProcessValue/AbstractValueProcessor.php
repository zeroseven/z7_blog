<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use Doctrine\DBAL\DBALException;
use Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMap;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Zeroseven\Z7Blog\Service\SettingsService;
use Zeroseven\Z7Blog\Service\TypeCastService;

class AbstractValueProcessor extends AbstractViewHelper
{
    /** @var string */
    protected $objectType;

    /** @var DataMap */
    protected $dataMap;

    public function __construct()
    {
        $this->dataMap = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class)->getDataMap($this->objectType);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('value', 'mixed', 'The value', true);
        $this->registerArgument('property', 'string', 'Property name');
        $this->registerArgument('format', 'string', 'String or translation key');
        $this->registerArgument('fields', 'array', 'Fields you want to get from database');
    }

    /** @throws AspectNotFoundException | DBALException */
    protected function getDatabaseValue(int $id, string $table): ?string
    {
        // Build array of fields
        if (empty($fields = $this->arguments['fields'])) {
            $fields = (array)($GLOBALS['TCA'][$table]['ctrl']['label'] ?? []);
            if ($labelAlt = ($GLOBALS['TCA'][$table]['ctrl']['label_alt'] ?? '')) {
                $fields = array_merge($fields, GeneralUtility::trimExplode(',', $labelAlt));
            }
        }

        // Get queryBuilder
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        // Create basic query
        $query = $queryBuilder->select(...$fields)->from($table)->setMaxResults(1);

        // Add default restrictions
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));

        // Add constraints
        if ($sysLanguageUid = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id')) {
            $query->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
                ),
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(-1, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
                )
            ));
        } else {
            $query->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)));
        }

        // Execute query and return result
        return empty($result = $query->execute()->fetch()) ? null : implode(' ', $result);
    }

    protected function processFallback($value, string $property = null): ?string
    {
        return null;
    }

    protected function processValue($value)
    {

        $property = $this->arguments['property'] ?? '';
        $tableName = $this->dataMap->getTableName();

        // Override table name
        if (($columnMap = $this->dataMap->getColumnMap($property)) && ($childTableName = $columnMap->getChildTableName())) {
            $tableName = $childTableName;
        }

        // Get label of the record
        if ($tableName && $databaseValue = $this->getDatabaseValue((int)$value, $tableName)) {
            return $databaseValue;
        }

        // Get fallback value
        if ($processedFallback = $this->processFallback($value, $property)) {
            return $processedFallback;
        }

        // Simply return the given value
        return (string)$value;
    }

    /** @throws Exception */
    public function render(): string
    {
        // Define value
        $value = $this->arguments['value'] ?? null;

        // Create processed value
        if (is_array($value) || is_string($value)) {
            $processedValue = implode(', ', array_map(function ($v) {
                return $this->processValue($v);
            }, TypeCastService::array($value)));
        } else {
            $processedValue = $this->processValue($value);
        }

        // Set wrap value into format pattern
        if ($format = $this->arguments['format'] ?? '') {
            if ($translation = LocalizationUtility::translate($format, SettingsService::EXTENSION_KEY, [$processedValue])) {
                return $translation;
            }

            return sprintf($format, $processedValue);
        }

        // Return "blank" value
        return $processedValue;
    }
}
