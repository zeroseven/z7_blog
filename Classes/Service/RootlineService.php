<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Zeroseven\Z7Blog\Domain\Model\Category;

class RootlineService
{
    protected static function getRequest(): ?ServerRequestInterface
    {
        return ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface ? $GLOBALS['TYPO3_REQUEST'] : null;
    }

    protected static function isFrontendMode(): bool
    {
        return ($request = self::getRequest()) && ApplicationType::fromRequest($request)->isFrontend();
    }

    protected static function isBackendMode(): bool
    {
        return !self::isFrontendMode();
    }

    protected static function getCurrentPage(): int
    {
        if (($GLOBALS['TSFE'] ?? null) instanceof TypoScriptFrontendController) {
            return (int)$GLOBALS['TSFE']->id;
        }

        if ($id = GeneralUtility::_GP('id')) {
            return (int)$id;
        }

        return 0;
    }

    protected static function getRootline(int $startingPoint = null): array
    {
        if (empty($startingPoint) && ($GLOBALS['TSFE'] ?? null) instanceof TypoScriptFrontendController && $rootLine = $GLOBALS['TSFE']->rootLine) {
            return $rootLine;
        }
        return GeneralUtility::makeInstance(RootlineUtility::class, $startingPoint ?: self::getCurrentPage())->get();
    }

    public static function findCategory(int $startingPoint = null, array $rootLine = null): ?int
    {
        if (empty($rootLine)) {
            $rootLine = self::getRootline($startingPoint);
        }

        foreach ($rootLine ?? [] as $row) {
            if (isset($row['doktype'], $row['uid']) && (int)$row['doktype'] === Category::DOKTYPE) {
                return (int)$row['uid'];
            }
        }

        return null;
    }

    public static function getRootPage(int $startingPoint = null): int
    {
        if (self::isFrontendMode()) {
            $site = $startingPoint === null && ($request = self::getRequest()) ? $request->getAttribute('site') : GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($startingPoint);

            return $site->getRootPageId();
        }

        if (self::isBackendMode()) {
            foreach (GeneralUtility::makeInstance(BackendUtility::class)->BEgetRootLine($startingPoint ?: self::getCurrentPage()) ?: [] as $page) {
                // todo: may throws undefined array key warning in php 8
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
