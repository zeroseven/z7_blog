<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\DrawHeaderHook;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;

class PostHeaderHook extends AbstractHeaderHook
{


    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getPropertyPermissions(): array
    {

        $class = Post::class;
        $dataMap = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class)->getDataMap($class);
        $backendUserAuthentication = $this->getBackendUser();

        // Get properties
        $properties = array_map(static function ($reflection) {
            return $reflection->name;
        }, GeneralUtility::makeInstance(\ReflectionClass::class, $class)->getProperties() ?? []);

        // Filter allowed properties
        return array_flip(array_filter($properties, static function ($property) use ($dataMap, $backendUserAuthentication) {
            return ($columnMap = $dataMap->getColumnMap($property)) && ((($table = $columnMap->getChildTableName()) && $backendUserAuthentication->check('tables_select', $table)) || $backendUserAuthentication->check('non_exclude_fields', $dataMap->getTableName() . ':' . $columnMap->getColumnName()));
        }));
    }

    public function render(): string
    {

        // Check if the page is a category
        if ((int)$this->row['doktype'] === Post::DOKTYPE) {
            return $this->createView('EXT:z7_blog/Resources/Private/Backend/Templates/Post/Info.html', [
                'post' => RepositoryService::getPostRepository()->findByUid($this->id, true),
                'propertyPermissions' => $this->getPropertyPermissions()
            ])->render();
        }

        return '';
    }

}
