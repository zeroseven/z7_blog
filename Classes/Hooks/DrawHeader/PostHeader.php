<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\DrawHeader;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;

class PostHeader extends AbstractHeader
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

        $permissions = [];
        foreach (GeneralUtility::makeInstance(\ReflectionClass::class, $class)->getProperties() ?? [] as $reflection) {
            if($property = $reflection->name) {
                $permissions[$property] = ($columnMap = $dataMap->getColumnMap($property)) && ((($table = $columnMap->getChildTableName()) && $backendUserAuthentication->check('tables_select', $table)) || $backendUserAuthentication->check('non_exclude_fields', $dataMap->getTableName() . ':' . $columnMap->getColumnName()));
            }
        }

        return $permissions;
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
