<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\ColumnMap;
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

    protected function createDemandConstraints(Demand $demand, QueryInterface $query): array
    {
        $constraints = [];
        $dataMapper = $this->objectManager->get(DataMapper::class);

        /**
         * This function has been tested with the current scope of the expansion.
         * With more practical tests, there will likely be a little more.
         */
        foreach ($demand->getTypeMapping() as $propertyName => $type) {
            if (($value = $demand->getProperty($propertyName)) && $columnMap = $dataMapper->getDataMap(Post::class)->getColumnMap($propertyName)) {
                if ($type === 'array') {
                    if (in_array($columnMap->getTypeOfRelation(), [ColumnMap::RELATION_HAS_MANY, ColumnMap::RELATION_HAS_AND_BELONGS_TO_MANY], true)) {
                        $constraints[] = $query->logicalOr(array_map(static function ($v) use ($query, $propertyName) {
                            return $query->contains($propertyName, $v);
                        }, $value));
                    } elseif ($columnMap->getTypeOfRelation() === ColumnMap::RELATION_NONE) {
                        $constraints[] = $query->logicalOr(array_map(static function ($v) use ($query, $propertyName) {
                            return $query->like($propertyName, '%' . $v . '%');
                        }, $value));
                    } else {
                        $constraints[] = $query->contains($propertyName, $value);
                    }
                } elseif ($type === 'string') {
                    $constraints[] = $query->like($propertyName, '%' . $value . '%');
                } else {
                    $constraints[] = $query->equals($propertyName, $value);
                }
            }
        }

        return $constraints;
    }

    public function findByDemand(Demand $demand): ?QueryResultInterface
    {

        // Override sorting of the posts
        $this->setOrdering($demand);

        // Create query
        $query = $this->createQuery();

        // Get constraints of demand object
        $constraints = $this->createDemandConstraints($demand, $query);

        // Search for specific uids
        if ($uids = $demand->getUids()) {
            $constraints[] = $query->in('uid', $uids);
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

    public function findAll(Demand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand($demand ?: Demand::makeInstance());
    }

    public function findByAuthor(int $author, Demand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: Demand::makeInstance())->setAuthor($author));
    }

    public function findByTags(array $tags, Demand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: Demand::makeInstance())->setTags($tags));
    }

    public function findByTopics(array $topics, Demand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: Demand::makeInstance())->setTopic($topics));
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
