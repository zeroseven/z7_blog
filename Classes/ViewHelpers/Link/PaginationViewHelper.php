<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Mvc\RequestInterface as ExtbaseRequestInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

class PaginationViewHelper extends AbstractLinkViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register arguments
        $this->registerArgument('ajaxPageType', 'int', 'Additional url for a "data-href" attribute.');
    }

    protected function getExtbaseRequest(): ExtbaseRequestInterface
    {
        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->renderingContext;
        $request = $renderingContext->getRequest();

        if ($request instanceof RequestInterface) {
            return $request;
        }

        if (($serverRequest = $GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface) {
            $bootstrapInitialization = GeneralUtility::makeInstance(Bootstrap::class)?->initialize([
                'extensionName' => 'Z7Blog',
                'pluginName' => 'List',
                'vendorName' => 'Zeroseven',
            ], $serverRequest);

            if (($request = GeneralUtility::makeInstance(RequestBuilder::class)?->build($bootstrapInitialization)) instanceof ExtbaseRequestInterface) {
                return $request;
            }
        }

        throw new RuntimeException('The request could not be created.', 1609450803);
    }

    protected function beforeRendering(): void
    {
        parent::beforeRendering();

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        // Add a "data-href" link attribute
        if (isset($this->arguments['ajaxPageType']) && ($pageType = (int)($this->arguments['ajaxPageType'])) && $this->demand->getListId()) {
            $this->tag->addAttribute('data-href', $uriBuilder->reset()
                ->setRequest($this->getExtbaseRequest())
                ->setTargetPageType($pageType)
                ->setCreateAbsoluteUri(true)
                ->setArguments((array)($this->arguments['arguments'] ?? []))
                ->setAddQueryString((bool)($this->arguments['addQueryString'] ?? false))
                ->setArguments((array)($this->arguments['additionalParams'] ?? []))
                ->uriFor($this->arguments['action'] ?? '', array_merge((array)($this->arguments['arguments'] ?? []), [
                    'ajax' => 1
                ]), $this->arguments['controller'] ?? null, $this->arguments['extensionName'] ?? null, $this->arguments['pluginName'] ?? null));
        }
    }
}
