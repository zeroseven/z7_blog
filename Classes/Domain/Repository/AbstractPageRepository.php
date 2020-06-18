<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Zeroseven\Z7Blog\Service\RootlineService;

abstract class AbstractPageRepository extends Repository
{

    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    public function getDefaultQuerySettings(): QuerySettingsInterface
    {
        return $this->defaultQuerySettings;
    }

    public function getRootlineAndLanguageConstraints(int $startPageId = null): array
    {

        // Set the root page as fallback for "Automatic [0]"
        if (empty($startPageId)) {
            $startPageId = RootlineService::getRootPage();
        }

        // Get pages beyond the given page in the tree
        $pids = GeneralUtility::intExplode(',', GeneralUtility::makeInstance(QueryGenerator::class)->getTreeList($startPageId, 99));

        // Create query
        $query = $this->createQuery();

        // Return constraints
        return [
            $query->in('pid', $pids),
            $query->logicalOr([
                $query->equals('l18n_cfg', 0),
                $query->logicalAnd([
                    $query->greaterThanOrEqual('l18n_cfg', 1),
                    $query->greaterThanOrEqual('sys_language_uid', 1),
                ]),
            ])
        ];
    }

    public function execute(int $startPageId = null, array $constraints = null): ?QueryResultInterface
    {

        // Create query
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(array_merge(
                $this->getRootlineAndLanguageConstraints($startPageId),
                $constraints ?? []
            ))
        );

        // Execute the query
        return $query->execute();
    }
}
