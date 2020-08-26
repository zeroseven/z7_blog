<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Info;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\SettingsService;

class FilterViewHelper extends AbstractTagBasedViewHelper
{

    /** @var string */
    protected $tagName = 'p';

    /** @var DataMapper */
    protected $dataMapper;

    public function __construct()
    {
        parent::__construct();

        $this->dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerUniversalTagAttributes();
        $this->registerArgument('value', null, 'The value', true);
        $this->registerArgument('property', 'string', 'Property name', true);
        $this->registerArgument('objectType', 'string', 'Extbase object', false, Post::class);
    }

    protected function processValue($value)
    {

        // Get the table name
        $property = $this->arguments['property'];
        $dataMapper = $this->dataMapper->getDataMap($this->arguments['objectType']);
        $columnMap = $dataMapper->getColumnMap($property);
        $childTableName = $columnMap && ($childTableName = $columnMap->getChildTableName()) ? $childTableName : $dataMapper->getTableName();

        // Get label of the record
        if ($childTableName) {

            // Build array of fields
            $fields = (array)$GLOBALS['TCA'][$childTableName]['ctrl']['label'];
            if ($labelAlt = $GLOBALS['TCA'][$childTableName]['ctrl']['label_alt']) {
                $fields = array_merge($fields, GeneralUtility::trimExplode(',', $labelAlt));
            }

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

        $value = $this->arguments['value'];

        // Create processed value
        if (is_array($value)) {
            $processedValue = implode(', ', array_map(function ($v) {
                return $this->processValue($v);
            }, $value));
        } else {
            $processedValue = $this->processValue($value);
        }

        // Set content
        $this->tag->setContent(LocalizationUtility::translate('filter.' . $this->arguments['property'], SettingsService::EXTENSION_KEY, [$processedValue]));

        // Add attributes
        $this->tag->addAttribute('role', 'status');

        return $this->tag->render();
    }

}
