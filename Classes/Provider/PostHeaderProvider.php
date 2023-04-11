<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Provider;

use TYPO3\CMS\Backend\Controller\Event\RenderAdditionalContentToRecordListEvent;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;

class PostHeaderProvider extends AbstractHeaderProvider
{
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getPropertyPermissions(): array
    {
        $class = Post::class;
        $dataMap = GeneralUtility::makeInstance(DataMapper::class)->getDataMap($class);
        $backendUserAuthentication = $this->getBackendUser();

        $permissions = [];
        foreach (GeneralUtility::makeInstance(\ReflectionClass::class, $class)->getProperties() ?? [] as $reflection) {
            if ($property = $reflection->name) {
                $permissions[$property] = ($columnMap = $dataMap->getColumnMap($property)) && ((($table = $columnMap->getChildTableName()) && $backendUserAuthentication->check('tables_select', $table)) || $backendUserAuthentication->check('non_exclude_fields', $dataMap->getTableName() . ':' . $columnMap->getColumnName()));
            }
        }

        return $permissions;
    }
    
    /**
     * Method __invoke
     *
     * @param RenderAdditionalContentToRecordListEvent $event 
     *
     * @return void
     */
    public function __invoke(RenderAdditionalContentToRecordListEvent $event): void
    {
        // Check if the page is a post
        if ((int)($this->row['doktype'] ?? 0) === Post::DOKTYPE) {
            // @extensionScannerIgnoreLine
            $content = $this->createView('EXT:z7_blog/Resources/Private/Backend/Templates/WebLayoutHeader/Post.html', [
                'post' => RepositoryService::getPostRepository()->findByUid($this->id, true),
                'propertyPermissions' => $this->getPropertyPermissions()
            ])->render();

            $event->addContentAbove($content);
        }
    }
}
