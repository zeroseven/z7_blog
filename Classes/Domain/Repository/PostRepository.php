<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Zeroseven\Z7Blog\Domain\Demand\AbstractDemand;
use Zeroseven\Z7Blog\Domain\Demand\PostDemand;
use Zeroseven\Z7Blog\Service\RootlineService;

class PostRepository extends AbstractPageRepository
{
    protected $defaultOrderings = [
        'post_date' => QueryInterface::ORDER_DESCENDING,
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    public function getDefaultQuerySettings(): QuerySettingsInterface
    {
        return $this->defaultQuerySettings;
    }

    protected function initializeDemand(): AbstractDemand
    {
        return PostDemand::makeInstance();
    }

    protected function setOrdering(AbstractDemand $demand = null): void
    {

        // Set orderings
        parent::setOrdering($demand);

        // If top posts first
        if ($demand && $demand->topPostsFirst()) {
            $this->setDefaultOrderings(array_merge(['post_top' => QueryInterface::ORDER_DESCENDING], $this->defaultOrderings));
        }
    }

    protected function createDemandConstraints(AbstractDemand $originalDemand, QueryInterface $query): array
    {

        // Create a copy of demand object, that can be modified
        $demand = clone $originalDemand;

        // Create uids in given category
        if (empty($demand->getUids()) && $category = $demand->getCategory()) {
            $uids = RootlineService::findPagesBelow($category);
            $demand->setUids($uids);
        }

        // Get the default demand constraints
        $constraints = parent::createDemandConstraints($demand, $query);

        // Set archive mode
        if ($demand->archivedPostsHidden()) {
            $constraints[] = $query->logicalOr(
                $query->equals('archiveDate', 0),
                $query->greaterThan('archiveDate', time())
            );
        } elseif ($demand->archivedPostsOnly()) {
            $constraints[] = $query->logicalAnd(
                $query->greaterThan('archiveDate', 1),
                $query->lessThan('archiveDate', time())
            );
        }

        // Display only top posts
        if ($demand->topPostsOnly()) {
            $constraints[] = $query->equals('top', 1);
        }

        return $constraints;
    }

    public function findByCategory($category, PostDemand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: PostDemand::makeInstance())->setCategory($category));
    }

    public function findByAuthor($author, PostDemand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: PostDemand::makeInstance())->setAuthor($author));
    }

    public function findByTags($tags, PostDemand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: PostDemand::makeInstance())->setTags($tags));
    }

    public function findByTopics($topics, PostDemand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: PostDemand::makeInstance())->setTopic($topics));
    }
}
