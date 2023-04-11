<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\SettingsService;
/**
 * Example:
 *
 * page.10 = TEXT
 * page.10.value = It's a normal page.
 *
 * [z7_blog.post]
 * page.10.value = Nice! It's a blog post.
 * [global]
 *
 * [z7_blog.category]
 * page.10.value = Nice! It's a blog category.
 * [global]
 */
class TypoScriptConditionProvider extends AbstractProvider
{
    public function __construct()
    {
        if (($GLOBALS['TSFE'] ?? null) instanceof TypoScriptFrontendController){
            
            $doktype = (int)($GLOBALS['TSFE']->page['doktype'] ?? 0);

            $z7blog = new \stdClass;
            $z7blog->post = $doktype === Post::DOKTYPE;
            $z7blog->category = $doktype === Category::DOKTYPE;

            $this->expressionLanguageVariables = [SettingsService::EXTENSION_KEY => $z7blog];
        }
    }
}
