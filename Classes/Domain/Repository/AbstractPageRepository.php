<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

abstract class AbstractPageRepository extends Repository
{

    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param int|null $start
     * @param QueryInterface|null $query
     * @return array|QueryResultInterface
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\InvalidNumberOfConstraintsException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnexpectedTypeException
     */
    public function findBelowPage(int $start = null, QueryInterface $query = null)
    {
        // Set the rootpage as fallback for "Automatic [0]"
        if ((int)$start === 0) {
            $start = (int)$GLOBALS['TSFE']->domainStartPage;
        }

        // Get pages beyond the given page in the tree
        $pids = GeneralUtility::intExplode(',', GeneralUtility::makeInstance(QueryGenerator::class)->getTreeList($start, 99));

        // Set constraint
        if ($query === null) {
            $query = $this->createQuery();
            $query->matching($query->in('pid', $pids));
        } else if ($query->getConstraint()) {
            $previousConstraint = $query->getConstraint();
            $query->matching(
                $query->logicalAnd([
                    $previousConstraint,
                    $query->in('pid', $pids)
                ])
            );
        } else {
            $query->matching(
                $query->logicalAnd([
                    $query->in('pid', $pids)
                ])
            );
        }

        // Execute the query
        return $query->execute();
    }
}
