<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Condition;

use Zeroseven\Z7Blog\Domain\Model\Post;

/**
 * This view helper will check if the current page is a blog post
 *
 * <blog:condition.isPost>
 *   The current page is a blog post
 * </blog:condition.isPost>
 *
 * <blog:condition.isPost negate="1">
 *   The current page is NOT a blog post
 * </blog:condition.isPost>
 *
 * <f:if condition="{blog:condition.isPost()} && {media}">
 *   The current page is a blog post with a media asset
 * </f:if>
 */
class IsPostViewHelper extends AbstractConditionViewHelper
{
    protected function validateCondition(): bool
    {
        return $this->getDoktype() === Post::DOKTYPE;
    }
}
