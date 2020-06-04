<?php

namespace Zeroseven\Z7Blog\TCA;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;
use Zeroseven\HfhProducts\Domain\Model\Category;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\SettingsService;

class ItemsProcFunc
{

    protected function getPageUid(array $config): int
    {
        return GeneralUtility::_GP('id') ?: $config['flexParentDatabaseRow']['pid'];
    }

    protected function getRootPageUid(int $currentPage): int
    {
        $rootLine = GeneralUtility::makeInstance(BackendUtility::class)->BEgetRootLine($currentPage) ?: [];

        foreach ($rootLine as $page) {
            if($page['is_siteroot'] || (int)$page['pid'] === 0) {
                return (int)$page['uid'];
            }
        }

        return 0;
    }

    protected function initializeRepository(RepositoryInterface $repository, bool $setStoragePid): RepositoryInterface
    {
        if($setStoragePid) {

            // Get the storage pid by plugin configuration
            $storagePids = (int)SettingsService::getPluginConfiguration('persistence.storagePid');

            // Define storage pid in repository
            $querySettings = GeneralUtility::makeInstance(ObjectManager::class)->get(Typo3QuerySettings::class);
            $querySettings->setStoragePageIds(GeneralUtility::intExplode(',', $storagePids, true));
            $repository->setDefaultQuerySettings($querySettings);
        }

        return $repository;
    }

    public function getContentLayouts(array &$PA)
    {
        $pagesTsConfig = BackendUtility::getPagesTSconfig($this->getPageUid($PA));
        $key = $PA['flexParentDatabaseRow']['CType'];

        if($key && $options = $pagesTsConfig['tx_z7blog.']['content.'][$key . '.']['layouts.'] ?? []) {
            foreach ($options as $value => $label) {
                $PA['items'][] = [$label, $value, null];
            }
        }
    }

    public function getCategories(array &$PA)
    {

        // Get the current pid
        $rootPageUid = 0;
        if ($currentUid = $this->getPageUid($PA)) {
            $rootPageUid = $this->getRootPageUid($currentUid);
        }

        // Add categories to the items
        foreach (RepositoryService::getCategoryRepository()->findAll($rootPageUid) ?: [] as $category) {
            $PA['items'][] = [$category->getTitle(), $category->getUid(), 'apps-pagetree-blogcategory'];
        }
    }

    public function getAuthors(array &$PA)
    {
        $authorRepository = $this->initializeRepository(RepositoryService::getAuthorRepository(), true);

        // Add authors to the items
        foreach ($authorRepository->findAll() ?: [] as $author) {
            $PA['items'][] = [$author->getFullName(), $author->getUid(), 'plugin-z7blog-author'];
        }
    }

}
