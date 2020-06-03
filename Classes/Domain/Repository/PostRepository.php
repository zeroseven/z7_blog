<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Domain\Model\Post;

class PostRepository extends AbstractPageRepository
{

    protected function setOrdering(Demand $demand = null): void
    {

        // If top posts first
        if ($demand && $demand->topPostsFirst()) {
            $ordering['post_top'] = QueryInterface::ORDER_DESCENDING;
        }

        // Override default ordering by propertyName with optional direction
        if ($demand && $demand->getSorting()) {

            // Examples: "date_desc", "title_asc", "title",
            if (preg_match('/([a-zA-Z]+)(?:_(asc|desc))?/', $demand->getSorting(), $matches) && $property = $matches[1] ?? null) {
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

    public function findAll(Demand $demand = null): ?QueryResultInterface
    {

        // Override sorting of the posts
        $this->setOrdering($demand);

        // Create query
        $query = $this->createQuery();

        // Array to collect constraints
        $constraints = [];

        // Filter post by author
        if ($author = $demand->getAuthor()) {
            $constraints[] = $query->equals('author', $author);
        }

        // Add topic constraint
        if ($topic = $demand->getTopic()) {
            $constraints[] = $query->contains('topic', $topic);
        }

        // Filter post by tags
        if ($tags = $demand->getTags()) {
            foreach ($tags as $tag) {
                $constraints[] = $query->like('tags', '%' . $tag . '%');
            }
        }

        // Set archive mode
        if ($demand->archivedPostsHidden()) {
            $constraints[] = $query->logicalOr([
                $query->equals('archive', 0),
                $query->greaterThan('archive', time())
            ]);
        } elseif ($demand->archivedPostsOnly()) {
            $constraints[] = $query->logicalAnd([
                $query->greaterThan('archive', 1),
                $query->lessThan('archive', time())
            ]);
        }

        // Display only top posts
        if ($demand->topPostsOnly()) {
            $constraints[] = $query->equals('top', 1);
        }

        // Ciao!
        $this->execute($demand->getBelowPage(), $constraints);
    }

    public function findByAuthor(int $author, Demand $demand = null): ?QueryResultInterface
    {
        return $this->findAll(($demand ?: Demand::makeInstance())->setAuthor($author));
    }

    public function findByTags(array $tags, Demand $demand = null): ?QueryResultInterface
    {
        return $this->findAll(($demand ?: Demand::makeInstance())->setTags($tags));
    }

    public function findByTopic(int $topic, Demand $demand = null): ?QueryResultInterface
    {
        return $this->findAll(($demand ?: Demand::makeInstance())->setTopic($topic));
    }

    public function findByUids(array $uids, Demand $demand = null): ?QueryResultInterface
    {
        // Override sorting of the posts
        $this->setOrdering($demand);

        // Create query constraints
        $query = $this->createQuery();
        $constraints = $query->in('uid', $uids);

        // Execute the query
        return $this->execute(null, $constraints);
    }


}
