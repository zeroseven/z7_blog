<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

class CategoryRepository extends AbstractRepository
{
    public function findAll(int $belowPage = null)
    {
        return $this->findBelowPage($belowPage);
    }
}
