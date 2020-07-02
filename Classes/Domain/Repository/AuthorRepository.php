<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class AuthorRepository extends Repository
{

    protected $defaultOrderings = [
        'firstName' => QueryInterface::ORDER_ASCENDING,
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    public function findByUids(array $uids): array
    {
        // Keep the sorting of the given list
        return array_map(function($uid) {
            return $this->findByUid($uid);
        }, $uids);
    }

}
