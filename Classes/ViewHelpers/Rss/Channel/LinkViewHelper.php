<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Rss\Channel;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Zeroseven\Z7Blog\Service\RootlineService;

class LinkViewHelper extends AbstractViewHelper
{
    public function render(): string
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(UriBuilder::class)
            ->reset()
            ->setTargetPageUid(RootlineService::getRootPage())
            ->setCreateAbsoluteUri(true)
            ->build();
    }

}
