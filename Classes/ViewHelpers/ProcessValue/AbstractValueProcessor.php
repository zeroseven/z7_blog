<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Zeroseven\Z7Blog\Service\SettingsService;

class AbstractValueProcessor extends AbstractViewHelper
{
    /** @var string */
    protected $objectType;

    /** @var DataMapper */
    protected $dataMapper;

    public function __construct()
    {
        $this->dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('value', null, 'The value', true);
        $this->registerArgument('property', 'string', 'Property name');
        $this->registerArgument('format', 'string', 'String or translation key');
        $this->registerArgument('fields', 'array', 'Fields you want to get from database');
    }

    protected function processValue($value)
    {

        // Get the table name
        $property = $this->arguments['property'];
        $dataMapper = $this->dataMapper->getDataMap($this->objectType);
        $columnMap = $property ? $dataMapper->getColumnMap($property) : null;
        $childTableName = $columnMap && ($childTableName = $columnMap->getChildTableName()) ? $childTableName : $dataMapper->getTableName();

        // Get label of the record
        if ($childTableName) {

            // Build array of fields
            if(empty($fields = $this->arguments['fields'])) {
                $fields = (array)$GLOBALS['TCA'][$childTableName]['ctrl']['label'];
                if ($labelAlt = $GLOBALS['TCA'][$childTableName]['ctrl']['label_alt']) {
                    $fields = array_merge($fields, GeneralUtility::trimExplode(',', $labelAlt));
                }
            }

            // Connect database
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($childTableName);
            $result = $queryBuilder->select(...$fields)
                ->from($childTableName)
                ->where($queryBuilder->expr()->eq('uid', (int)$value))
                ->setMaxResults(1)
                ->execute()
                ->fetch();

            if (!empty($result)) {
                return implode(' ', $result);
            }
        }

        // Simply return the given value
        return (string)$value;
    }

    public function render(): string
    {

        // Define value
        $value = $this->arguments['value'];

        // Create processed value
        if (is_array($value)) {
            $processedValue = implode(', ', array_map(function ($v) {
                return $this->processValue($v);
            }, $value));
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
