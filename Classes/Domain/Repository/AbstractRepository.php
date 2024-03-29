<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\ColumnMap\Relation as ColumnMapRelation;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Zeroseven\Z7Blog\Domain\Demand\AbstractDemand;
use Zeroseven\Z7Blog\Service\TypeCastService;

abstract class AbstractRepository extends Repository
{
    protected function setOrdering(AbstractDemand $demand = null): void
    {

        // Override default ordering by propertyName with optional direction
        try {
            if (
                $demand
                && $demand->getOrdering()
                && preg_match('/([a-zA-Z]+)(?:_(asc|desc))?/', $demand->getOrdering(), $matches) // Examples: "date_desc", "title_asc", "title",
                && ($property = $matches[1] ?? null)
                && ($dataMapper = GeneralUtility::makeInstance(DataMapper::class))
                && ($columnMap = $dataMapper->getDataMap($this->objectType)->getColumnMap($property))
                && ($columnName = $columnMap->getColumnName())
            ) {
                $this->setDefaultOrderings([
                    $columnName => ($direction = $matches[2] ?? null) && $direction === 'desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING
                ]);
            }
        } catch (Exception $e) {
        }
    }

    /** @throws AspectNotFoundException | InvalidQueryException | Exception */
    protected function createDemandConstraints(AbstractDemand $demand, QueryInterface $query): array
    {
        $constraints = [];
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

        // Search for specific uids
        if ($uids = $demand->getUids()){
            if (($langaugeUid = (int)GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', 0)) > 0) {
                $dataMap = $dataMapper->getDataMap($this->objectType);

                $constraints[] = $query->logicalAnd(
                    $query->in($dataMap->getTranslationOriginColumnName(), $uids),
                    $query->equals($dataMap->getLanguageIdColumnName(), $langaugeUid)
                );
            } else {
                $constraints[] = $query->in('uid', $uids);
            }
        }

        /**
         * This function has been tested with the current scope of the expansion.
         * With more practical tests, there will likely be a little more.
         */
        foreach ($demand->getTypeMapping() as $propertyName => $type) {
            if (($value = $demand->getProperty($propertyName)) && $columnMap = $dataMapper->getDataMap($this->objectType)->getColumnMap($propertyName)) {
                if ($type === 'array') {
                    if (in_array($columnMap->getTypeOfRelation(), [ColumnMapRelation::HAS_MANY, ColumnMapRelation::HAS_AND_BELONGS_TO_MANY], true)) {
                        $constraints[] = $query->logicalOr(...array_map(static function ($v) use ($query, $propertyName) {
                            return $query->contains($propertyName, $v);
                        }, $value));
                    } elseif ($columnMap->getTypeOfRelation() === ColumnMapRelation::NONE) {
                        $constraints[] = $query->logicalOr(...array_map(static function ($v) use ($query, $propertyName) {
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

    protected function orderByUid($orderReference, QueryResultInterface $objects): QueryResultInterface
    {
        // Create ordered list
        $sortedList = array_fill_keys(TypeCastService::array($orderReference), null);

        // Assign objects
        foreach ($objects as $object) {
            if ($uid = $object->getUid()) {
                $sortedList[$uid] = $object;
            }
        }

        // Remove empty objects
        $sortedList = array_filter($sortedList, static function ($o) {
            return $o;
        });

        // Resort objects in result
        foreach ($objects as $key => $value) {
            $objects->offsetSet($key, array_shift($sortedList));
        }

        return $objects;
    }

    public function findByDemand(AbstractDemand $demand): ?QueryResultInterface
    {

        // Override sorting
        $this->setOrdering($demand);

        // Create query
        $query = $this->createQuery();

        // Apply constraints
        if (!empty($constraints = $this->createDemandConstraints($demand, $query))) {
            $query->matching(
                $query->logicalAnd(...$constraints)
            );
        }

        // Execute
        if ($demand->getOrdering() === 'manual' && $uids = $demand->getUids()) {
            return $this->orderByUid($uids, $query->execute());
        }

        return $query->execute();
    }

    public function findAll(AbstractDemand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand($demand ?: $this->initializeDemand());
    }

    public function findByUids($uids, AbstractDemand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: $this->initializeDemand())->setUids($uids));
    }
}
