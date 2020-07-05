<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Zeroseven\Z7Blog\Domain\Model\Category;

class RootlineService
{

    public static function findCategory(int $startingPoint = null, array $rootLine = null): ?int
    {

        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController && $GLOBALS['TSFE']->id) {
            $startingPoint = (int)($startingPoint ?: $GLOBALS['TSFE']->id);
            $rootLine = $rootLine === null && $startingPoint === (int)$GLOBALS['TSFE']->id ? $GLOBALS['TSFE']->rootLine : null;
        } else {
            $startingPoint = (int)($startingPoint ?: GeneralUtility::_GP('id'));
        }

        if ($rootLine === null) {
            $rootLine = $rootLine ?: GeneralUtility::makeInstance(RootlineUtility::class, $startingPoint)->get();
        }

        foreach ($rootLine as $key => $row) {
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
            foreach (GeneralUtility::makeInstance(BackendUtility::class)->BEgetRootLine($startingPoint ?: (int)GeneralUtility::_GP('id')) ?: [] as $page) {
                if ($page['is_siteroot'] || (int)$page['pid'] === 0) {
                    return (int)$page['uid'];
                }
            }
        }

        return 0;
    }
}
