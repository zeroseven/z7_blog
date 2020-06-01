<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

class CategoryRepository extends AbstractRepository
{
    public function findAll(int $pageUid = null)
    {
        return $this->findBelowPage($pageUid);
    }
}
