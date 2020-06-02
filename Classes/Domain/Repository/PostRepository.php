<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Zeroseven\Z7Blog\Domain\Model\Post;

class PostRepository extends AbstractPageRepository
{

    public function initializeObject(): void
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    protected function setOrdering(string $sorting = null, bool $topPostsFirst = null): void
    {
        // If top posts first
        if ($topPostsFirst !== false) {
            $ordering['post_top'] = QueryInterface::ORDER_DESCENDING;
        }

        // Override default ordering by propertyName with optional direction
        if($sorting) {

            // Examples: "postDate_desc", "title_asc", "title",
            if (preg_match('/([a-zA-Z]+)(?:_(asc|desc))?/', $sorting, $matches) && $property = $matches[1] ?? null) {
                $columnName = GeneralUtility::makeInstance(DataMapper::class)->convertPropertyNameToColumnName($property, Post::class);
                $ordering[$columnName] = ($direction = $matches[2] ?? null) && $direction === 'desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;
            }
        } else {
            $ordering['post_date'] = QueryInterface::ORDER_DESCENDING;
        }

        // And at finally by the uid
        $ordering['uid'] = QueryInterface::ORDER_DESCENDING;

        $this->setDefaultOrderings($ordering);
    }

    public function findByUid($uid, bool $ignoreRestrictions = null): ?Post
    {

        // Todo: fix for translations on ignored restrictions
        if ($ignoreRestrictions) {
            $query = $this->createQuery();
            $query->setLimit(1);
            $query->matching(
                $query->equals('uid', (int)$uid)
            );

            // Allow hidden pages
            $query->getQuerySettings()->setIgnoreEnableFields(true)->setIncludeDeleted(true)->setRespectStoragePage(false);

            // Get posts and return the first one …
            return ($posts = $query->execute()) ? $posts->getFirst() : null;
        }

        return parent::findByUid($uid);
    }

    public function findAll(int $belowPage = null, int $author = null, array $tags = null, bool $topPostsFirst = null, bool $topOnly = null, bool $archiveMode = null, string $sorting = null): QueryResultInterface
    {

        // Override sorting of the posts
        $this->setOrdering($sorting, $topPostsFirst);

        // Create query
        $query = $this->createQuery();

        // ...

        return $this->findBelowPage($belowPage, $query);
    }

    public function findByAuthor(int $author, bool $topPostsFirst = null, bool $topOnly = null, bool $archiveMode = null, string $sorting = null) {
        return $this->findAll(null, $author, null, $topPostsFirst, $topOnly, $archiveMode, $sorting);
    }

    public function findByTags(array $tags, bool $topPostsFirst = null, bool $topOnly = null, bool $archiveMode = null, string $sorting = null) {
        return $this->findAll(null, null, $tags, $topPostsFirst, $topOnly, $archiveMode, $sorting);
    }


    public function findByUids(array $uids, bool $topPostsFirst = null, string $sorting = null): ObjectStorage
    {
        // Override sorting of the posts
        $this->setOrdering($sorting, $topPostsFirst);

        // Create query
        $query = $this->createQuery();
        $query->matching(
            $query->in('uid', $uids)
        );

        // Execute the query
        return $this->findBelowPage(null, $query);
    }


}
