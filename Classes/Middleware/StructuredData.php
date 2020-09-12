<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\SettingsService;

class StructuredData implements MiddlewareInterface
{

    protected function collectArrays(...$arguments): array{
        return array_replace_recursive(...array_filter($arguments, static function($a){
            return is_array($a);
        }));
    }

    protected function parseStructuredData(array $array): array
    {

        // Create output
        $output = [];

        // Remove empty values
        $array = array_filter($array, static function ($v) {
            return !empty($v);
        });

        // Loop through array (recursive)
        foreach ($array as $key => $value) {
            if (preg_match('/^type((?:[A-Z][a-z]+)+)$/', $key, $matches)) {
                return $this->parseStructuredData(array_merge(['@type' => $matches[1]], $value));
            }

            $output[$key] = is_array($value) ? $this->parseStructuredData($value) : $value;
        }

        return $output;
    }

    protected function createImageObjectType(FileReference $media = null): ?array
    {
        if ($media) {
            $imageService = GeneralUtility::makeInstance(ImageService::class);
            $processedImage = $imageService->applyProcessingInstructions($media, [
                'width' => '1920m',
                'height' => '1080m'
            ]);

            // Get url of created source
            $url = $imageService->getImageUri($processedImage, true);

            // Add data of processed image
            if ($lastImageInfo = $GLOBALS['TSFE']->lastImageInfo) {
                return [
                    'url' => $url,
                    'width' => $lastImageInfo[0],
                    'height' => $lastImageInfo[1]
                ];
            }

            return ['url' => $url];
        }

        return null;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if (($tsfe = $GLOBALS['TSFE']) instanceof TypoScriptFrontendController && (int)$tsfe->page['doktype'] === Post::DOKTYPE && ($post = RepositoryService::getPostRepository()->findByUid($tsfe->id))) {

            // Define the basic structure of a post
            $basicStructure = [
                '@context' => 'http://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->getTitle(),
                'datePublished' => ($date = $post->getDate()) ? $date ->format('Y-m-d') : null,
                'dateModified' => ($lastChange = $post->getLastChange()) ? $lastChange ->format('Y-m-d') : null,
                'description' => $post->getAbstract() ?: $post->getDescription(),
            ];

            // Create author object
            $authorStructure = ($author = $post->getAuthor()) === null ? [] : [
                'author' => [
                    'typePerson' => [
                        'name' => trim($author->getFirstName() . ' ' . $author->getLastName())
                    ]
                ]
            ];

            // Create image object
            $imageStructure = ($image = $post->getFirstImage()) === null ? [] : [
                'image' => [
                    'typeImageObject' => $this->createImageObjectType($image)
                ]
            ];

            // Override by static typoScript definition
            $staticStructure = SettingsService::getSettings('post.structuredData');

            // Add data by the post model somehow
            $postStructure = method_exists($post, 'getStructuredData') ? $post->getStructuredData() : [];

            // Merge data
            $collectedData = $this->collectArrays($basicStructure, $authorStructure, $imageStructure, $staticStructure, $postStructure);

            // Add to the end of the page
            GeneralUtility::makeInstance(PageRenderer::class)->addFooterData('<script type="application/ld+json">' . json_encode($this->parseStructuredData($collectedData)) . '</script>');
        }

        return $handler->handle($request);
    }

}
