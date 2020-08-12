<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\IconFactory;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;

class OverrideIconOverlay
{

    public function postOverlayPriorityLookup(string $table, array $row, array $status, string $iconName = null): ?string
    {
        if ($table === 'pages' && empty($iconName) && $mapping = $GLOBALS['TYPO3_CONF_VARS']['SYS']['IconFactory']['recordStatusMapping'] ?? null) {

            $doktype = (int)$row['doktype'];

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
        }

        return $iconName;
    }
}
