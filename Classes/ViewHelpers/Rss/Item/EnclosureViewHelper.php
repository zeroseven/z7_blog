<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Rss\Item;

use TYPO3\CMS\Fluid\ViewHelpers\Uri\ImageViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class EnclosureViewHelper extends ImageViewHelper
{

    protected $escapeOutput = false;

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {

        // Abort, if no image is given
        if (empty($arguments['src']) && $arguments['image'] === null) {
            return '';
        }

        // Get the url
        $url = parent::renderStatic(array_merge($arguments, ['absolute' => true]), $renderChildrenClosure, $renderingContext);

        // Add data of processed image
        if (($lastImageInfo = $GLOBALS['TSFE']->lastImageInfo) && $processedImage = $lastImageInfo['processedFile'] ?? $lastImageInfo['originalFile']) {
            $length = $processedImage->getSize();
            $type = $processedImage->getMimeType();

            return sprintf('<enclosure url="%s" length="%s" type="%s" />', $url, $length, $type);
        }

        // Return url only
        return sprintf('<enclosure url="%s" />', $url);

    }

}
