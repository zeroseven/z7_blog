<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\DrawHeaderHook;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

abstract class AbstractHeaderHook
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

        $editLink = GeneralUtility::makeInstance(UriBuilder::class)->buildUriFromRoute(
            'record_edit',
            [
                'edit[pages][' . $this->id . ']' => 'edit',
                'noView' => 1,
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
            ]
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($pathAndFilename));
        $view->assignMultiple(array_merge([
            'editLink' => $editLink,
            'state' => FlashMessage::INFO
        ], $variables ?: []));

        return $view;
    }
}
