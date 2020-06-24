<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\IconFactory;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Domain\Model\Post;

class OverrideIconOverlay
{

    protected const TABLE = 'pages';

    protected function getFullRecordData($uid): array
    {
        return BackendUtility::getRecord(self::TABLE, (int)$uid);
    }

    public function postOverlayPriorityLookup(string $table, array $row, array $status, string $iconName = null): ?string
    {
        if ($table === self::TABLE && empty($iconName) && $mapping = $GLOBALS['TYPO3_CONF_VARS']['SYS']['IconFactory']['recordStatusMapping'] ?? null) {

            $doktype = (int)$row['doktype'];

            if (Post::DOKTYPE === $doktype && ($row = $this->getFullRecordData($row['uid'])) && $row['post_top']) {
                return 'overlay-approved';
            }

            if (Category::DOKTYPE === $doktype && ($row = $this->getFullRecordData($row['uid'])) && $row['post_redirect_category']) {
                return 'overlay-shortcut';
            }
        }

        return $iconName;
    }
}
