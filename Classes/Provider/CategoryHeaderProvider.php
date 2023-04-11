<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Provider;

use TYPO3\CMS\Backend\Controller\Event\RenderAdditionalContentToRecordListEvent;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Service\RepositoryService;

class CategoryHeaderProvider extends AbstractHeaderProvider
{
    /** @throws AspectNotFoundException */
    public function render(): string
    {

        // Check if the page is a category
        if ((int)($this->row['doktype'] ?? 0) === Category::DOKTYPE) {
            return $this->createView('EXT:z7_blog/Resources/Private/Backend/Templates/WebLayoutHeader/Category.html', [
                // @extensionScannerIgnoreLine
                'category' => RepositoryService::getCategoryRepository()->findByUid($this->id),
                // @extensionScannerIgnoreLine
                'posts' => RepositoryService::getPostRepository()->findByCategory($this->id)
            ])->render();
        }

        return '';
    }
    
    /**
     * Method __invoke
     *
     * @param RenderAdditionalContentToRecordListEvent $event 
     *
     * @return void
     */
    public function __invoke(RenderAdditionalContentToRecordListEvent $event): void
    {
        // Check if the page is a category
        if ((int)($this->row['doktype'] ?? 0) === Category::DOKTYPE) {
            $content = $this->createView('EXT:z7_blog/Resources/Private/Backend/Templates/WebLayoutHeader/Category.html', [
                // @extensionScannerIgnoreLine
                'category' => RepositoryService::getCategoryRepository()->findByUid($this->id),
                // @extensionScannerIgnoreLine
                'posts' => RepositoryService::getPostRepository()->findByCategory($this->id)
            ])->render();

            $event->addContentAbove($content);
        }
    }
}
