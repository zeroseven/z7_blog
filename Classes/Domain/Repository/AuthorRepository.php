<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Zeroseven\Z7Blog\Service\TypeCastService;

class AuthorRepository extends AbstractRepository
{

    protected $defaultOrderings = [
        'firstName' => QueryInterface::ORDER_ASCENDING,
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    public function findByUids($uids): ?QueryResultInterface
    {
        // Create query
        $query = $this->createQuery();

        // Search for specific uids
        $query->matching(
            $query->in('uid', TypeCastService::array($uids))
        );

        // Ciao!
        return $query->execute();
    }

}
