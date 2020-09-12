<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

    public function getRootlineAndLanguageConstraints(AbstractDemand $demand, QueryInterface $query): array
    {

        // Build array
        $constraints = [];

        // Stay in the hood
        if (empty($demand->getUids()) && $startPageId = RootlineService::getRootPage()) {
            $constraints[] = $query->in('uid', RootlineService::findPagesBelow($startPageId));
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

    protected function createDemandConstraints(AbstractDemand $demand, QueryInterface $query): array
    {
        $constraints = parent::createDemandConstraints($demand, $query);

        return array_merge($constraints, $this->getRootlineAndLanguageConstraints($demand, $query));
    }

    public function findByUid($uid, bool $ignoreRestrictions = null)
    {

        // Convert the uid to an integer
        $postUid = TypeCastService::int($uid);
        $key = 'post-' . $postUid . ($ignoreRestrictions ? '' : '-ignoredRestrictions');

        // Try to deliver a stored post object
        if ($post = $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['post'][$key] ?? null) {
            return $post;
        }

        // Load post without restrictions
        if ($ignoreRestrictions) {

            $query = $this->createQuery();

            if ((int)GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', 0) > 0) {
                $constraint = $query->equals('l10n_parent', $postUid);
            } else {
                $constraint = $query->equals('uid', $postUid);
            }

            $query->setLimit(1);
            $query->matching($constraint);

            // Allow hidden pages
            $query->getQuerySettings()->setIgnoreEnableFields(true)->setIncludeDeleted(true)->setRespectStoragePage(false);

            // Get posts and store and return the first one …
            return ($posts = $query->execute()) ? $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['post'][$key] = $posts->getFirst() : null;
        }

        // Store and return the post in a familiar way
        return ($post = parent::findByUid($postUid)) ? $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['post'][$key] = $post : null;
    }
}
