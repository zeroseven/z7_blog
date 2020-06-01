<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class PostRepository extends AbstractRepository
{

    public function initializeObject(): void
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    protected function createOrdering(string $sorting = null, bool $topPostsFirst = null): void
    {

        // If top posts first
        if ($topPostsFirst) {
            $ordering['post_top'] = QueryInterface::ORDER_DESCENDING;
        }

        // Override default ordering
        switch ($sorting) {
            case 'title_asc':
                $ordering['title'] = QueryInterface::ORDER_ASCENDING;
                break;
            case 'title_desc':
                $ordering['title'] = QueryInterface::ORDER_DESCENDING;
                break;
            case 'date_asc':
                $ordering['post_date'] = QueryInterface::ORDER_ASCENDING;
                break;
            default:
                $ordering['post_date'] = QueryInterface::ORDER_DESCENDING;
                break;
        }

        // And at finally by the uid
        $ordering['uid'] = QueryInterface::ORDER_DESCENDING;

        $this->setDefaultOrderings($ordering);
    }

    public function findAll(int $belowPage = null, bool $topPostsFirst = false, bool $topOnly = null, int $author = null, string $tags = null, bool $archiveMode = null, string $sorting = null): QueryResultInterface
    {

        // Override sorting of the posts
        $this->createQuery($sorting, $topPostsFirst);

        // Create query
        $query = $this->createQuery();

        // ...

        return $this->findBelowPage($belowPage, $query);
    }

    public function findByUid($uid, bool $ignoreRestrictions = null): ?Post
    {

        // Todo: fix for translations on ignored restrictions
        if ($ignoreRestrictions) {

            // Create query
            $query = $this->createQuery();
            $query->setLimit(1);
            $query->matching(
                $query->equals('uid', (int)$uid)
            );

            // Allow hidden
            $query->getQuerySettings()->setIgnoreEnableFields(true)->setIncludeDeleted(true)->setRespectStoragePage(false);

            // Get events and return the first one …
            $events = $query->execute();

            return $events ? $events->getFirst() : null;
        }

        return parent::findByUid($uid);
    }


    public function findByUids(array $uids): ObjectStorage
    {
        $objectStorage = GeneralUtility::makeInstance(ObjectStorage::class);

        foreach ($uids ?? [] as $uid) {
            if ($post = $this->findByUid($uid)) {
                $objectStorage->attach($post);
            }
        }

        return $objectStorage;
    }
}
