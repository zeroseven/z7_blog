<?php

namespace Zeroseven\Z7Blog\TCA;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Service\RepositoryService;

class ItemsProcFunc
{

    protected function getPageUid(array $config): int
    {
        return GeneralUtility::_GP('id') ?: $config['flexParentDatabaseRow']['pid'];
    }

    protected function getUidOfRootPage(int $currentPage): int
    {
        $rootLine = GeneralUtility::makeInstance(BackendUtility::class)->BEgetRootLine($currentPage) ?: [];

        foreach ($rootLine as $page) {
            if($page['is_siteroot'] || (int)$page['pid'] === 0) {
                return (int)$page['uid'];
            }
        }

        return 0;
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
            $rootPageUid = $this->getUidOfRootPage($currentUid);
        }

        // Add categories to the items
        foreach (RepositoryService::getCategoryRepository()->findAll($rootPageUid) ?: [] as $category) {
            $PA['items'][] = [$category->getTitle(), $category->getUid(), 'apps-pagetree-blogcategory'];
        }
    }

}
