<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use TYPO3\CMS\Core\Database\ConnectionPool;
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

        $this->registerArgument('value', null, 'The value', true);
        $this->registerArgument('property', 'string', 'Property name', true);
        $this->registerArgument('format', 'string', 'String or translation key');
        $this->registerArgument('fields', 'array', 'Fields you want to get from database');
    }

    protected function getDatabaseValue(int $id, string $table): ?string
    {
        // Build array of fields
        if (empty($fields = $this->arguments['fields'])) {
            $fields = (array)$GLOBALS['TCA'][$table]['ctrl']['label'];
            if ($labelAlt = $GLOBALS['TCA'][$table]['ctrl']['label_alt']) {
                $fields = array_merge($fields, GeneralUtility::trimExplode(',', $labelAlt));
            }
        }

        // Connect database
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $result = $queryBuilder->select(...$fields)
            ->from($table)
            ->where($queryBuilder->expr()->eq('uid', $id))
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        if (!empty($result)) {
            return implode(' ', $result);
        }

        return null;
    }

    protected function processFallback($value, string $property): ?string
    {
        if($property === 'uid') {
            return $this->getDatabaseValue((int)$value, $this->dataMap->getTableName());
        }

        return null;
    }

    protected function processValue($value)
    {

        // Get the table name
        $property = $this->arguments['property'];
        $columnMap = $property ? $this->dataMap->getColumnMap($property) : null;
        $childTableName = $columnMap && ($childTableName = $columnMap->getChildTableName()) ? $childTableName : null;

        // Get label of the record
        if ($childTableName && $databaseValue = $this->getDatabaseValue((int)$value, $childTableName)) {
            return $databaseValue;
        }

        // Get fallback value
        if ($processedFallback = $this->processFallback($value, $property)) {
            return $processedFallback;
        }

        // Simply return the given value
        return (string)$value;
    }

    public function render(): string
    {

        // Define value
        $value = $this->arguments['value'];

        // Create processed value
        if (is_array($value) || is_string($value)) {
            $processedValue = implode(', ', array_map(function ($v) {
                return $this->processValue($v);
            }, TypeCastService::array($value)));
        } else {
            $processedValue = $this->processValue($value);
        }

        // Set wrap value into format pattern
        if ($format = $this->arguments['format']) {
            if ($translation = LocalizationUtility::translate($format, SettingsService::EXTENSION_KEY, [$processedValue])) {
                return $translation;
            }

            return sprintf($format, $processedValue);
        }

        // Return "blank" value
        return $processedValue;
    }

}
