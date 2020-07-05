<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class CategoryRepository extends AbstractPageRepository
{
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    public function findAll(int $belowPage = null): ?QueryResultInterface
    {
        return $this->execute($belowPage);
    }
}
