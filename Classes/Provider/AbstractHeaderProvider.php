<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Provider;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Zeroseven\Z7Blog\Utility\GlobalUtility;

abstract class AbstractHeaderProvider
{

    /** @var int */
    protected $id = 0;

    /** @var array */
    protected $row = [];

    public function __construct()
    {
        // @extensionScannerIgnoreLine
        $this->id = GlobalUtility::getRequestParameter('id');

        // @extensionScannerIgnoreLine
        $this->row = BackendUtility::readPageAccess($this->id, true);
    }

    protected function createView(string $pathAndFilename, array $variables = null): StandaloneView
    {
        // FlashMessage::INFO deprecated in TYPO3 12
        // @extensionScannerIgnoreLine
        $state = 
        GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() == 11 ? FlashMessage::INFO : \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::INFO->value;

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($pathAndFilename));
        $view->assignMultiple(array_merge(['state' => $state], $variables ?: []));

        return $view;
    }
}
