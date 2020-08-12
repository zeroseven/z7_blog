<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Zeroseven\Z7Blog\Domain\Model\Category;

class RootlineService
{

    protected static function getCurrentPage(): int
    {
        if (isset($GLOBALS['TSFE']) && $GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            return (int)$GLOBALS['TSFE']->id;
        }

        if ($id = GeneralUtility::_GP('id')) {
            return (int)$id;
        }

        return 0;
    }

    public static function findCategory(int $startingPoint = null, array $rootLine = null): ?int
    {

        if (empty($startingPoint)) {
            $startingPoint = self::getCurrentPage();
        }

        if (empty($rootLine)) {
            $rootLine = $GLOBALS['TSFE'] instanceof TypoScriptFrontendController ? $GLOBALS['TSFE']->rootLine : GeneralUtility::makeInstance(RootlineUtility::class, $startingPoint)->get();
        }

        foreach ($rootLine ?? [] as $key => $row) {
            if ((int)$row['doktype'] === Category::DOKTYPE) {
                return (int)$row['uid'];
            }
        }

        return null;
    }

    public static function getRootPage(int $startingPoint = null): int
    {

        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController && $rootPage = $GLOBALS['TSFE']->domainStartPage) {
            return $rootPage;
        }

        if (TYPO3_MODE === 'BE') {
            foreach (GeneralUtility::makeInstance(BackendUtility::class)->BEgetRootLine($startingPoint ?: self::getCurrentPage()) ?: [] as $page) {
                if ($page['is_siteroot'] || (int)$page['pid'] === 0) {
                    return (int)$page['uid'];
                }
            }
        }

        return 0;
    }

    public static function findPagesBelow(int $startingPoint = null): array
    {
        return GeneralUtility::intExplode(',', GeneralUtility::makeInstance(QueryGenerator::class)->getTreeList($startingPoint ?: self::getCurrentPage(), 99));
    }
}
