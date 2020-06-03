<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class CategoryRepository extends AbstractRepository
{
    public function findAll(int $belowPage = null): ?QueryResultInterface
    {
        return $this->executeWithDefaults($belowPage);
    }
}
