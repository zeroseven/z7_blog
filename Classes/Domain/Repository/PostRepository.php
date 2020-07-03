<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

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
        if ($demand && $demand->getOrdering()) {

            // Examples: "date_desc", "title_asc", "title",
            if (preg_match('/([a-zA-Z]+)(?:_(asc|desc))?/', $demand->getOrdering(), $matches) && $property = $matches[1] ?? null) {
                $columnName = $this->objectManager->get(DataMapper::class)->convertPropertyNameToColumnName($property, Post::class);
                $ordering[$columnName] = ($direction = $matches[2] ?? null) && $direction === 'desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;
            }
        } else {
            $ordering['post_date'] = QueryInterface::ORDER_DESCENDING;
        }

        // And at finally by the uid
        $ordering['uid'] = QueryInterface::ORDER_DESCENDING;

        $this->setDefaultOrderings($ordering);
    }

    public function findAll(Demand $demand = null): ?QueryResultInterface
    {

        // Override sorting of the posts
        $this->setOrdering($demand);

        // Abort here, if no demand object is given
        if ($demand === null) {
            return $this->execute();
        }

        // Create query
        $query = $this->createQuery();

        // Array to collect constraints
        $constraints = [];

        // Filter post by author
        if ($author = $demand->getAuthor()) {
            $constraints[] = $query->equals('author', $author);
        }

        // Add topic constraint
        if ($topics = $demand->getTopics()) {
            $constraints[] = $query->in('topics', $topics);
        }

        // Filter post by tags
        if ($tags = $demand->getTags()) {
            $constraints[] = $query->logicalOr(array_map(static function($tag) use($query) {
                return $query->like('tagList', '%' . $tag . '%');
            }, $tags));
        }

        // Set archive mode
        if ($demand->archivedPostsHidden()) {
            $constraints[] = $query->logicalOr([
                $query->equals('archiveDate', 0),
                $query->greaterThan('archiveDate', time())
            ]);
        } elseif ($demand->archivedPostsOnly()) {
            $constraints[] = $query->logicalAnd([
                $query->greaterThan('archiveDate', 1),
                $query->lessThan('archiveDate', time())
            ]);
        }

        // Display only top posts
        if ($demand->topPostsOnly()) {
            $constraints[] = $query->equals('top', 1);
        }

        // Ciao!
        return $this->execute($demand->getCategory(), $constraints);
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
        $constraint = $query->in('uid', $uids);

        // Execute the query
        return $this->execute(null, [$constraint]);
    }

}
