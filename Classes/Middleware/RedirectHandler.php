<?php declare(strict_types=1);

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
use Zeroseven\Z7Blog\Service\RequestService;
use Zeroseven\Z7Blog\Service\SettingsService;

class RedirectHandler implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController && ($row = $GLOBALS['TSFE']->page) && $row['post_redirect_category'] && (int)$row['doktype'] === Category::DOKTYPE) {

            // Get target page uid of plugin settings
            $targetUid = (int)SettingsService::getSettings('post.list.defaultListPage');

            // Return redirect response
            if ($targetUid !== (int)$row['uid']) {
                return $this->buildRedirectResponse([
                    'parameter' => $targetUid ?: (int)$row['pid'],
                    'forceAbsoluteUrl' => true,
                    'useCacheHash' => true,
                    'additionalParams' => '&' . RequestService::REQUEST_KEY . '[category]=' . $row['uid'],
                    'addQueryString' => true,
                    'addQueryString.' => [
                        'exclude' => RequestService::REQUEST_KEY . '[list_id],' . RequestService::REQUEST_KEY . '[category], cHash, id'
                    ]
                ]);
            }
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
