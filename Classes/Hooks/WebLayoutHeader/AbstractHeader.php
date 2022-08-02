<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\WebLayoutHeader;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

abstract class AbstractHeader
{

    /** @var int */
    protected $id = 0;

    /** @var array */
    protected $row = [];

    public function __construct()
    {
        $this->id = (int)GeneralUtility::_GP('id');
        $this->row = BackendUtility::readPageAccess($this->id, true);
    }

    protected function createView(string $pathAndFilename, array $variables = null): StandaloneView
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($pathAndFilename));
        $view->assignMultiple(array_merge(['state' => AbstractMessage::INFO], $variables ?: []));

        return $view;
    }
}
