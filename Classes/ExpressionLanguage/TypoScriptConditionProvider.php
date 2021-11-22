<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\SettingsService;

/**
 * Example:
 *
 * page.10 = TEXT
 * page.10.value = It's a normal page.
 *
 * [z7_blog.isPost]
 * page.10.value = Nice! It's a blog post.
 * [global]
 */
class TypoScriptConditionProvider extends AbstractProvider
{

    public function __construct()
    {
        $doktype = (int)$GLOBALS['TSFE']->page['doktype'];

        $z7blog = new \stdClass();
        $z7blog->isPost = $doktype === Post::DOKTYPE;
        $z7blog->isCategory = $doktype === Category::DOKTYPE;

        $this->expressionLanguageVariables = [SettingsService::EXTENSION_KEY => $z7blog];
    }
}
