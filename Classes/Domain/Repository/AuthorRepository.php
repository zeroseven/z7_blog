<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class AuthorRepository extends Repository
{

    public function findByUids(array $uids): array
    {
        // Keep the sorting of the given list
        return array_map(function($uid) {
            return $this->findByUid($uid);
        }, $uids);
    }

}
