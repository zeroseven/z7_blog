<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Rss\Channel;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class LanguageViewHelper extends AbstractViewHelper
{
    public function render(): ?string
    {
        return $GLOBALS['TSFE']->config['config']['language'] ?? null;
    }

}
