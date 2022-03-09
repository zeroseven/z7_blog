<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\WebLayoutHeader;

use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Service\RepositoryService;

class CategoryHeader extends AbstractHeader
{
    /** @throws AspectNotFoundException */
    public function render(): string
    {

        // Check if the page is a category
        if (isset($this->row['doktype']) && (int)$this->row['doktype'] === Category::DOKTYPE) {
            return $this->createView('EXT:z7_blog/Resources/Private/Backend/Templates/WebLayoutHeader/Category.html', [
                'category' => RepositoryService::getCategoryRepository()->findByUid($this->id),
                'posts' => RepositoryService::getPostRepository()->findByCategory($this->id)
            ])->render();
        }

        return '';
    }
}
