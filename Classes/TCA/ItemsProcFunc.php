<?php

namespace Zeroseven\Z7Blog\TCA;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;
use Zeroseven\Z7Blog\Domain\Demand\CategoryDemand;
use Zeroseven\Z7Blog\Domain\Demand\PostDemand;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\RootlineService;
use Zeroseven\Z7Blog\Service\SettingsService;
use Zeroseven\Z7Blog\Service\TagService;

class ItemsProcFunc
{

    protected function getPageUid(array $config): int
    {
        return GeneralUtility::_GP('id') ?: $config['flexParentDatabaseRow']['pid'];
    }

    protected function getRootPageUid(array $config): int
    {
        if ($currentUid = $this->getPageUid($config)) {
            return RootlineService::getRootPage($currentUid);
        }

        return 0;
    }

    protected function initializeRepository(RepositoryInterface $repository, bool $setStoragePid): RepositoryInterface
    {
        if ($setStoragePid) {

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
        $key = $PA['config']['contentLayoutKey'] ?? $PA['flexParentDatabaseRow']['CType'];

        if ($key && $options = $pagesTsConfig['tx_z7blog.']['content.'][$key . '.']['layouts.'] ?? null) {
            foreach ($options as $value => $label) {
                $PA['items'][] = [$label, $value, null];
            }
        }
    }

    public function getCategories(array &$PA)
    {

        if(($currentPageUid = $this->getPageUid($PA)) > 0) {

            // Get potential page uids of categories
            $rootPageUid = $this->getRootPageUid($PA);
            $potentialPageIds = RootlineService::findPagesBelow($rootPageUid);

            // Get the "auto" category
            $categoryRepository = RepositoryService::getCategoryRepository();
            $autoCategory = ($categoryUid = RootlineService::findCategory($currentPageUid)) ? $categoryRepository->findByUid($categoryUid) : null;

            // Suggest category
            if ($autoCategory) {
                $PA['items'][] = ['LLL:EXT:z7_blog/Resources/Private/Language/locallang_be.xlf:itemsProcFunc.category.auto', '--div--'];
                $PA['items'][] = [$autoCategory->getTitle(), $autoCategory->getUid(), 'apps-pagetree-blogcategory'];
            }

            // Add static options
            $PA['items'][] = ['LLL:EXT:z7_blog/Resources/Private/Language/locallang_be.xlf:itemsProcFunc.category.all', '--div--'];
            $PA['items'][] = ['LLL:EXT:z7_blog/Resources/Private/Language/locallang_be.xlf:itemsProcFunc.category.all.0', 0, 'apps-pagetree-blogcategory'];

            // Add further categories
            if(!empty($potentialPageIds) && $categories = $categoryRepository->findAll(CategoryDemand::makeInstance()->setUids($potentialPageIds))) {
                $PA['items'][] = ['LLL:EXT:z7_blog/Resources/Private/Language/locallang_be.xlf:itemsProcFunc.category.manuel', '--div--'];

                // Add categories to the items
                foreach ($categories as $category) {
                    if($autoCategory === null || $category->getUid() !== $autoCategory->getUid()) {
                        $PA['items'][] = [$category->getTitle(), $category->getUid(), 'apps-pagetree-blogcategory'];
                    }
                }
            }
        } else {
            $PA['items'][] = ['LLL:EXT:z7_blog/Resources/Private/Language/locallang_be.xlf:itemsProcFunc.category.empty', ''];
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

    public function getTopics(array &$PA)
    {
        $topicRepository = $this->initializeRepository(RepositoryService::getTopicRepository(), true);

        // Add topics to the items
        foreach ($topicRepository->findAll() ?: [] as $topic) {
            $PA['items'][] = [$topic->getTitle(), $topic->getUid(), 'plugin-z7blog-topic'];
        }
    }

    public function getTags(array &$PA)
    {
        // Get the current pid
        $rootPageUid = $this->getRootPageUid($PA);

        // Build demand object
        $demand = PostDemand::makeInstance()->setCategory($rootPageUid);

        // Add topics to the items
        foreach (TagService::getTags($demand) ?: [] as $tag) {
            $PA['items'][] = [$tag, $tag, 'plugin-z7blog-tag'];
        }
    }

}
