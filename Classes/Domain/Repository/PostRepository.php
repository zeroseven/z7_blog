<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Zeroseven\Z7Blog\Domain\Demand\AbstractDemand;
use Zeroseven\Z7Blog\Domain\Demand\PostDemand;

class PostRepository extends AbstractPageRepository
{

    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'uid' => QueryInterface::ORDER_ASCENDING
    ];

    protected function setOrdering(AbstractDemand $demand = null): void
    {

        // Set orderings
        parent::setOrdering($demand);

        // If top posts first
        if ($demand && $demand->topPostsFirst()) {
            $this->setDefaultOrderings(array_merge(['post_top' => QueryInterface::ORDER_DESCENDING], $this->defaultOrderings));
        }
    }

    public function findByDemand(AbstractDemand $demand): ?QueryResultInterface
    {

        // Override sorting of the posts
        $this->setOrdering($demand);

        // Create query
        $query = $this->createQuery();

        // Get constraints of demand object
        $constraints = $this->createDemandConstraints($demand, $query);

        // Set archive mode
        if ($demand->archivedPostsHidden()) {
            $constraints[] = $query->logicalOr([
                $query->equals('archiveDate', 0),
                $query->greaterThan('archiveDate', time())
            ]);
        } elseif ($demand->archivedPostsOnly()) {
            $constraints[] = $query->logicalAnd([
                $query->greaterThan('archiveDate', 1),
                $query->lessThan('archiveDate', time())
            ]);
        }

        // Display only top posts
        if ($demand->topPostsOnly()) {
            $constraints[] = $query->equals('top', 1);
        }

        // Ciao!
        return $this->execute($demand->getCategory(), $constraints);
    }

    public function findAll(): ?QueryResultInterface
    {
        return $this->findByDemand(PostDemand::makeInstance());
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

    public function findByUids($uids, PostDemand $demand = null): ?QueryResultInterface
    {
        return $this->findByDemand(($demand ?: PostDemand::makeInstance())->setUids($uids));
    }

}
