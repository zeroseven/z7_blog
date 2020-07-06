<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\DrawHeaderHook;

use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Service\RepositoryService;

class CategoryHeaderHook extends AbstractHeaderHook
{

    public function render(): string
    {

        // Check if the page is a category
        if ((int)$this->row['doktype'] === Category::DOKTYPE) {
            $demand = Demand::makeInstance()->setProperty('category', $this->id);
            return $this->createView('EXT:z7_blog/Resources/Private/Backend/Templates/Category/Info.html', [
                'category' => RepositoryService::getCategoryRepository()->findByUid($this->id),
                'posts' => RepositoryService::getPostRepository()->findAll($demand)
            ])->render();
        }

        return '';
    }

}
