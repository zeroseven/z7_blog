<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class CategoryRepository extends AbstractPageRepository
{
    public function findAll(int $belowPage = null): ?QueryResultInterface
    {
        return $this->execute($belowPage);
    }
}
