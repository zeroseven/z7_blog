<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

class PaginationViewHelper extends AbstractLinkViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register arguments
        $this->registerArgument('ajaxPageType', 'int', 'Additional url for a "data-href" attribute.');
    }

    protected function beforeRendering(): void
    {
        parent::beforeRendering();

        // Add a "data-href" link attribute
        if (isset($this->arguments['ajaxPageType']) && ($pageType = (int)($this->arguments['ajaxPageType'])) && $this->demand->getListId()) {
            $this->tag->addAttribute('data-href', $this->renderingContext->getControllerContext()->getUriBuilder()->reset()
                ->setTargetPageType($pageType)
                ->setCreateAbsoluteUri(true)
                ->setArguments((array)($this->arguments['arguments'] ?? []))
                ->setAddQueryString((bool)($this->arguments['addQueryString'] ?? false))
                ->setArguments((array)($this->arguments['additionalParams'] ?? []))
                ->uriFor($this->arguments['action'] ?? '', array_merge((array)($this->arguments['arguments'] ?? []), [
                    'ajax' => 1
                ]), $this->arguments['controller'] ?? '', $this->arguments['extensionName'] ?? '', $this->arguments['pluginName'] ?? ''));
        }
    }
}
