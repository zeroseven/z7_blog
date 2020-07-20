<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\ColumnMap;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Zeroseven\Z7Blog\Domain\Demand\AbstractDemand;
use Zeroseven\Z7Blog\Domain\Model\Post;

abstract class AbstractRepository extends Repository
{

    protected function setOrdering(AbstractDemand $demand = null): void
    {

        // Override default ordering by propertyName with optional direction
        if (
            $demand
            && $demand->getOrdering()
            && preg_match('/([a-zA-Z]+)(?:_(asc|desc))?/', $demand->getOrdering(), $matches) // Examples: "date_desc", "title_asc", "title",
            && ($property = $matches[1] ?? null)
            && ($dataMapper = $this->objectManager->get(DataMapper::class))
            && ($columnMap = $dataMapper->getDataMap(Post::class)->getColumnMap($property)) // Todo: get requested model
        ) {
            $ordering[$columnMap->getColumnName()] = ($direction = $matches[2] ?? null) && $direction === 'desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;
        } elseif(!empty($this->defaultOrderings)) {
            $ordering = $this->defaultOrderings;
        }

        // And at finally by the uid
        $ordering['uid'] = QueryInterface::ORDER_DESCENDING;

        // Store the array
        $this->setDefaultOrderings($ordering);
    }

    protected function createDemandConstraints(AbstractDemand $demand, QueryInterface $query = null): array
    {
        $constraints = [];
        $dataMapper = $this->objectManager->get(DataMapper::class);

        // Create query if not exists
        if($query === null) {
            $query = $this->createQuery();
        }

        // Search for specific uids
        if ($uids = $demand->getUids()) {
            $constraints[] = $query->in('uid', $uids);
        }

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

    public function findByDemand(AbstractDemand $demand): ?QueryResultInterface
    {
        \TYPO3\CMS\Core\Utility\DebugUtility::debug($this);
        // Override sorting of the posts
        $this->setOrdering($demand);

        // Create query
        $query = $this->createQuery();

        // Apply constraints
        $query->matching(
            $this->logicalAnd($this->createDemandConstraints($demand, $query))
        );

        // Ciao!
        return $query->execute();
    }

}
