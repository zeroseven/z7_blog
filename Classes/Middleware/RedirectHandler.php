<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Service\ArgumentsService;
use Zeroseven\Z7Blog\Service\SettingsService;

class RedirectHandler implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController && ($row = $GLOBALS['TSFE']->page) && $row['post_redirect_category'] && (int)$row['doktype'] === Category::DOKTYPE) {

            // Return redirect response
            return $this->buildRedirectResponse([
                'parameter' => (int)SettingsService::getKey('list.defaultPid'),
                'useCacheHash' => true,
                'additionalParams' => '&' . ArgumentsService::REQUEST_KEY . '[category]=' . $row['uid'] . (($type = (int)GeneralUtility::_GET('type')) ? '&type=' . $type : '')
            ]);
        }

        return $handler->handle($request);
    }

    protected function buildRedirectResponse(array $typolinkConfiguration, int $statusCode = null): ResponseInterface
    {
        $typolink = GeneralUtility::makeInstance(ContentObjectRenderer::class)->typoLink_URL($typolinkConfiguration);
        $url = GeneralUtility::makeInstance(Uri::class, $typolink);

        return GeneralUtility::makeInstance(RedirectResponse::class, $url, $statusCode ?: 307, ['X-Redirect-By' => 'TYPO3 Redirect: z7_blog']);
    }

}