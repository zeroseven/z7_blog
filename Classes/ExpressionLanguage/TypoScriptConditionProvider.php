<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;
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
 * [z7_blog.post.getAuthor().getUid() == 77]
 * page.10.value = This post is written by John Doe.
 * [global]
 */
class TypoScriptConditionProvider extends AbstractProvider
{

    public function __construct()
    {
        if (!isset($GLOBALS['TSFE'])){
            return;
        }

        $doktype = (int)$GLOBALS['TSFE']->page['doktype'];
        $pagUid = (int)$GLOBALS['TSFE']->id;

        $z7blog = new \stdClass();
        $z7blog->post = $doktype === Post::DOKTYPE ? RepositoryService::getPostRepository()->findByUid($pagUid) : null;
        $z7blog->category = $doktype === Category::DOKTYPE ? RepositoryService::getCategoryRepository()->findByUid($pagUid) : null;

        $this->expressionLanguageVariables = [SettingsService::EXTENSION_KEY => $z7blog];
    }
}
