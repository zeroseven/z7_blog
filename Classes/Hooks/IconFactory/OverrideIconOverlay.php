<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\IconFactory;

use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;

class OverrideIconOverlay
{
    public function postOverlayPriorityLookup(string $table, array $row, array $status, string $iconName = null): ?string
    {
        if (isset($row['doktype'], $row['uid']) && $table === 'pages' && empty($iconName)) {
            $doktype = (int)$row['doktype'];

            try {
                if (Post::DOKTYPE === $doktype && $post = RepositoryService::getPostRepository()->findByUid($row['uid'], true)) {
                    if ($post->isArchived()) {
                        return 'overlay-scheduled';
                    }

                    if ($post->isTop()) {
                        return 'overlay-approved';
                    }
                }

                if (Category::DOKTYPE === $doktype && $category = RepositoryService::getCategoryRepository()->findByUid($row['uid'], true)) {
                    if ($category->isRedirect()) {
                        return 'overlay-shortcut';
                    }
                }
            } catch (AspectNotFoundException $e) {
            }
        }

        return $iconName;
    }
}
