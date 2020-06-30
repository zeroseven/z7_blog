<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Utility\InfoRenderUtility;

class InfoViewHelper extends AbstractViewHelper
{

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('postUid', 'int', 'The uid of the post');
        $this->registerArgument('file', 'string', 'The template file');
    }


    public function render(): string
    {

        $post = ($postUid = (int)$this->arguments['postUid']) ? RepositoryService::getPostRepository()->findByUid($postUid) : null;

        return GeneralUtility::makeInstance(InfoRenderUtility::class)->render($this->arguments['file'], $post);
    }
}
