<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
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

    public function getDefaultQuerySettings(): QuerySettingsInterface
    {
        return $this->defaultQuerySettings;
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
