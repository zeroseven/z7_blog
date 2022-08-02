<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Repository;

use Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Zeroseven\Z7Blog\Domain\Demand\AbstractDemand;
use Zeroseven\Z7Blog\Service\RootlineService;
use Zeroseven\Z7Blog\Service\SettingsService;
use Zeroseven\Z7Blog\Service\TypeCastService;

abstract class AbstractPageRepository extends AbstractRepository
{
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /** @throws AspectNotFoundException | InvalidQueryException */
    public function getRootlineAndLanguageConstraints(AbstractDemand $demand, QueryInterface $query): array
    {

        // Build array
        $constraints = [];

        // Stay in the hood
        if (empty($demand->getUids()) && $startPageId = RootlineService::getRootPage()) {
            $treeTableField = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', null) ? 'pid' : 'uid';
            $constraints[] = $query->in($treeTableField, RootlineService::findPagesBelow($startPageId));
        }

        // Hide what wants to be hidden
        $constraints[] = $query->equals('nav_hide', 0);

        // Add language constraints
        $constraints[] = $query->logicalOr([
            $query->equals('l18n_cfg', 0),
            $query->logicalAnd([
                $query->greaterThanOrEqual('l18n_cfg', 1),
                $query->greaterThanOrEqual('sys_language_uid', 1),
            ]),
        ]);

        return $constraints;
    }

    /** @throws AspectNotFoundException | InvalidQueryException */
    protected function createDemandConstraints(AbstractDemand $demand, QueryInterface $query): array
    {
        $constraints = parent::createDemandConstraints($demand, $query);

        return array_merge($constraints, $this->getRootlineAndLanguageConstraints($demand, $query));
    }

    /** @throws Exception */
    public function findByUid($uid, bool $ignoreRestrictions = null)
    {

        // Convert the uid to an integer
        $pageUid = TypeCastService::int($uid);
        $key = 'page-' . $pageUid . ($ignoreRestrictions ? '' : '-ignoredRestrictions');

        // Try to deliver a stored page object
        if ($page = $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['page'][$key] ?? null) {
            return $page;
        }

        // Load page without restrictions
        if ($ignoreRestrictions) {
            $query = $this->createQuery();

            if ((int)GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', 0) > 0) {
                $constraint = $query->equals('l10n_parent', $pageUid);
            } else {
                $constraint = $query->equals('uid', $pageUid);
            }

            $query->setLimit(1);
            $query->matching($constraint);

            // Allow hidden pages
            $query->getQuerySettings()->setIgnoreEnableFields(true)->setIncludeDeleted(true)->setRespectStoragePage(false);

            // Get pages and store and return the first one …
            return ($pages = $query->execute()) ? $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['page'][$key] = $pages->getFirst() : null;
        }

        // Store and return the page in a familiar way
        return ($page = parent::findByUid($pageUid)) ? $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['page'][$key] = $page : null;
    }
}
