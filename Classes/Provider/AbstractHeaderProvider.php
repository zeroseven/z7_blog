<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Provider;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper;
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
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($pathAndFilename));
        $view->assignMultiple(array_merge(['state' => InfoboxViewHelper::STATE_INFO], $variables ?: []));

        return $view;
    }
}
