<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Zeroseven\Z7Blog\Domain\Demand\AbstractDemand;
use Zeroseven\Z7Blog\Domain\Demand\CategoryDemand;

class CategoryRepository extends AbstractPageRepository
{
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    protected function initializeDemand(): AbstractDemand
    {
        return CategoryDemand::makeInstance();
    }
}
